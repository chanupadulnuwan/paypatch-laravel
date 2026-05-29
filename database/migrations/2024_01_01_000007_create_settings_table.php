<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Key-value store for app-wide settings (e.g. max_group_members)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
        });

        // Seed default settings
        DB::table('settings')->insert([
            ['key' => 'max_group_members', 'value' => '10'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
