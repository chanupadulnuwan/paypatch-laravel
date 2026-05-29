<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailTwoFactorController extends Controller
{
    /**
     * Show the 2FA verification challenge form.
     */
    public function show()
    {
        if (!session()->has('login.email_2fa')) {
            return redirect()->route('login');
        }

        return view('auth.email-two-factor');
    }

    /**
     * Verify the user-submitted 6-digit verification code.
     */
    public function verify(Request $request)
    {
        $sessionData = session()->get('login.email_2fa');

        if (!$sessionData) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        // Check if code has expired
        if (now()->timestamp > $sessionData['expires_at']) {
            session()->forget('login.email_2fa');
            return redirect()->route('login')->withErrors([
                'email' => 'The login verification code has expired. Please try again.',
            ]);
        }

        // Validate code matches
        if (trim($request->code) !== (string) $sessionData['code']) {
            return back()->withErrors([
                'code' => 'The verification PIN code entered is incorrect.',
            ]);
        }

        // Login user
        $user = User::find($sessionData['user_id']);
        if ($user) {
            Auth::login($user, $sessionData['remember']);

            // Record sign-in timestamp and mark email as verified!
            $user->update([
                'last_login_at' => now(),
                'email_verified_at' => now(),
            ]);

            session()->forget('login.email_2fa');

            // Redirect based on role
            return $user->role === 'admin' 
                ? redirect()->route('admin') 
                : redirect()->intended(route('dashboard'));
        }

        return redirect()->route('login');
    }
}
