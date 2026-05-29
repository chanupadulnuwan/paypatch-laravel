<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// EnsureAdmin middleware
// Blocks access to admin routes for non-admin users.
// Applied to the /admin route group in web.php.

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // If the user is not logged in OR does not have the admin role, redirect away
        if (!$request->user() || $request->user()->role !== 'admin') {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access that page.');
        }

        return $next($request);
    }
}
