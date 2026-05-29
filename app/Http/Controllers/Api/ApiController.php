<?php

namespace App\Http\Controllers\Api;

use App\Events\ExpenseCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Resources\GroupResource;
use App\Models\Expense;
use App\Models\ExpenseShare;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

// ApiController handles all /api/* routes
// All methods (except login) are protected by auth:sanctum middleware in api.php

class ApiController extends Controller
{
    // ── POST /api/login ──────────────────────────────────────────────────────
    // Returns a Sanctum API token the client can use for all future requests.
    // Rate limited to 5 attempts per minute (set in api.php with throttle:5,1)
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !password_verify($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        // createToken gives both 'read' and 'write' abilities
        // tokenCan('write') is checked before creating expenses
        $token = $user->createToken('api-token', ['read', 'write']);

        return response()->json([
            'token' => $token->plainTextToken,
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    // ── DELETE /api/logout ───────────────────────────────────────────────────
    // Revokes the current token so it can no longer be used
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    // ── GET /api/groups ──────────────────────────────────────────────────────
    // Returns paginated list of the user's groups with their balance
    public function groups(Request $request)
    {
        $userId = $request->user()->id;

        $groups = Group::forUser($userId)
            ->withCount('members')
            ->with(['expenses.shares', 'settlements', 'creator'])
            ->paginate(15); // paginate: 15 per page, adds links/meta automatically

        // Attach the user's balance to each group before wrapping in resource
        $groups->getCollection()->transform(function ($group) use ($userId) {
            $group->your_balance = $this->calculateBalance($group, $userId);
            return $group;
        });

        // GroupResource::collection wraps each group through GroupResource::toArray()
        return GroupResource::collection($groups);
    }

    // ── POST /api/groups ─────────────────────────────────────────────────────
    // Create a new group
    public function storeGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group = DB::transaction(function () use ($request) {
            $group = Group::create([
                'name'       => $request->name,
                'created_by' => $request->user()->id,
            ]);
            $group->members()->attach($request->user()->id, ['joined_at' => now()]);
            return $group;
        });

        $group->loadCount('members');
        $group->load('creator');
        $group->your_balance = 0;

        return new GroupResource($group);
    }

    // ── GET /api/groups/{id} ─────────────────────────────────────────────────
    // Single group with members and simplified debt list
    public function showGroup(Request $request, Group $group)
    {
        // Make sure the requesting user is a member
        if (!$group->members()->where('users.id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Not a member of this group.'], 403);
        }

        $group->load(['members', 'expenses.shares', 'settlements', 'creator']);
        $group->loadCount('members');

        $userId = $request->user()->id;
        $group->your_balance = $this->calculateBalance($group, $userId);

        // Include members list in response (safe — no passwords due to $hidden on User)
        $members = $group->members->map(fn ($m) => [
            'id'   => $m->id,
            'name' => $m->name,
        ]);

        return response()->json([
            'group'   => new GroupResource($group),
            'members' => $members,
        ]);
    }

    // ── POST /api/expenses ───────────────────────────────────────────────────
    // Create an expense — uses StoreExpenseRequest for auth + validation
    // tokenCan('write') ensures the token has write ability
    public function storeExpense(StoreExpenseRequest $request)
    {
        // Check the token has 'write' ability
        if (!$request->user()->tokenCan('write')) {
            return response()->json(['message' => 'Token does not have write permission.'], 403);
        }

        $group = Group::with('members')->findOrFail($request->group_id);

        $expense = DB::transaction(function () use ($request, $group) {
            $expense = Expense::create([
                'group_id'   => $group->id,
                'paid_by'    => $request->paid_by,
                'created_by' => $request->user()->id,
                'title'      => $request->title,
                'amount'     => $request->amount,
                'split_type' => 'equal',
            ]);

            $members     = $group->members;
            $count       = $members->count();
            $shareAmount = floor(((float) $request->amount / $count) * 100) / 100;
            $remainder   = round((float) $request->amount - ($shareAmount * $count), 2);

            foreach ($members as $index => $member) {
                ExpenseShare::create([
                    'expense_id'   => $expense->id,
                    'user_id'      => $member->id,
                    'share_amount' => $index === 0 ? $shareAmount + $remainder : $shareAmount,
                ]);
            }

            ExpenseCreated::dispatch($expense, $request->user());
            Cache::forget('dashboard_groups_' . $request->user()->id);

            return $expense;
        });

        return response()->json([
            'message' => 'Expense created.',
            'expense' => [
                'id'     => $expense->id,
                'title'  => $expense->title,
                'amount' => $expense->formatted_amount,
            ],
        ], 201);
    }

    // ── GET /api/friends ─────────────────────────────────────────────────────
    // Cross-group net balances with each person the user has shared expenses with
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

            foreach ($group->settlements as $s) {
                if ($s->from_user_id === $userId) {
                    $balanceMap[$s->to_user_id] = ($balanceMap[$s->to_user_id] ?? 0) + $s->amount;
                } elseif ($s->to_user_id === $userId) {
                    $balanceMap[$s->from_user_id] = ($balanceMap[$s->from_user_id] ?? 0) - $s->amount;
                }
            }
        }

        $friends = collect($balanceMap)->map(function ($balance, $id) {
            $user = User::find($id);
            return $user ? [
                'user_id' => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
                'balance' => round($balance, 2),
                'status'  => $balance > 0 ? 'owes_you' : ($balance < 0 ? 'you_owe' : 'settled'),
            ] : null;
        })->filter()->values();

        return response()->json(['friends' => $friends]);
    }

    // ── Helper: calculate a user's balance in one group ───────────────────────
    private function calculateBalance($group, $userId): float
    {
        $paid     = $group->expenses->where('paid_by', $userId)->sum('amount');
        $share    = $group->expenses->flatMap->shares->where('user_id', $userId)->sum('share_amount');
        $sent     = $group->settlements->where('from_user_id', $userId)->sum('amount');
        $received = $group->settlements->where('to_user_id', $userId)->sum('amount');

        return round($paid - $share - $sent + $received, 2);
    }
}
