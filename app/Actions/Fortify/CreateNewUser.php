<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'country' => ['required', 'string', 'max:255'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'plan' => ['nullable', 'string', 'in:Free Tier,Premium,Premium Plus'],
        ])->validate();

        $accountType = 'free';
        if (isset($input['plan']) && ($input['plan'] === 'Premium' || $input['plan'] === 'Premium Plus')) {
            $accountType = 'premium';
        }

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'country' => $input['country'],
            'password' => Hash::make($input['password']),
            'account_type' => $accountType,
        ]);

        // Auto-enroll new user in default seeded groups to supply previous user data
        try {
            $defaultGroups = \App\Models\Group::whereIn('name', ['Ella Trip 2026', 'Apartment 303'])->get();
            foreach ($defaultGroups as $group) {
                if (!$group->members()->where('users.id', $user->id)->exists()) {
                    $group->members()->attach($user->id, ['joined_at' => now()]);
                    $group->forgetMembersCache();
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to auto-enroll new user in default groups: " . $e->getMessage());
        }

        return $user;
    }
}
