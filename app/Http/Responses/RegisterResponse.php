<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $user = Auth::user();

        if ($user) {
            // Generate random 6-digit PIN code
            $code = rand(100000, 999999);

            // Log out the user immediately to block direct access
            Auth::logout();

            // Clean up session and regenerate CSRF token to prevent session corruption and 419 issues
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Put verification payload in the fresh new session
            $request->session()->put('login.email_2fa', [
                'user_id' => $user->id,
                'code' => $code,
                'remember' => false,
                'expires_at' => now()->addMinutes(10)->timestamp,
            ]);

            // Dispatch PIN code email to log/email system
            Mail::raw(
                "Your PayPatch registration verification PIN is: {$code}. This code expires in 10 minutes.",
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('PayPatch Registration Verification PIN');
                }
            );
        }

        return redirect()->route('login.two-factor');
    }
}
