<?php

namespace App\Listeners;

use App\Events\ExpenseCreated;
use App\Models\ActivityLog;

// This listener runs automatically whenever ExpenseCreated is fired.
// It writes a row to activity_logs so it shows up in the activity feed.

class LogExpenseActivity
{
    public function handle(ExpenseCreated $event): void
    {
        $expense = $event->expense;
        $creator = $event->creator;

        ActivityLog::create([
            'group_id' => $expense->group_id,
            'user_id'  => $creator->id,
            'message'  => $creator->name . ' added "' . $expense->title . '" — LKR ' . number_format($expense->amount, 2),
            'type'     => 'expense',
        ]);
    }
}
