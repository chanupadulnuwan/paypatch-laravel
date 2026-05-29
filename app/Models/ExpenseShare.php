<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseShare extends Model
{
    // No auto-incrementing id — composite primary key (expense_id + user_id)
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['expense_id', 'user_id', 'share_amount'];

    // The expense this share belongs to
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    // The user this share belongs to
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
