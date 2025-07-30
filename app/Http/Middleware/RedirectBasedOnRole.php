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

        $user = $request->user();

        // Check if user has any of the required roles using the existing hasRole method
        if ($user->hasRole($roles)) {
            return $next($request);
        }

        // Log unauthorized access attempt for security monitoring
        Log::warning('Unauthorized access attempt', [
            'user_id' => $user->getAuthIdentifier(),
            'user_role' => $user->role?->slug ?? 'no_role',
            'required_roles' => $roles,
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
        ]);

        // Return 404 for unauthorized roles to avoid revealing route existence
        abort(404);
    }
}
