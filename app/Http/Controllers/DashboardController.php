<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin');
        }

        $userId = Auth::id();

        // Clear cache to keep the redesign fully dynamic and up to date
        Cache::forget("dashboard_groups_{$userId}");

        $groups = Group::forUser($userId)
            ->withCount('members')                       // adds members_count
            ->with(['expenses.shares', 'settlements'])   // eager load
            ->get()
            ->map(function ($group) use ($userId) {
                $group->your_balance = $this->calculateBalance($group, $userId);
                $group->total_expenses = $group->expenses->sum('amount');
                return $group;
            });

        // Compute totals
        $youOwe = abs($groups->where('your_balance', '<', 0)->sum('your_balance'));
        $youAreOwed = $groups->where('your_balance', '>', 0)->sum('your_balance');

        $groupsYouOweCount = $groups->where('your_balance', '<', 0)->count();
        $groupsYouAreOwedCount = $groups->where('your_balance', '>', 0)->count();

        // Fetch recent expenses across all user's groups
        $groupIds = $groups->pluck('id');
        $recentExpenses = \App\Models\Expense::whereIn('group_id', $groupIds)
            ->with(['paidBy', 'group'])
            ->latest()
            ->take(5)
            ->get();

        // Compute live settle-up suggestions across all groups
        $suggestions = [];
        $debtCalculator = new \App\Services\DebtCalculatorService();
        foreach ($groups as $group) {
            $members = $group->members()->get();
            $memberBalances = [];
            foreach ($members as $member) {
                $paid     = $group->expenses->where('paid_by', $member->id)->sum('amount');
                $share    = $group->expenses->flatMap->shares->where('user_id', $member->id)->sum('share_amount');
                $sent     = $group->settlements->where('from_user_id', $member->id)->sum('amount');
                $received = $group->settlements->where('to_user_id', $member->id)->sum('amount');
                $memberBalances[$member->id] = round($paid - $share - $sent + $received, 2);
            }
            $debts = $debtCalculator->simplify($memberBalances);
            foreach ($debts as $debt) {
                if ($debt['from'] == $userId || $debt['to'] == $userId) {
                    $suggestions[] = [
                        'group_id' => $group->id,
                        'group_name' => $group->name,
                        'from' => \App\Models\User::find($debt['from']),
                        'to' => \App\Models\User::find($debt['to']),
                        'amount' => $debt['amount']
                    ];
                }
            }
        }
        $suggestions = array_slice($suggestions, 0, 3);

        $exchangeRate = $this->getExchangeRate() ?? 325.40;

        return view('dashboard', compact(
            'groups', 
            'exchangeRate', 
            'youOwe', 
            'youAreOwed', 
            'groupsYouOweCount', 
            'groupsYouAreOwedCount',
            'recentExpenses',
            'suggestions'
        ));
    }

    // Balance = paid - your_share - settlements_sent + settlements_received
    private function calculateBalance($group, $userId): float
    {
        $paid = $group->expenses->where('paid_by', $userId)->sum('amount');

        $share = $group->expenses->flatMap->shares
            ->where('user_id', $userId)
            ->sum('share_amount');

        $sent     = $group->settlements->where('from_user_id', $userId)->sum('amount');
        $received = $group->settlements->where('to_user_id', $userId)->sum('amount');

        return round($paid - $share - $sent + $received, 2);
    }

    // Fetch live USD→LKR rate; returns null if API is down
    private function getExchangeRate(): ?float
    {
        try {
            $response = Http::timeout(3)->get('https://api.exchangerate-api.com/v4/latest/USD');
            if ($response->successful()) {
                return $response->json('rates.LKR');
            }
        } catch (\Exception $e) {
            // silently fail — dashboard still works without it
        }
        return null;
    }
}
