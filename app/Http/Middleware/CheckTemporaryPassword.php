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
      if(Auth::check()){
        $user = Auth::user();

        if($user && !$user->is_temporary_password_used && !$request->is('password/change')){
            return redirect()->route('password.change');
        }
      }
        return $next($request);
    }
}
