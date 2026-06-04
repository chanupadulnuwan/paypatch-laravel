<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. Basic validation
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255|unique:users,email,' . $user->id,
            'country' => 'nullable|string|max:100',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. Handle profile details update
        $user->name = $request->name;
        $user->email = $request->email;
        $user->country = $request->country;

        // 3. Handle avatar upload (custom public storage for bulletproof Windows rendering)
        if ($request->hasFile('profile_photo')) {
            $photo = $request->file('profile_photo');
            $dir = public_path('uploads/avatars');
            
            // Create directory if not exists
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            // Delete old custom photo if exists
            if ($user->profile_photo_path && File::exists(public_path($user->profile_photo_path))) {
                File::delete(public_path($user->profile_photo_path));
            }

            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $photo->move($dir, $filename);
            
            // Store public relative path in profile_photo_path
            $user->profile_photo_path = 'uploads/avatars/' . $filename;
        }

        // 4. Handle password changes if present
        if ($request->filled('new_password')) {
            $request->validate([
                'current_password' => 'required|string',
                'new_password'     => 'required|string|min:8|confirmed',
            ]);

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'The provided current password does not match our records.'])->with('modal', 'profile');
            }

            // Savehashed new password (User model mutator hashes it automatically, but let's assign it directly)
            $user->password = $request->new_password;
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!')->with('modal', 'profile');
    }

    /**
     * Upgrade or downgrade the user's subscription plan dynamically.
     */
    public function upgradePlan(Request $request)
    {
        $request->validate([
            'plan_name' => 'required|string|in:Free Tier,Premium,Premium Plus',
        ]);

        $user = Auth::user();
        $accountType = 'free';
        if ($request->plan_name === 'Premium' || $request->plan_name === 'Premium Plus') {
            $accountType = 'premium';
        }

        $user->update([
            'account_type' => $accountType,
        ]);

        // Clear dashboard cache to apply upgraded limits/features if any
        \Illuminate\Support\Facades\Cache::forget("dashboard_groups_{$user->id}");

        return redirect()->back()->with('success', 'Subscription plan updated to ' . $request->plan_name . ' successfully!')->with('modal', 'profile');
    }
}
