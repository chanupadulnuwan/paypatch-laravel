<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanUpgradeTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_with_custom_plan_creates_user_with_correct_account_type(): void
    {
        $this->withoutExceptionHandling();

        // 1. Assert packages exist (since migration seeds them)
        $this->assertDatabaseHas('packages', ['name' => 'Premium']);

        // 2. Register user under Premium Plan
        $response = $this->post('/register', [
            'name' => 'Upgrade User',
            'email' => 'upgrade@example.com',
            'country' => 'Sri Lanka',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'plan' => 'Premium',
            'terms' => 'on',
        ]);

        $response->assertRedirect(route('login.two-factor'));

        // 3. Assert user is created with premium account_type
        $this->assertDatabaseHas('users', [
            'email' => 'upgrade@example.com',
            'account_type' => 'premium',
        ]);
    }

    public function test_registration_with_free_tier_creates_user_with_free_account_type(): void
    {
        $response = $this->post('/register', [
            'name' => 'Free User',
            'email' => 'free@example.com',
            'country' => 'Sri Lanka',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'plan' => 'Free Tier',
            'terms' => 'on',
        ]);

        $response->assertRedirect(route('login.two-factor'));

        $this->assertDatabaseHas('users', [
            'email' => 'free@example.com',
            'account_type' => 'free',
        ]);
    }

    public function test_logged_in_user_can_upgrade_plan_via_profile_route(): void
    {
        $user = User::factory()->create([
            'account_type' => 'free',
        ]);

        $response = $this->actingAs($user)
            ->post(route('profile.upgradePlan'), [
                'plan_name' => 'Premium Plus',
            ]);

        $response->assertRedirect();
        $this->assertEquals('premium', $user->fresh()->account_type);
    }

    public function test_upgrade_plan_requires_valid_plan_name(): void
    {
        $user = User::factory()->create([
            'account_type' => 'free',
        ]);

        $response = $this->actingAs($user)
            ->post(route('profile.upgradePlan'), [
                'plan_name' => 'Super Platinum Ultra Plus',
            ]);

        $response->assertSessionHasErrors(['plan_name']);
        $this->assertEquals('free', $user->fresh()->account_type);
    }
}
