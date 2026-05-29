<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FriendsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Get all groups the user is in, with expenses, shares, and settlements eager loaded
        $groups = Group::forUser($userId)
            ->with(['expenses.shares', 'settlements', 'members'])
            ->get();

        // Build a net balance map: otherUserId => net amount
        // Positive = they owe you, Negative = you owe them
        $balanceMap = [];

        foreach ($groups as $group) {
            foreach ($group->expenses as $expense) {
                $payerId = $expense->paid_by;

                foreach ($expense->shares as $share) {
                    $shareUserId = $share->user_id;

                    if ($payerId === $userId && $shareUserId !== $userId) {
                        // I paid, they owe me their share
                        $balanceMap[$shareUserId] = ($balanceMap[$shareUserId] ?? 0) + $share->share_amount;
                    } elseif ($shareUserId === $userId && $payerId !== $userId) {
                        // They paid, I owe them my share
                        $balanceMap[$payerId] = ($balanceMap[$payerId] ?? 0) - $share->share_amount;
                    }
                }
            }

            // Adjust for settlements
            foreach ($group->settlements as $settlement) {
                if ($settlement->from_user_id === $userId) {
                    // I paid someone — reduces what I owe them (or increases what they owe me)
                    $balanceMap[$settlement->to_user_id] = ($balanceMap[$settlement->to_user_id] ?? 0) + $settlement->amount;
                } elseif ($settlement->to_user_id === $userId) {
                    // Someone paid me — reduces what they owe me
                    $balanceMap[$settlement->from_user_id] = ($balanceMap[$settlement->from_user_id] ?? 0) - $settlement->amount;
                }
            }
        }

        // Load user objects for display, sort by absolute balance (largest first)
        $friends = collect($balanceMap)
            ->map(fn ($balance, $id) => [
                'user'    => User::find($id),
                'balance' => round($balance, 2),
            ])
            ->filter(fn ($item) => $item['user'] !== null)
            ->sortByDesc(fn ($item) => abs($item['balance']))
            ->values();

        // Compute totals
        $youOwe = abs($friends->where('balance', '<', 0)->sum('balance'));
        $youAreOwed = $friends->where('balance', '>', 0)->sum('balance');
        $totalFriends = $friends->count();

        // Fetch recent expenses/activities across our groups
        $groupIds = Group::forUser($userId)->pluck('id');
        $recentExpenses = \App\Models\Expense::whereIn('group_id', $groupIds)
            ->with(['paidBy', 'group'])
            ->latest()
            ->take(5)
            ->get();

        // Load suggestions (users not currently sharing any expenses with us)
        $friendUserIds = $friends->pluck('user.id')->toArray();
        $friendUserIds[] = $userId; // exclude self
        $suggestions = User::whereNotIn('id', $friendUserIds)
            ->take(3)
            ->get();

        return view('friends.index', compact('friends', 'youOwe', 'youAreOwed', 'totalFriends', 'recentExpenses', 'suggestions'));
    }
}
