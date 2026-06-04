<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\Expense;
use App\Models\ExpenseShare;
use App\Models\Settlement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InsightsController extends Controller
{
    public function index()
    {
        // Admins are redirected to their own system insights page
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.insights');
        }

        $userId = Auth::id();

        // 1. Basic Stats Calculation
        $totalPaid = (float) Expense::where('paid_by', $userId)->sum('amount');
        $totalShare = (float) ExpenseShare::where('user_id', $userId)->sum('share_amount');
        
        $settlementsSent = (float) Settlement::where('from_user_id', $userId)->sum('amount');
        $settlementsReceived = (float) Settlement::where('to_user_id', $userId)->sum('amount');
        
        $netBalance = round($totalPaid - $totalShare - $settlementsSent + $settlementsReceived, 2);

        // 2. Spending Trend Over Last 6 Months (Month-by-month user share)
        $months = [];
        $monthlySpending = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('F');
            
            $sum = ExpenseShare::where('user_id', $userId)
                ->whereHas('expense', function ($q) use ($date) {
                    $q->whereYear('created_at', $date->year)
                      ->whereMonth('created_at', $date->month);
                })
                ->sum('share_amount');
                
            $monthlySpending[] = (float) $sum;
        }

        // 3. Group spending breakdown
        $groupSpending = ExpenseShare::where('expense_shares.user_id', $userId)
            ->join('expenses', 'expense_shares.expense_id', '=', 'expenses.id')
            ->join('groups', 'expenses.group_id', '=', 'groups.id')
            ->select('groups.name', DB::raw('SUM(expense_shares.share_amount) as total'))
            ->groupBy('groups.name')
            ->get();
            
        $groupSpendingNames = $groupSpending->pluck('name')->toArray();
        $groupSpendingTotals = $groupSpending->pluck('total')->map(fn($t) => (float)$t)->toArray();

        // 4. Top Creditors and Debtors
        // Fetch all groups user is in
        $groups = Group::forUser($userId)->with(['expenses.shares', 'settlements'])->get();
        $balanceMap = [];

        foreach ($groups as $group) {
            foreach ($group->expenses as $expense) {
                $payerId = $expense->paid_by;
                foreach ($expense->shares as $share) {
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

        $creditors = [];
        $debtors = [];
        foreach ($balanceMap as $id => $bal) {
            $user = User::find($id);
            if (!$user) continue;
            
            $balVal = round($bal, 2);
            if ($balVal < 0) {
                $creditors[] = [
                    'user' => $user,
                    'balance' => abs($balVal)
                ];
            } elseif ($balVal > 0) {
                $debtors[] = [
                    'user' => $user,
                    'balance' => $balVal
                ];
            }
        }

        // Sort by amount desc
        usort($creditors, fn($a, $b) => $b['balance'] <=> $a['balance']);
        usort($debtors, fn($a, $b) => $b['balance'] <=> $a['balance']);

        // Limit to top 5
        $topCreditors = array_slice($creditors, 0, 5);
        $topDebtors = array_slice($debtors, 0, 5);

        // 5. Records & Averages
        $avgExpenseShare = (float) (ExpenseShare::where('user_id', $userId)->avg('share_amount') ?? 0);
        
        $mostActiveGroup = $groups->sortByDesc(fn($g) => $g->expenses->count())->first();
        $mostExpensiveGroup = $groups->sortByDesc(fn($g) => $g->expenses->sum('amount'))->first();

        return view('insights.index', compact(
            'totalPaid',
            'totalShare',
            'netBalance',
            'months',
            'monthlySpending',
            'groupSpendingNames',
            'groupSpendingTotals',
            'topCreditors',
            'topDebtors',
            'avgExpenseShare',
            'mostActiveGroup',
            'mostExpensiveGroup'
        ));
    }
}
