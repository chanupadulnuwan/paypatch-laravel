<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 8, 2)->default(0.00);
            $table->integer('discount_percent')->default(0);
            $table->integer('max_group_members')->default(10);
            $table->integer('max_groups')->default(5);
            $table->text('features')->nullable(); // comma-separated features
            $table->timestamps();
        });

        // Seed default packages
        DB::table('packages')->insert([
            [
                'name' => 'Free Tier',
                'price' => 0.00,
                'discount_percent' => 0,
                'max_group_members' => 10,
                'max_groups' => 5,
                'features' => 'Limit of 10 members per group,Standard splits (Equal & Checklist),2-Way Settle Up requests',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Premium',
                'price' => 9.99,
                'discount_percent' => 15,
                'max_group_members' => 50,
                'max_groups' => 20,
                'features' => 'Limit of 50 members per group,Custom percent & weight split tools,Priority support response',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Premium Plus',
                'price' => 19.99,
                'discount_percent' => 20,
                'max_group_members' => 100,
                'max_groups' => 9999,
                'features' => 'All Premium plan features,Smart AI settlement optimizer,Unlimited transaction history',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
