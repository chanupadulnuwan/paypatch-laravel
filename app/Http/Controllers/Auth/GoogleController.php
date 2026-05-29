<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google and log them in.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleGoogleCallback()
    {
        try {
            if (config('app.env') === 'local') {
                $guzzleClient = new \GuzzleHttp\Client([
                    'verify' => false, // Bypass SSL certificate verification in local XAMPP
                ]);
                Socialite::driver('google')->setHttpClient($guzzleClient);
            }

            // Use stateless() to bypass state verification (resolves InvalidStateException on local sessions)
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            // 1. Attempt to find user by google_id
            $user = User::where('google_id', $googleUser->id)->first();
            
            if (!$user) {
                // 2. Fallback: Find user by email and bind google_id
                $user = User::where('email', $googleUser->email)->first();
                
                if ($user) {
                    $user->update(['google_id' => $googleUser->id]);
                } else {
                    // 3. Register a new user dynamically
                    $user = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'password' => null, // No password needed for OAuth-only users
                        'country' => 'United States', // Default fallback country
                        'role' => 'user',
                        'status' => 'active',
                        'account_type' => 'free',
                    ]);
                }
            }

            // Safety check: is the user banned?
            if ($user->status === 'banned') {
                return redirect()->route('home')->withErrors([
                    'email' => 'This account has been banned.',
                ]);
            }

            // Successfully authenticate user directly, bypassing local 2FA (since Google handles OAuth 2FA natively)
            Auth::login($user, true);

            // Record login timestamp
            $user->update(['last_login_at' => now()]);

            return $user->role === 'admin' 
                ? redirect()->route('admin') 
                : redirect()->route('dashboard');

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Auth Error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('home')->withErrors([
                'email' => 'Unable to sign in with Google: ' . $e->getMessage(),
            ]);
        }
    }
}
