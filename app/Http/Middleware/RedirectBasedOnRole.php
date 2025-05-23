<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Not authenticated
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Get user role and convert to lowercase
        $userRole = strtolower($request->user()->role);

        // Check if user role is in allowed roles
        if (in_array($userRole, array_map('strtolower', $roles))) {
            return $next($request);
        }

        // Return 404 for unauthorized roles
        abort(404);
    }
}
