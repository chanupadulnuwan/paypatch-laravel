<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->foreignId('request_to_user_id')->nullable()->after('type')->constrained('users')->cascadeOnDelete();
            $table->decimal('request_amount', 15, 2)->nullable()->after('request_to_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign(['request_to_user_id']);
            $table->dropColumn(['request_to_user_id', 'request_amount']);
        });
    }
};
