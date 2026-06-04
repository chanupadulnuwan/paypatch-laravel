<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\ActivityLog;
use App\Models\Settlement;
use App\Models\Setting;
use App\Models\User;
use App\Services\DebtCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $groups = Cache::remember("dashboard_groups_{$userId}", 3600, function () use ($userId) {
            return Group::forUser($userId)
                ->withCount('members')                       // adds members_count
                ->with(['expenses.shares', 'settlements'])   // eager load
                ->get()
                ->map(function ($group) use ($userId) {
                    // Balance = paid - your_share - settlements_sent + settlements_received
                    $paid = $group->expenses->where('paid_by', $userId)->sum('amount');
                    $share = $group->expenses->flatMap->shares->where('user_id', $userId)->sum('share_amount');
                    $sent = $group->settlements->where('from_user_id', $userId)->sum('amount');
                    $received = $group->settlements->where('to_user_id', $userId)->sum('amount');
                    
                    $group->your_balance = round($paid - $share - $sent + $received, 2);
                    $group->total_expenses = $group->expenses->sum('amount');
                    return $group;
                });
        });

        return view('groups.index', compact('groups'));
    }

    // create — show the create group form
    public function create()
    {
        return view('groups.create');
    }

    // store — save a new group, add the creator as a member
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $group = Group::create([
                'name'       => $request->name,
                'created_by' => Auth::id(),
            ]);

            // Add creator as the first member
            $group->members()->attach(Auth::id(), ['joined_at' => now()]);

            ActivityLog::create([
                'group_id' => $group->id,
                'user_id'  => Auth::id(),
                'message'  => Auth::user()->name . ' created the group "' . $group->name . '"',
                'type'     => 'group',
            ]);
        });

        return redirect()->route('groups.index')->with('success', 'Group created!');
    }

    // show — group detail page with expenses, balances, and simplified debts
    public function show(Group $group)
    {
        // Make sure the logged-in user is actually a member of this group
        if (!$group->members()->where('users.id', Auth::id())->exists()) {
            abort(403, 'You are not a member of this group.');
        }

        // Eager load everything needed — avoids N+1 queries
        $group->load(['expenses.paidBy', 'expenses.shares.user', 'settlements']);
        $members = $group->members()->get();

        // Build balance map: userId => balance
        $memberBalances = [];
        foreach ($members as $member) {
            $paid     = $group->expenses->where('paid_by', $member->id)->sum('amount');
            $share    = $group->expenses->flatMap->shares->where('user_id', $member->id)->sum('share_amount');
            $sent     = $group->settlements->where('from_user_id', $member->id)->sum('amount');
            $received = $group->settlements->where('to_user_id', $member->id)->sum('amount');
            $memberBalances[$member->id] = round($paid - $share - $sent + $received, 2);
        }

        // Use DebtCalculatorService to get minimum transactions needed
        $calculator     = new DebtCalculatorService();
        $simplifiedDebts = $calculator->simplify($memberBalances);

        $expenses = $group->expenses->sortByDesc('created_at');

        // Fetch pending settlement requests where the user is the recipient (to_user)
        $pendingSettlementRequests = ActivityLog::where('group_id', $group->id)
            ->where('type', 'settlement_request')
            ->where('request_to_user_id', Auth::id())
            ->latest()
            ->get();

        return view('groups.show', compact('group', 'members', 'expenses', 'memberBalances', 'simplifiedDebts', 'pendingSettlementRequests'));
    }

    // edit / update — rename the group (owner only)
    public function edit(Group $group)
    {
        abort(404); // We use inline forms, no separate edit page needed
    }

    public function update(Request $request, Group $group)
    {
        if ($group->created_by !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'currency' => 'required|string|max:10',
            'preset_cover' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $updateData = [
            'name' => $request->name,
            'currency' => $request->currency,
        ];

        // Handle cover image upload or preset selection
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = 'group_' . $group->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $uploadPath = public_path('assets/uploads/covers');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $file->move($uploadPath, $filename);
            $updateData['cover_image_path'] = 'assets/uploads/covers/' . $filename;
        } elseif ($request->filled('preset_cover')) {
            $updateData['cover_image_path'] = 'preset:' . $request->preset_cover;
        }

        $group->update($updateData);

        $group->forgetMembersCache();

        return back()->with('success', 'Group updated successfully.');
    }

    // destroy — delete the group (owner only)
    public function destroy(Group $group)
    {
        if ($group->created_by !== Auth::id()) {
            abort(403);
        }

        $group->forgetMembersCache();
        $group->delete(); // cascades to members, expenses, shares, logs, settlements

        return redirect()->route('groups.index')->with('success', 'Group deleted.');
    }

    // addMember — owner adds a user by email
    public function addMember(Request $request, Group $group)
    {
        if ($group->created_by !== Auth::id()) {
            abort(403);
        }

        $request->validate(['email' => 'required|email']);

        // Check max members setting
        $max = (int) Setting::getValue('max_group_members', 10);
        if ($group->members()->count() >= $max) {
            return back()->with('error', "This group already has the maximum of {$max} members.")->with('modal', 'edit-group');
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'No user found with that email.')->with('modal', 'edit-group');
        }

        if ($group->members()->where('users.id', $user->id)->exists()) {
            return back()->with('error', 'That user is already in the group.')->with('modal', 'edit-group');
        }

        $group->members()->attach($user->id, ['joined_at' => now()]);
        $group->forgetMembersCache();

        ActivityLog::create([
            'group_id' => $group->id,
            'user_id'  => Auth::id(),
            'message'  => $user->name . ' was added to the group.',
            'type'     => 'group',
        ]);

        return back()->with('success', $user->name . ' added to the group.')->with('modal', 'edit-group');
    }

    // removeMember — owner removes a member
    public function removeMember(Request $request, Group $group)
    {
        if ($group->created_by !== Auth::id()) {
            abort(403);
        }

        $userId = $request->user_id;

        if ($userId == Auth::id()) {
            return back()->with('error', 'You cannot remove yourself as the owner.')->with('modal', 'edit-group');
        }

        $group->forgetMembersCache();
        $group->members()->detach($userId);
        Cache::forget("dashboard_groups_{$userId}");

        return back()->with('success', 'Member removed.')->with('modal', 'edit-group');
    }

    // settle — record a payment from the logged-in user to another member
    public function settle(Request $request, Group $group)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'amount'     => 'required|numeric|min:0.01',
            'note'       => 'nullable|string|max:255',
        ]);

        Settlement::create([
            'group_id'     => $group->id,
            'from_user_id' => Auth::id(),
            'to_user_id'   => $request->to_user_id,
            'amount'       => $request->amount,
            'note'         => $request->note,
        ]);

        $toUser = User::find($request->to_user_id);

        // Delete any matching pending settlement requests
        ActivityLog::where('group_id', $group->id)
            ->where('type', 'settlement_request')
            ->where('user_id', $request->to_user_id)
            ->where('request_to_user_id', Auth::id())
            ->delete();

        ActivityLog::where('group_id', $group->id)
            ->where('type', 'settlement_request')
            ->where('user_id', Auth::id())
            ->where('request_to_user_id', $request->to_user_id)
            ->delete();

        ActivityLog::create([
            'group_id' => $group->id,
            'user_id'  => Auth::id(),
            'message'  => Auth::user()->name . ' paid ' . $toUser->name . ' ' . $group->currency . ' ' . number_format($request->amount, 2),
            'type'     => 'settle',
        ]);

        $group->forgetMembersCache();

        return back()->with('success', 'Settlement recorded!');
    }

    // requestSettle — send a settlement request alert to another group member
    public function requestSettle(Request $request, Group $group)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'amount'     => 'required|numeric|min:0.01',
        ]);

        $toUser = User::findOrFail($request->to_user_id);

        ActivityLog::create([
            'group_id'           => $group->id,
            'user_id'            => Auth::id(),
            'message'            => Auth::user()->name . ' requested to settle up ' . $group->currency . ' ' . number_format($request->amount, 2) . ' with ' . $toUser->name,
            'type'               => 'settlement_request',
            'request_to_user_id' => $request->to_user_id,
            'request_amount'     => $request->amount,
        ]);

        $group->forgetMembersCache();

        return back()->with('success', 'Settlement request sent!');
    }
}
