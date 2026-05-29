<?php

namespace App\Events;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// This event is fired when a new expense is saved.
// It carries the expense + creator so the listener can write an activity log.

class ExpenseCreated
{
    use Dispatchable, SerializesModels;

    public Expense $expense;
    public User $creator;

    public function __construct(Expense $expense, User $creator)
    {
        $this->expense = $expense;
        $this->creator = $creator;
    }
}
