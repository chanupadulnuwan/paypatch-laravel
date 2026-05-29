<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Pivot table — links users to groups (many-to-many)
// composite primary key means no duplicate memberships
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_members', function (Blueprint $table) {
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('joined_at')->useCurrent();
            $table->primary(['group_id', 'user_id']); // composite PK — no duplicate members
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};
