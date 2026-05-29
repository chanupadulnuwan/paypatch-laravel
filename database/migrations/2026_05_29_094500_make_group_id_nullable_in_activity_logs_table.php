<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Make group_id nullable to support system-wide administrative action logs
        DB::statement('ALTER TABLE activity_logs MODIFY group_id BIGINT UNSIGNED NULL;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE activity_logs MODIFY group_id BIGINT UNSIGNED NOT NULL;');
    }
};
