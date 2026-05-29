<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Stores each person's share of an expense
// e.g. expense of LKR 300 split 3 ways = 3 rows each with share_amount = 100
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_shares', function (Blueprint $table) {
            $table->foreignId('expense_id')->constrained('expenses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('share_amount', 10, 2);
            $table->primary(['expense_id', 'user_id']); // composite PK — one share per user per expense
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_shares');
    }
};
