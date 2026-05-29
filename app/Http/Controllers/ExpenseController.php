<?php

namespace App\Http\Controllers;

use App\Events\ExpenseCreated;
use App\Models\Expense;
use App\Models\ExpenseShare;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    // create — show the add expense form (Livewire component handles it)
    public function create()
    {
        return view('expenses.create');
    }

    // store — save a new expense with equal or custom selected member split shares
    // Uses DB::transaction so either BOTH the expense AND shares save, or neither does
    public function store(Request $request)
    {
        $request->validate([
            'group_id'         => 'required|exists:groups,id',
            'title'            => 'required|string|max:255',
            'amount'           => 'required|numeric|min:0.01',
            'paid_by'          => 'required|exists:users,id',
            'split_type'       => 'required|in:equal,custom',
            'selected_members' => 'required_if:split_type,custom|array|min:1',
            'selected_members.*' => 'exists:users,id',
        ]);

        $group = Group::with('members')->findOrFail($request->group_id);

        // Make sure the logged-in user is in this group
        if (!$group->members->contains(Auth::id())) {
            abort(403);
        }

        // Custom splits validation: must select at least one member
        if ($request->split_type === 'custom') {
            if (empty($request->selected_members)) {
                return back()
                    ->withErrors(['amount' => 'You must select at least one member to split the expense.'])
                    ->withInput()
                    ->with('modal', 'add-expense');
            }
        }

        DB::transaction(function () use ($request, $group) {
            $expense = Expense::create([
                'group_id'   => $group->id,
                'paid_by'    => $request->paid_by,
                'created_by' => Auth::id(),
                'title'      => $request->title,
                'amount'     => $request->amount,
                'split_type' => $request->split_type,
            ]);

            $members = $group->members;

            if ($request->split_type === 'custom') {
                $selectedIds = $request->selected_members ?? [];
                $count = count($selectedIds);
                $shareAmount = floor(($request->amount / $count) * 100) / 100;
                $remainder = round($request->amount - ($shareAmount * $count), 2);

                $firstSelectedProcessed = false;
                foreach ($members as $member) {
                    $isFirstSelected = false;
                    $hasShare = in_array($member->id, $selectedIds);
                    if ($hasShare && !$firstSelectedProcessed) {
                        $isFirstSelected = true;
                        $firstSelectedProcessed = true;
                    }

                    ExpenseShare::create([
                        'expense_id'   => $expense->id,
                        'user_id'      => $member->id,
                        'share_amount' => $hasShare ? ($isFirstSelected ? $shareAmount + $remainder : $shareAmount) : 0,
                    ]);
                }
            } else {
                // Equal split: divide amount by number of members
                // The remainder (from rounding) goes to the first member
                $count      = $members->count();
                $shareAmount = floor(($request->amount / $count) * 100) / 100; // round down to 2dp
                $remainder  = round($request->amount - ($shareAmount * $count), 2);

                foreach ($members as $index => $member) {
                    ExpenseShare::create([
                        'expense_id'   => $expense->id,
                        'user_id'      => $member->id,
                        'share_amount' => $index === 0 ? $shareAmount + $remainder : $shareAmount,
                    ]);
                }
            }

            // Fire the event — the LogExpenseActivity listener will write to activity_logs
            ExpenseCreated::dispatch($expense, Auth::user());

            Cache::forget("dashboard_groups_" . Auth::id());
        });

        return redirect()->route('groups.show', $request->group_id)
            ->with('success', 'Expense added!');
    }

    // destroy — delete an expense (only creator or group owner)
    public function destroy(Expense $expense)
    {
        $group = $expense->group;

        if ($expense->created_by !== Auth::id() && $group->created_by !== Auth::id()) {
            abort(403, 'You do not have permission to delete this expense.');
        }

        $expense->delete(); // cascades to expense_shares

        Cache::forget("dashboard_groups_" . Auth::id());

        return back()->with('success', 'Expense deleted.');
    }

    // update — save changes to an existing expense (only creator or group owner)
    public function update(Request $request, Expense $expense)
    {
        $group = $expense->group;

        if ($expense->created_by !== Auth::id() && $group->created_by !== Auth::id()) {
            abort(403, 'You do not have permission to edit this expense.');
        }

        $request->validate([
            'title'            => 'required|string|max:255',
            'amount'           => 'required|numeric|min:0.01',
            'paid_by'          => 'required|exists:users,id',
            'split_type'       => 'required|in:equal,custom',
            'selected_members' => 'required_if:split_type,custom|array|min:1',
            'selected_members.*' => 'exists:users,id',
        ]);

        // Custom splits validation: must select at least one member
        if ($request->split_type === 'custom') {
            if (empty($request->selected_members)) {
                return back()
                    ->withErrors(['amount' => 'You must select at least one member to split the expense.'])
                    ->withInput()
                    ->with('modal', 'edit-expense-' . $expense->id);
            }
        }

        DB::transaction(function () use ($request, $expense, $group) {
            $expense->update([
                'paid_by'    => $request->paid_by,
                'title'      => $request->title,
                'amount'     => $request->amount,
                'split_type' => $request->split_type,
            ]);

            // Re-create shares
            ExpenseShare::where('expense_id', $expense->id)->delete();

            $members = $group->members;

            if ($request->split_type === 'custom') {
                $selectedIds = $request->selected_members ?? [];
                $count = count($selectedIds);
                $shareAmount = floor(($request->amount / $count) * 100) / 100;
                $remainder = round($request->amount - ($shareAmount * $count), 2);

                $firstSelectedProcessed = false;
                foreach ($members as $member) {
                    $isFirstSelected = false;
                    $hasShare = in_array($member->id, $selectedIds);
                    if ($hasShare && !$firstSelectedProcessed) {
                        $isFirstSelected = true;
                        $firstSelectedProcessed = true;
                    }

                    ExpenseShare::create([
                        'expense_id'   => $expense->id,
                        'user_id'      => $member->id,
                        'share_amount' => $hasShare ? ($isFirstSelected ? $shareAmount + $remainder : $shareAmount) : 0,
                    ]);
                }
            } else {
                // Equal split
                $count      = $members->count();
                $shareAmount = floor(($request->amount / $count) * 100) / 100;
                $remainder  = round($request->amount - ($shareAmount * $count), 2);

                foreach ($members as $index => $member) {
                    ExpenseShare::create([
                        'expense_id'   => $expense->id,
                        'user_id'      => $member->id,
                        'share_amount' => $index === 0 ? $shareAmount + $remainder : $shareAmount,
                    ]);
                }
            }

            // Log update activity directly in database
            ActivityLog::create([
                'group_id' => $group->id,
                'user_id'  => Auth::id(),
                'message'  => Auth::user()->name . ' updated the expense "' . $expense->title . '" — ' . $group->currency . ' ' . number_format($expense->amount, 2),
                'type'     => 'expense',
            ]);

            Cache::forget("dashboard_groups_" . Auth::id());
        });

        return redirect()->route('groups.show', $group->id)
            ->with('success', 'Expense updated!');
    }

    // These are required by --resource but not used
    public function index()   { abort(404); }
    public function show($id) { abort(404); }
    public function edit($id) { abort(404); }
}
