<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Fortify\Fortify;

class RedirectIfEmailTwoFactorAuthenticatable
{
    /**
     * Handle the incoming request and intercept successful credential checks.
     */
    public function handle(Request $request, $next)
    {
        $request->validate([
            Fortify::username() => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->input(Fortify::username()))->first();

        // If credentials are valid, generate and send Email PIN code, then intercept login
        if ($user && Hash::check($request->password, $user->password)) {
            
            // Safety: Check if user is banned
            if ($user->status === 'banned') {
                return redirect()->route('login')->withErrors([
                    Fortify::username() => 'This account has been banned.',
                ]);
            }

            // Only require 2FA email PIN code if user is not verified yet (newly signed up)
            if (is_null($user->email_verified_at)) {
                // Generate random 6-digit PIN code
                $code = rand(100000, 999999);

                // Store verification credentials in session
                session()->put('login.email_2fa', [
                    'user_id' => $user->id,
                    'code' => $code,
                    'remember' => $request->boolean('remember'),
                    'expires_at' => now()->addMinutes(10)->timestamp,
                ]);

                // Dispatch verification PIN code email
                Mail::raw(
                    "Your PayPatch login verification PIN is: {$code}. This code expires in 10 minutes.",
                    function ($message) use ($user) {
                        $message->to($user->email)
                                ->subject('PayPatch Login Verification PIN');
                    }
                );

                // Redirect user to input verification code
                return redirect()->route('login.two-factor');
            }
        }

        return $next($request);
    }
}
