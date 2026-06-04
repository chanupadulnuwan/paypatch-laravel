<?php

namespace App\Livewire;

use App\Events\ExpenseCreated;
use App\Models\Expense;
use App\Models\ExpenseShare;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

// ExpenseForm Livewire component
// Handles the add-expense form with a LIVE split preview.
// When the user types an amount, the "per person" amount updates instantly
// without any page reload — that's what wire:model.live does.

class ExpenseForm extends Component
{
    // Public properties are two-way bound to form inputs via wire:model
    public string $title  = '';
    public string $amount = '';
    public int    $groupId;
    public int    $paidBy;
    public int    $memberCount = 1;

    // Called when the component is first mounted (rendered)
    // Sets up defaults from the URL query string if present
    public function mount(int $preselectedGroupId = 0): void
    {
        $this->groupId = $preselectedGroupId;

        if ($this->groupId) {
            $group = Group::with('members')->find($this->groupId);
            if ($group) {
                $this->memberCount = $group->members->count();
            }
        }

        $this->paidBy = Auth::id();
    }

    // Computed property — recalculates automatically when $amount or $memberCount changes
    // Accessed in Blade as $this->splitAmount
    public function getSplitAmountProperty(): string
    {
        if (!is_numeric($this->amount) || $this->amount <= 0 || $this->memberCount < 1) {
            return 'LKR 0.00';
        }
        $perPerson = round((float) $this->amount / $this->memberCount, 2);
        return 'LKR ' . number_format($perPerson, 2);
    }

    // Called when the user picks a different group — updates member count for preview
    public function updatedGroupId(int $value): void
    {
        $group = Group::with('members')->find($value);
        $this->memberCount = $group ? $group->members->count() : 1;
        $this->paidBy = Auth::id();
    }

    // save() — validates, creates expense + shares inside a transaction, fires event
    public function save(): void
    {
        $this->validate([
            'title'   => 'required|string|max:255',
            'amount'  => 'required|numeric|min:0.01',
            'groupId' => 'required|exists:groups,id',
            'paidBy'  => 'required|exists:users,id',
        ]);

        $group = Group::with('members')->findOrFail($this->groupId);

        // Make sure current user is in this group
        if (!$group->members->contains(Auth::id())) {
            $this->addError('groupId', 'You are not a member of this group.');
            return;
        }

        DB::transaction(function () use ($group) {
            $expense = Expense::create([
                'group_id'   => $group->id,
                'paid_by'    => $this->paidBy,
                'created_by' => Auth::id(),
                'title'      => $this->title,
                'amount'     => $this->amount,
                'split_type' => 'equal',
            ]);

            // Equal split — distribute amount evenly, remainder to first member
            $members     = $group->members;
            $count       = $members->count();
            $shareAmount = floor(((float) $this->amount / $count) * 100) / 100;
            $remainder   = round((float) $this->amount - ($shareAmount * $count), 2);

            foreach ($members as $index => $member) {
                ExpenseShare::create([
                    'expense_id'   => $expense->id,
                    'user_id'      => $member->id,
                    'share_amount' => $index === 0 ? $shareAmount + $remainder : $shareAmount,
                ]);
            }

            // Fire event → LogExpenseActivity listener writes to activity_logs
            ExpenseCreated::dispatch($expense, Auth::user());

            $group->forgetMembersCache();
        });

        // Reset form after successful save
        $this->title  = '';
        $this->amount = '';

        // Send a browser event so the page can show a success message
        $this->dispatch('expense-saved');

        // Redirect back to the group page
        $this->redirectRoute('groups.show', $this->groupId);
    }

    public function render()
    {
        // Load all groups this user belongs to for the group dropdown
        $groups  = Group::forUser(Auth::id())->get();

        // Load members of the selected group for the "paid by" dropdown
        $members = $this->groupId
            ? Group::with('members')->find($this->groupId)?->members ?? collect()
            : collect();

        return view('livewire.expense-form', compact('groups', 'members'));
    }
}
