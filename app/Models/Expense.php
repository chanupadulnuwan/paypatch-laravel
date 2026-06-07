<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'paid_by',
        'created_by',
        'title',
        'amount',
        'split_type',
        'receipt_image_path',
    ];

    // ─── ACCESSOR ────────────────────────────────────────────────────────────
    // Returns the amount formatted as "LKR 1,250.00"
    // Usage: $expense->formatted_amount  (snake_case in Blade)
    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => 'LKR ' . number_format($this->amount, 2),
        );
    }

    // ─── RELATIONSHIPS ───────────────────────────────────────────────────────
    // The user who paid for this expense
    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    // The user who added/created this expense entry
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // The individual shares for each member
    public function shares()
    {
        return $this->hasMany(ExpenseShare::class);
    }

    // The group this expense belongs to
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
