<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('group_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->string('image_path')->nullable();
            $table->text('caption')->nullable();
            $table->enum('audience', ['group', 'friends'])->default('group');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('group_posts'); }
};
