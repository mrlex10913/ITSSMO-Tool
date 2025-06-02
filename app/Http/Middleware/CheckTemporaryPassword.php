<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTemporaryPassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
    //   if(Auth::check()){
    //     $user = Auth::user();

    //     if($user && !$user->is_temporary_password_used && !$request->is('password/change')){
    //         return redirect()->route('password.change');
    //     }
    //   }
    if(Auth::check()){
            $user = Auth::user();

            if($user && !$user->is_temporary_password_used) {
                // Check if it's an AJAX request or API call
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json(['message' => 'Password change required'], 403);
                }

                // For password change related routes, allow access
                if($request->is('password/change') ||
                   $request->is('user/password') ||
                   $request->routeIs('password.*') ||
                   $request->routeIs('user-password.*') ||
                   $request->routeIs('logout')) {
                    return $next($request);
                }

                // Set session flag for modal display
                $request->session()->put('force_password_change', true);
            }
        }
        return $next($request);
    }
}
