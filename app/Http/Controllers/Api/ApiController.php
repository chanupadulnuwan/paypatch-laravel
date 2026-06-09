<?php

namespace App\Http\Controllers\Api;

use App\Events\ExpenseCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Resources\GroupResource;
use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\ExpenseShare;
use App\Models\Group;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ApiController extends Controller
{
    private const SUPPORTED_CURRENCIES = ['LKR', 'USD', 'EUR', 'GBP', 'AUD', 'JPY'];

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        $login = trim($request->email);

        // Allow login with email or username
        $user = User::where('email', strtolower($login))
            ->orWhere('username', $login)
            ->first();

        if (!$user || !password_verify($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $token = $user->createToken('api-token', ['read', 'write']);

        return response()->json([
            'token' => $token->plainTextToken,
            'user'  => $this->serializeAuthUser($user),
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'nullable|string|max:50|alpha_dash|unique:users,username',
            'email'    => 'required|string|email|max:255|unique:users',
            'phone'    => 'nullable|string|max:30',
            'password' => 'required|string|min:8',
            'country'  => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name'         => $request->name,
            'username'     => $request->username,
            'email'        => $request->email,
            'phone'        => $request->phone ? preg_replace('/[^+\d]/', '', $request->phone) : null,
            'country'      => $request->country,
            'password'     => $request->password,
            'role'         => 'user',
            'status'       => 'active',
            'account_type' => 'free',
        ]);

        $token = $user->createToken('api-token', ['read', 'write']);

        return response()->json([
            'token' => $token->plainTextToken,
            'user'  => $this->serializeAuthUser($user),
        ], 201);
    }

    public function getProfile(Request $request)
    {
        return response()->json(['user' => $this->serializeAuthUser($request->user())]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name'          => 'sometimes|string|max:255',
            'username'      => 'sometimes|nullable|string|max:50|alpha_dash|unique:users,username,' . $user->id,
            'phone'         => 'sometimes|nullable|string|max:30',
            'profile_photo' => 'sometimes|nullable|image|max:4096',
        ]);

        if ($request->has('name'))     $user->name     = $request->name;
        if ($request->has('username')) $user->username = $request->username;
        if ($request->has('phone'))    $user->phone    = $request->phone ? preg_replace('/[^+\d]/', '', $request->phone) : null;

        if ($request->hasFile('profile_photo')) {
            $this->deleteUploadedAsset($user->profile_photo_path);
            $newPath = $this->storeUploadedImage($request, 'profile_photo', 'profile-photos', 'user_photo');
            if ($newPath) {
                $user->profile_photo_path = $newPath;
            }
        }

        $user->save();

        return response()->json(['user' => $this->serializeAuthUser($user)]);
    }

    public function matchByPhone(Request $request)
    {
        $request->validate([
            'phones'   => 'required|array|max:500',
            'phones.*' => 'string|max:30',
        ]);

        $normalized = array_map(fn ($p) => preg_replace('/[^+\d]/', '', $p), $request->phones);
        $normalized = array_filter($normalized, fn ($p) => strlen($p) >= 7);

        $users = User::whereIn(
            DB::raw("REGEXP_REPLACE(phone, '[^+0-9]', '')"),
            array_values($normalized)
        )
            ->where('id', '!=', $request->user()->id)
            ->get(['id', 'name', 'username', 'email', 'phone', 'profile_photo_path']);

        $matched = [];
        foreach ($request->phones as $original) {
            $norm = preg_replace('/[^+\d]/', '', $original);
            foreach ($users as $u) {
                $uNorm = preg_replace('/[^+\d]/', '', $u->phone ?? '');
                if ($norm === $uNorm && strlen($norm) >= 7) {
                    $matched[$original] = [
                        'id'                => $u->id,
                        'name'              => $u->name,
                        'username'          => $u->username,
                        'email'             => $u->email,
                        'phone'             => $u->phone,
                        'profile_photo_url' => $u->profile_photo_url,
                    ];
                    break;
                }
            }
        }

        return response()->json(['matched' => $matched]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function usdLkrRate()
    {
        return response()->json([
            'base'       => 'USD',
            'target'     => 'LKR',
            'rate'       => $this->getUsdLkrRate(),
            'fetched_at' => now()->toIso8601String(),
        ]);
    }

    public function searchUsers(Request $request)
    {
        $request->validate([
            'q'        => 'required|string|min:1|max:255',
            'group_id' => 'nullable|integer|exists:groups,id',
        ]);

        $query = trim($request->input('q', ''));
        $groupId = $request->integer('group_id');

        $usersQuery = User::query()
            ->where('id', '!=', $request->user()->id)
            ->where(function ($builder) use ($query) {
                $builder
                    ->where('name', 'like', '%' . $query . '%')
                    ->orWhere('email', 'like', '%' . $query . '%');
            });

        if ($groupId) {
            $group = Group::findOrFail($groupId);
            if (!$this->isGroupMember($group, $request->user()->id)) {
                return response()->json(['message' => 'You are not a member of this group.'], 403);
            }

            $existingMemberIds = $group->members()->pluck('users.id')->all();
            $usersQuery->whereNotIn('id', $existingMemberIds);
        }

        $users = $usersQuery
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json([
            'users' => $users->map(fn (User $user) => $this->serializeUser($user, $request->user()->id)),
        ]);
    }

    public function groups(Request $request)
    {
        $userId = $request->user()->id;

        $groups = Group::forUser($userId)
            ->withCount('members')
            ->with(['expenses.shares.user', 'settlements', 'creator'])
            ->latest()
            ->paginate(15);

        $groups->getCollection()->transform(function (Group $group) use ($userId) {
            return $this->prepareGroup($group, $userId);
        });

        return GroupResource::collection($groups);
    }

    public function storeGroup(Request $request)
    {
        $memberIds = $this->normalizeMemberIds($request);
        $request->merge(['member_ids' => $memberIds]);

        $request->validate([
            'name'          => 'required|string|max:255',
            'currency'      => ['nullable', 'string', Rule::in(self::SUPPORTED_CURRENCIES)],
            'member_ids'    => 'nullable|array',
            'member_ids.*'  => 'integer|exists:users,id',
            'cover_image'   => 'nullable|image|max:4096',
            'profile_image' => 'nullable|image|max:4096',
        ]);

        $uniqueMemberIds = collect($memberIds)
            ->push($request->user()->id)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $maxMembers = (int) Setting::getValue('max_group_members', 10);
        if ($uniqueMemberIds->count() > $maxMembers) {
            return response()->json([
                'message' => "This group can have at most {$maxMembers} members.",
            ], 422);
        }

        $group = DB::transaction(function () use ($request, $uniqueMemberIds) {
            $group = Group::create([
                'name'       => $request->name,
                'created_by' => $request->user()->id,
                'currency'   => $request->input('currency', 'LKR'),
            ]);

            if ($coverPath = $this->storeUploadedImage($request, 'cover_image', 'covers', 'group_cover')) {
                $group->cover_image_path = $coverPath;
            }

            if ($profilePath = $this->storeUploadedImage($request, 'profile_image', 'group-profiles', 'group_profile')) {
                $group->profile_image_path = $profilePath;
            }

            $group->save();

            $attachPayload = [];
            foreach ($uniqueMemberIds as $memberId) {
                $attachPayload[$memberId] = ['joined_at' => now()];
            }
            $group->members()->attach($attachPayload);

            ActivityLog::create([
                'group_id' => $group->id,
                'user_id'  => $request->user()->id,
                'message'  => $request->user()->name . ' created the group "' . $group->name . '"',
                'type'     => 'group',
            ]);

            $group->load('members');
            $group->forgetMembersCache();

            return $group;
        });

        $group->load(['creator', 'expenses.shares', 'settlements']);
        $group->loadCount('members');
        $group = $this->prepareGroup($group, $request->user()->id);

        return response()->json([
            'message' => 'Group created successfully.',
            'group'   => new GroupResource($group),
        ], 201);
    }

    public function showGroup(Request $request, Group $group)
    {
        if (!$this->isGroupMember($group, $request->user()->id)) {
            return response()->json(['message' => 'Not a member of this group.'], 403);
        }

        $group->load([
            'members',
            'expenses.shares.user',
            'expenses.paidBy',
            'expenses.createdBy',
            'settlements',
            'creator',
        ]);
        $group->loadCount('members');

        $group = $this->prepareGroup($group, $request->user()->id);

        $members = $group->members
            ->sortBy('name')
            ->values()
            ->map(fn (User $member) => $this->serializeUser($member, $request->user()->id, $group));

        return response()->json([
            'group'   => new GroupResource($group),
            'members' => $members,
            'meta'    => [
                'usd_lkr_rate' => $this->getUsdLkrRate(),
            ],
        ]);
    }

    public function updateGroup(Request $request, Group $group)
    {
        if ($group->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Only the group owner can update this group.'], 403);
        }

        $request->validate([
            'name'          => 'required|string|max:255',
            'currency'      => ['required', 'string', Rule::in(self::SUPPORTED_CURRENCIES)],
            'cover_image'   => 'nullable|image|max:4096',
            'profile_image' => 'nullable|image|max:4096',
        ]);

        $group->update([
            'name'     => $request->name,
            'currency' => $request->currency,
        ]);

        if ($request->hasFile('cover_image')) {
            $this->deleteUploadedAsset($group->cover_image_path);
            $group->cover_image_path = $this->storeUploadedImage($request, 'cover_image', 'covers', 'group_cover');
        }

        if ($request->hasFile('profile_image')) {
            $this->deleteUploadedAsset($group->profile_image_path);
            $group->profile_image_path = $this->storeUploadedImage($request, 'profile_image', 'group-profiles', 'group_profile');
        }

        $group->save();

        ActivityLog::create([
            'group_id' => $group->id,
            'user_id'  => $request->user()->id,
            'message'  => $request->user()->name . ' updated the group settings for "' . $group->name . '"',
            'type'     => 'group',
        ]);

        $group->load(['creator', 'expenses.shares', 'expenses.paidBy', 'expenses.createdBy', 'settlements', 'members']);
        $group->loadCount('members');
        $group = $this->prepareGroup($group, $request->user()->id);
        $group->forgetMembersCache();

        return response()->json([
            'message' => 'Group updated successfully.',
            'group'   => new GroupResource($group),
        ]);
    }

    public function destroyGroup(Request $request, Group $group)
    {
        if ($group->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Only the group owner can delete this group.'], 403);
        }

        $group->load('members');
        $group->forgetMembersCache();
        $this->deleteUploadedAsset($group->cover_image_path);
        $this->deleteUploadedAsset($group->profile_image_path);
        $group->delete();

        return response()->json(['message' => 'Group deleted successfully.']);
    }

    public function addGroupMember(Request $request, Group $group)
    {
        if ($group->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Only the group owner can add members.'], 403);
        }

        $request->validate([
            'member_id' => 'required|integer|exists:users,id',
        ]);

        $memberId = (int) $request->member_id;
        if ($group->members()->where('users.id', $memberId)->exists()) {
            return response()->json(['message' => 'That user is already in this group.'], 422);
        }

        $maxMembers = (int) Setting::getValue('max_group_members', 10);
        if ($group->members()->count() >= $maxMembers) {
            return response()->json([
                'message' => "This group can have at most {$maxMembers} members.",
            ], 422);
        }

        $group->members()->attach($memberId, ['joined_at' => now()]);
        $group->forgetMembersCache();

        $member = User::findOrFail($memberId);
        ActivityLog::create([
            'group_id' => $group->id,
            'user_id'  => $request->user()->id,
            'message'  => $member->name . ' was added to the group.',
            'type'     => 'group',
        ]);

        return response()->json([
            'message' => $member->name . ' added to the group.',
            'member'  => $this->serializeUser($member, $request->user()->id, $group),
        ], 201);
    }

    public function removeGroupMember(Request $request, Group $group, User $user)
    {
        if ($group->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Only the group owner can remove members.'], 403);
        }

        if ($user->id === $group->created_by) {
            return response()->json(['message' => 'You cannot remove the group owner.'], 422);
        }

        if (!$group->members()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'That user is not a member of this group.'], 404);
        }

        $group->members()->detach($user->id);
        $group->forgetMembersCache();
        Cache::forget("dashboard_groups_{$user->id}");

        ActivityLog::create([
            'group_id' => $group->id,
            'user_id'  => $request->user()->id,
            'message'  => $user->name . ' was removed from the group.',
            'type'     => 'group',
        ]);

        return response()->json(['message' => 'Member removed successfully.']);
    }

    public function storeExpense(StoreExpenseRequest $request)
    {
        if (!$request->user()->tokenCan('write')) {
            return response()->json(['message' => 'Token does not have write permission.'], 403);
        }

        $group = Group::with('members')->findOrFail($request->group_id);

        if (!$group->members->contains('id', (int) $request->paid_by)) {
            return response()->json(['message' => 'The selected payer is not a member of this group.'], 422);
        }

        $receiptPath = $this->storeUploadedImage($request, 'receipt_image', 'receipts', 'receipt');

        $expense = DB::transaction(function () use ($request, $group, $receiptPath) {
            $splitMemberIds = $request->input('split_member_ids');
            $isCustomSplit  = !empty($splitMemberIds) && is_array($splitMemberIds);

            $expense = Expense::create([
                'group_id'           => $group->id,
                'paid_by'            => $request->paid_by,
                'created_by'         => $request->user()->id,
                'title'              => $request->title,
                'amount'             => $request->amount,
                'split_type'         => $isCustomSplit ? 'custom' : 'equal',
                'receipt_image_path' => $receiptPath,
                'location'           => $request->input('location'),
            ]);

            if ($isCustomSplit) {
                $splitMembers = $group->members->whereIn('id', $splitMemberIds);
                $count        = $splitMembers->count() ?: 1;
            } else {
                $splitMembers = $group->members;
                $count        = $splitMembers->count();
            }

            $shareAmount = floor(((float) $request->amount / $count) * 100) / 100;
            $remainder   = round((float) $request->amount - ($shareAmount * $count), 2);

            foreach ($splitMembers->values() as $index => $member) {
                \App\Models\ExpenseShare::create([
                    'expense_id'   => $expense->id,
                    'user_id'      => $member->id,
                    'share_amount' => $index === 0 ? $shareAmount + $remainder : $shareAmount,
                ]);
            }

            ExpenseCreated::dispatch($expense, $request->user());
            $group->forgetMembersCache();

            return $expense;
        });

        $expense->load(['paidBy', 'createdBy', 'group']);

        return response()->json([
            'message' => 'Expense created.',
            'expense' => [
                'id'                => $expense->id,
                'title'             => $expense->title,
                'amount'            => (double) $expense->amount,
                'paid_by'           => $expense->paid_by,
                'paid_by_name'      => $expense->paidBy->name ?? 'User',
                'created_by_id'     => $expense->created_by,
                'created_by_name'   => $expense->createdBy->name ?? 'User',
                'can_delete'        => $expense->created_by === $request->user()->id,
                'receipt_image_url' => $this->resolveAssetUrl($expense->receipt_image_path),
                'created_at'        => optional($expense->created_at)->toDateTimeString(),
            ],
        ], 201);
    }

    public function destroyExpense(Request $request, Expense $expense)
    {
        $group = $expense->group()->firstOrFail();

        if (!$this->isGroupMember($group, $request->user()->id)) {
            return response()->json(['message' => 'You are not a member of this group.'], 403);
        }

        if ($expense->created_by !== $request->user()->id) {
            return response()->json([
                'message' => 'Only the person who added this expense can delete it.',
            ], 403);
        }

        $group->load('members');
        $group->forgetMembersCache();
        $this->deleteUploadedAsset($expense->receipt_image_path);
        $expense->delete();

        return response()->json(['message' => 'Expense deleted successfully.']);
    }

    public function inviteFriend(Request $request)
    {
        $request->validate(['user_id' => 'required|integer|exists:users,id']);

        $senderId    = $request->user()->id;
        $recipientId = (int) $request->user_id;

        if ($senderId === $recipientId) {
            return response()->json(['message' => 'You cannot add yourself.'], 400);
        }

        $existing = \App\Models\Friendship::where(function ($q) use ($senderId, $recipientId) {
            $q->where('user_id', $senderId)->where('friend_id', $recipientId);
        })->orWhere(function ($q) use ($senderId, $recipientId) {
            $q->where('user_id', $recipientId)->where('friend_id', $senderId);
        })->first();

        if ($existing) {
            $msg = match ($existing->status) {
                'accepted' => 'You are already friends.',
                'pending'  => 'A friend request is already pending.',
                default    => 'A request already exists.',
            };
            return response()->json(['message' => $msg], 400);
        }

        $friendship = \App\Models\Friendship::create([
            'user_id'   => $senderId,
            'friend_id' => $recipientId,
            'status'    => 'pending',
        ]);

        ActivityLog::create([
            'user_id'            => $recipientId,
            'type'               => 'friend_request',
            'message'            => $request->user()->name . ' sent you a friend request.',
            'request_to_user_id' => $senderId,
        ]);

        return response()->json([
            'message'       => 'Friend request sent.',
            'friendship_id' => $friendship->id,
        ], 201);
    }

    public function acceptFriendRequest(Request $request, $id)
    {
        $userId     = $request->user()->id;
        $friendship = \App\Models\Friendship::where('id', $id)
            ->where('friend_id', $userId)
            ->where('status', 'pending')
            ->first();

        if (!$friendship) {
            return response()->json(['message' => 'Friend request not found.'], 404);
        }

        $friendship->update(['status' => 'accepted']);

        ActivityLog::create([
            'user_id'            => $friendship->user_id,
            'type'               => 'friend_accepted',
            'message'            => $request->user()->name . ' accepted your friend request.',
            'request_to_user_id' => $userId,
        ]);

        return response()->json(['message' => 'Friend request accepted.']);
    }

    public function declineFriendRequest(Request $request, $id)
    {
        $userId     = $request->user()->id;
        $friendship = \App\Models\Friendship::where('id', $id)
            ->where('friend_id', $userId)
            ->where('status', 'pending')
            ->first();

        if (!$friendship) {
            return response()->json(['message' => 'Friend request not found.'], 404);
        }

        $friendship->update(['status' => 'declined']);

        return response()->json(['message' => 'Friend request declined.']);
    }

    public function friends(Request $request)
    {
        $userId = $request->user()->id;

        $groups = Group::forUser($userId)
            ->with(['expenses.shares', 'settlements'])
            ->get();

        $balanceMap = [];

        foreach ($groups as $group) {
            foreach ($group->expenses as $expense) {
                foreach ($expense->shares as $share) {
                    $payerId     = $expense->paid_by;
                    $shareUserId = $share->user_id;

                    if ($payerId === $userId && $shareUserId !== $userId) {
                        $balanceMap[$shareUserId] = ($balanceMap[$shareUserId] ?? 0) + $share->share_amount;
                    } elseif ($shareUserId === $userId && $payerId !== $userId) {
                        $balanceMap[$payerId] = ($balanceMap[$payerId] ?? 0) - $share->share_amount;
                    }
                }
            }

            foreach ($group->settlements as $settlement) {
                if ($settlement->from_user_id === $userId) {
                    $balanceMap[$settlement->to_user_id] = ($balanceMap[$settlement->to_user_id] ?? 0) + $settlement->amount;
                } elseif ($settlement->to_user_id === $userId) {
                    $balanceMap[$settlement->from_user_id] = ($balanceMap[$settlement->from_user_id] ?? 0) - $settlement->amount;
                }
            }
        }

        // Build per-friend currency map (use first group's currency as dominant)
        $currencyMap = [];
        foreach ($groups as $group) {
            foreach ($group->members as $member) {
                if ($member->id !== $userId && !isset($currencyMap[$member->id])) {
                    $currencyMap[$member->id] = $group->currency ?? 'LKR';
                }
            }
        }

        // Also include direct friends from accepted friendship requests
        $directFriendIds = \App\Models\Friendship::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)->orWhere('friend_id', $userId);
        })->where('status', 'accepted')->get()->map(function ($f) use ($userId) {
            return $f->user_id === $userId ? $f->friend_id : $f->user_id;
        });

        foreach ($directFriendIds as $fid) {
            if (!isset($balanceMap[$fid])) {
                $balanceMap[$fid] = 0;
            }
        }

        $friends = collect($balanceMap)->map(function ($balance, $id) use ($currencyMap) {
            $user = User::find($id);
            $photoUrl = null;
            if ($user && $user->profile_photo_path && !str_starts_with($user->profile_photo_path, 'preset:')) {
                $photoUrl = url($user->profile_photo_path);
            }

            return $user ? [
                'user_id'           => $user->id,
                'name'              => $user->name,
                'username'          => $user->username,
                'email'             => $user->email,
                'profile_photo_url' => $photoUrl,
                'balance'           => round($balance, 2),
                'currency'          => $currencyMap[$id] ?? 'LKR',
                'status'            => $balance > 0 ? 'owes_you' : ($balance < 0 ? 'you_owe' : 'settled'),
            ] : null;
        })->filter()->values();

        return response()->json(['friends' => $friends]);
    }

    public function getActivity(Request $request)
    {
        $userId = $request->user()->id;

        $logs = ActivityLog::where('user_id', $userId)
            ->with('group')
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        // Mark all fetched logs as read
        ActivityLog::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $activity = $logs->map(function ($log) {
            $data = [
                'id'         => $log->id,
                'message'    => $log->message,
                'type'       => $log->type,
                'group_name' => $log->group?->name,
                'group_id'   => $log->group_id,
                'created_at' => optional($log->created_at)->toDateTimeString(),
            ];

            if ($log->type === 'friend_request' && $log->request_to_user_id) {
                $friendship = \App\Models\Friendship::where('user_id', $log->request_to_user_id)
                    ->where('friend_id', $log->user_id)
                    ->first();
                $sender   = User::find($log->request_to_user_id);
                $photoUrl = null;
                if ($sender && $sender->profile_photo_path && !str_starts_with($sender->profile_photo_path, 'preset:')) {
                    $photoUrl = url($sender->profile_photo_path);
                }
                $data['friendship_id']     = $friendship?->id;
                $data['friendship_status'] = $friendship?->status;
                $data['from_user_id']      = $log->request_to_user_id;
                $data['from_user_name']    = $sender?->name;
                $data['from_user_photo']   = $photoUrl;
            }

            return $data;
        });

        return response()->json(['activity' => $activity]);
    }

    public function getUnreadCount(Request $request)
    {
        $count = ActivityLog::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!password_verify($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $user->password = $request->new_password;
        $user->save();

        return response()->json(['message' => 'Password changed successfully.']);
    }

    public function storeSettlement(Request $request, Group $group)
    {
        if (!$this->isGroupMember($group, $request->user()->id)) {
            return response()->json(['message' => 'Not a member of this group.'], 403);
        }

        $request->validate([
            'from_user_id' => 'required|integer|exists:users,id',
            'to_user_id'   => 'required|integer|exists:users,id',
            'amount'       => 'required|numeric|min:0.01',
        ]);

        \App\Models\Settlement::create([
            'group_id'     => $group->id,
            'from_user_id' => (int) $request->from_user_id,
            'to_user_id'   => (int) $request->to_user_id,
            'amount'       => $request->amount,
        ]);

        $fromUser   = User::find($request->from_user_id);
        $toUser     = User::find($request->to_user_id);
        $currentId  = $request->user()->id;
        $otherUserId = $currentId === (int)$request->from_user_id
            ? (int)$request->to_user_id
            : (int)$request->from_user_id;

        $settlementMsg = ($fromUser->name ?? 'User') . ' settled up with ' . ($toUser->name ?? 'User') . ' in "' . $group->name . '".';

        // Log for current user
        ActivityLog::create([
            'group_id' => $group->id,
            'user_id'  => $currentId,
            'message'  => $settlementMsg,
            'type'     => 'settlement',
        ]);

        // Also notify the other party
        if ($otherUserId && $otherUserId !== $currentId) {
            ActivityLog::create([
                'group_id' => $group->id,
                'user_id'  => $otherUserId,
                'message'  => $settlementMsg,
                'type'     => 'settlement',
            ]);
        }

        return response()->json(['message' => 'Settlement recorded.'], 201);
    }

    public function sendReminder(Request $request, Group $group)
    {
        if (!$this->isGroupMember($group, $request->user()->id)) {
            return response()->json(['message' => 'Not a member of this group.'], 403);
        }

        $request->validate([
            'member_ids'   => 'required|array|min:1',
            'member_ids.*' => 'integer|exists:users,id',
        ]);

        foreach ($request->member_ids as $memberId) {
            ActivityLog::create([
                'group_id' => $group->id,
                'user_id'  => (int) $memberId,
                'message'  => $request->user()->name . ' is reminding you to settle up in "' . $group->name . '".',
                'type'     => 'reminder',
            ]);
        }

        return response()->json(['message' => 'Reminders sent.']);
    }

    public function leaveGroup(Request $request, Group $group)
    {
        $userId = $request->user()->id;

        if (!$this->isGroupMember($group, $userId)) {
            return response()->json(['message' => 'Not a member of this group.'], 403);
        }

        if ($group->created_by === $userId) {
            return response()->json(['message' => 'Group owner cannot leave the group.'], 403);
        }

        $group->members()->detach($userId);
        Cache::forget("dashboard_groups_{$userId}");

        ActivityLog::create([
            'group_id' => $group->id,
            'user_id'  => $userId,
            'message'  => $request->user()->name . ' left the group "' . $group->name . '".',
            'type'     => 'group',
        ]);

        return response()->json(['message' => 'Left group successfully.']);
    }

    private function prepareGroup(Group $group, int $userId): Group
    {
        $group->your_balance = $this->calculateBalance($group, $userId);
        $group->total_expenses = round((float) $group->expenses->sum('amount'), 2);

        if ($group->relationLoaded('expenses')) {
            $group->setRelation(
                'expenses',
                $group->expenses->sortByDesc('created_at')->values()
            );
        }

        return $group;
    }

    private function calculateBalance(Group $group, int $userId): float
    {
        $paid     = $group->expenses->where('paid_by', $userId)->sum('amount');
        $share    = $group->expenses->flatMap->shares->where('user_id', $userId)->sum('share_amount');
        $sent     = $group->settlements->where('from_user_id', $userId)->sum('amount');
        $received = $group->settlements->where('to_user_id', $userId)->sum('amount');

        return round($paid - $share + $sent - $received, 2);
    }

    private function normalizeMemberIds(Request $request): array
    {
        $memberIds = $request->input('member_ids', []);

        if (is_string($memberIds)) {
            $decoded = json_decode($memberIds, true);
            $memberIds = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($memberIds)) {
            return [];
        }

        return collect($memberIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function serializeAuthUser(User $user): array
    {
        $photoUrl = null;
        if ($user->profile_photo_path && !str_starts_with($user->profile_photo_path, 'preset:')) {
            $photoUrl = url($user->profile_photo_path);
        }

        return [
            'id'                => $user->id,
            'name'              => $user->name,
            'username'          => $user->username,
            'email'             => $user->email,
            'phone'             => $user->phone,
            'country'           => $user->country,
            'profile_photo_url' => $photoUrl,
        ];
    }

    private function serializeUser(User $user, int $currentUserId, ?Group $group = null): array
    {
        return [
            'id'                => $user->id,
            'name'              => $user->name,
            'username'          => $user->username,
            'email'             => $user->email,
            'profile_photo_url' => $user->profile_photo_url,
            'is_owner'          => $group ? $group->created_by === $user->id : false,
            'is_current_user'   => $user->id === $currentUserId,
        ];
    }

    private function isGroupMember(Group $group, int $userId): bool
    {
        if ($group->relationLoaded('members')) {
            return $group->members->contains('id', $userId);
        }

        return $group->members()->where('users.id', $userId)->exists();
    }

    private function storeUploadedImage(Request $request, string $key, string $directory, string $prefix): ?string
    {
        if (!$request->hasFile($key)) {
            return null;
        }

        $file = $request->file($key);
        if (!$file || !$file->isValid()) {
            return null;
        }

        $uploadPath = public_path("assets/uploads/{$directory}");
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $filename = $prefix . '_' . now()->timestamp . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $filename);

        return "assets/uploads/{$directory}/{$filename}";
    }

    private function deleteUploadedAsset(?string $path): void
    {
        if (!$path || str_starts_with($path, 'preset:')) {
            return;
        }

        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }

    private function resolveAssetUrl(?string $path): ?string
    {
        if (!$path || str_starts_with($path, 'preset:')) {
            return null;
        }

        return url($path);
    }

    private function getUsdLkrRate(): float
    {
        return Cache::remember('usd_lkr_exchange_rate', 3600, function () {
            try {
                $response = Http::timeout(3)->get('https://api.exchangerate-api.com/v4/latest/USD');
                if ($response->successful()) {
                    $rate = (float) $response->json('rates.LKR');
                    if ($rate > 0) {
                        return $rate;
                    }
                }
            } catch (\Throwable $exception) {
                // Fall through to the static fallback below.
            }

            return 325.40;
        });
    }

    // ── OTP ─────────────────────────────────────────────────────────────────

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put('otp_' . md5($request->email), Hash::make($otp), now()->addMinutes(10));

        try {
            Mail::raw(
                "Your PayPatch verification code is: {$otp}\n\nThis code expires in 10 minutes.\nIf you did not request this, ignore this email.",
                function ($message) use ($request) {
                    $message->to($request->email)->subject('PayPatch Email Verification');
                }
            );
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send email: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Verification code sent.']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        $key       = 'otp_' . md5($request->email);
        $hashedOtp = Cache::get($key);

        if (!$hashedOtp || !Hash::check($request->otp, $hashedOtp)) {
            return response()->json(['message' => 'Invalid or expired code. Please request a new one.'], 422);
        }

        Cache::forget($key);

        return response()->json(['message' => 'OTP verified.']);
    }

    // ── Google OAuth ─────────────────────────────────────────────────────────

    public function googleAuth(Request $request)
    {
        $request->validate(['id_token' => 'required|string']);

        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $request->id_token,
        ]);

        if (!$response->successful()) {
            return response()->json(['message' => 'Invalid Google token.'], 401);
        }

        $payload       = $response->json();
        $email         = $payload['email'] ?? null;
        $emailVerified = ($payload['email_verified'] ?? 'false') === 'true';
        $name          = $payload['name'] ?? 'Google User';

        if (!$email || !$emailVerified) {
            return response()->json(['message' => 'Could not verify Google account.'], 401);
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'              => $name,
                'password'          => Hash::make(Str::random(32)),
                'country'           => 'Unknown',
                'email_verified_at' => now(),
            ]
        );

        $token = $user->createToken('google-auth')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $this->serializeAuthUser($user),
        ]);
    }
}
