<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use App\Models\Expense;
use App\Models\ExpenseShare;
use App\Models\Settlement;
use App\Models\ActivityLog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ─── 1. CREATE SYSTEM USERS ──────────────────────────────────────────
        
        // System Admin
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@paypatch.com',
            'password' => 'Admin@123', // Meets new password criteria!
            'role' => 'admin',
            'status' => 'active',
            'account_type' => 'premium',
            'email_verified_at' => now(),
        ]);

        // Main User
        $testUser = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password@123',
            'role' => 'user',
            'status' => 'active',
            'account_type' => 'free',
            'country' => 'Sri Lanka',
            'email_verified_at' => now(),
        ]);

        // Alice
        $alice = User::create([
            'name' => 'Alice Wijesinghe',
            'email' => 'alice@example.com',
            'password' => 'Alice@123',
            'role' => 'user',
            'status' => 'active',
            'account_type' => 'free',
            'country' => 'Sri Lanka',
            'email_verified_at' => now(),
        ]);

        // Bob
        $bob = User::create([
            'name' => 'Bob Perera',
            'email' => 'bob@example.com',
            'password' => 'BobPass@123',
            'role' => 'user',
            'status' => 'active',
            'account_type' => 'premium',
            'country' => 'Sri Lanka',
            'email_verified_at' => now(),
        ]);

        // Charlie
        $charlie = User::create([
            'name' => 'Charlie Silva',
            'email' => 'charlie@example.com',
            'password' => 'Charlie@123',
            'role' => 'user',
            'status' => 'active',
            'account_type' => 'free',
            'country' => 'Sri Lanka',
            'email_verified_at' => now(),
        ]);

        // Dummy User 1
        User::create([
            'name' => 'Dummy User One',
            'email' => 'user1@paypatch.com',
            'password' => 'User1@123',
            'role' => 'user',
            'status' => 'active',
            'account_type' => 'free',
            'country' => 'Sri Lanka',
            'email_verified_at' => now(),
        ]);

        // Dummy User 2
        User::create([
            'name' => 'Dummy User Two',
            'email' => 'user2@paypatch.com',
            'password' => 'User2@123',
            'role' => 'user',
            'status' => 'active',
            'account_type' => 'free',
            'country' => 'Sri Lanka',
            'email_verified_at' => now(),
        ]);

        // Dummy User 3
        User::create([
            'name' => 'Dummy User Three',
            'email' => 'user3@paypatch.com',
            'password' => 'User3@123',
            'role' => 'user',
            'status' => 'active',
            'account_type' => 'free',
            'country' => 'Sri Lanka',
            'email_verified_at' => now(),
        ]);

        // ─── 2. CREATE BILL SPLITTING GROUPS ─────────────────────────────────

        // Ella Trip Group
        $ellaTrip = Group::create([
            'name' => 'Ella Trip 2026',
            'created_by' => $testUser->id,
            'currency' => 'LKR',
            'cover_image_path' => 'preset:mountain',
        ]);
        $ellaTrip->members()->attach([
            $testUser->id => ['joined_at' => now()],
            $alice->id    => ['joined_at' => now()],
            $bob->id      => ['joined_at' => now()],
            $charlie->id  => ['joined_at' => now()],
        ]);
        
        ActivityLog::create([
            'group_id' => $ellaTrip->id,
            'user_id' => $testUser->id,
            'message' => 'Test User created the group "Ella Trip 2026"',
            'type' => 'group',
        ]);

        // Flatmates Group
        $apartment = Group::create([
            'name' => 'Apartment 303',
            'created_by' => $alice->id,
            'currency' => 'LKR',
            'cover_image_path' => 'preset:home',
        ]);
        $apartment->members()->attach([
            $alice->id    => ['joined_at' => now()],
            $testUser->id => ['joined_at' => now()],
            $bob->id      => ['joined_at' => now()],
        ]);

        ActivityLog::create([
            'group_id' => $apartment->id,
            'user_id' => $alice->id,
            'message' => 'Alice Wijesinghe created the group "Apartment 303"',
            'type' => 'group',
        ]);

        // ─── 3. SEED TRIP EXPENSES ───────────────────────────────────────────

        // Fuel Expense split equally among all 4 members
        $fuelExpense = Expense::create([
            'group_id' => $ellaTrip->id,
            'paid_by' => $testUser->id,
            'created_by' => $testUser->id,
            'title' => 'Fuel & Transport',
            'amount' => 12000.00,
            'split_type' => 'equal',
        ]);
        foreach ([$testUser, $alice, $bob, $charlie] as $member) {
            ExpenseShare::create([
                'expense_id' => $fuelExpense->id,
                'user_id' => $member->id,
                'share_amount' => 3000.00,
            ]);
        }
        ActivityLog::create([
            'group_id' => $ellaTrip->id,
            'user_id' => $testUser->id,
            'message' => 'Test User added "Fuel & Transport" — LKR 12,000.00',
            'type' => 'expense',
        ]);

        // Safari tickets paid by Alice, split custom (Only Alice, Bob, Charlie)
        $safariExpense = Expense::create([
            'group_id' => $ellaTrip->id,
            'paid_by' => $alice->id,
            'created_by' => $alice->id,
            'title' => 'Yala Safari Tickets',
            'amount' => 9000.00,
            'split_type' => 'custom',
        ]);
        ExpenseShare::create(['expense_id' => $safariExpense->id, 'user_id' => $testUser->id, 'share_amount' => 0.00]);
        ExpenseShare::create(['expense_id' => $safariExpense->id, 'user_id' => $alice->id, 'share_amount' => 3000.00]);
        ExpenseShare::create(['expense_id' => $safariExpense->id, 'user_id' => $bob->id, 'share_amount' => 3000.00]);
        ExpenseShare::create(['expense_id' => $safariExpense->id, 'user_id' => $charlie->id, 'share_amount' => 3000.00]);
        ActivityLog::create([
            'group_id' => $ellaTrip->id,
            'user_id' => $alice->id,
            'message' => 'Alice Wijesinghe added "Yala Safari Tickets" — LKR 9,000.00 (split)',
            'type' => 'expense',
        ]);

        // Dinner paid by Bob, split equally
        $dinnerExpense = Expense::create([
            'group_id' => $ellaTrip->id,
            'paid_by' => $bob->id,
            'created_by' => $bob->id,
            'title' => 'Dinner at Nine Arch',
            'amount' => 6000.00,
            'split_type' => 'equal',
        ]);
        foreach ([$testUser, $alice, $bob, $charlie] as $member) {
            ExpenseShare::create([
                'expense_id' => $dinnerExpense->id,
                'user_id' => $member->id,
                'share_amount' => 1500.00,
            ]);
        }
        ActivityLog::create([
            'group_id' => $ellaTrip->id,
            'user_id' => $bob->id,
            'message' => 'Bob Perera added "Dinner at Nine Arch" — LKR 6,000.00',
            'type' => 'expense',
        ]);

        // ─── 4. SEED SETTLEMENTS ─────────────────────────────────────────────

        // Charlie settles part of his debt directly to Alice
        Settlement::create([
            'group_id' => $ellaTrip->id,
            'from_user_id' => $charlie->id,
            'to_user_id' => $alice->id,
            'amount' => 2000.00,
            'note' => 'Thanks for safari tickets!',
        ]);
        ActivityLog::create([
            'group_id' => $ellaTrip->id,
            'user_id' => $charlie->id,
            'message' => 'Charlie Silva paid Alice Wijesinghe LKR 2,000.00',
            'type' => 'settle',
        ]);
    }
}
