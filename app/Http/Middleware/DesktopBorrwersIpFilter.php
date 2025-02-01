<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DesktopBorrwersIpFilter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // dd('IpFilter middleware is being executed.', 'Request IP: ' . $request->ip());
        $filteredIps = [
            '127.0.0.1',
            '192.168.0.102'
        ];
        if(in_array($request->ip(), $filteredIps)){
            // dd('my ip is filtered');
            if(!$request->routeIs('desktop.borrowers'))
            return redirect()->route('desktop.borrowers');
        }else{
            if (!$request->routeIs('dashboard')) {
                // dd('test');
                return redirect()->route('dashboard');
            }
        }
        // if(!in_array($request->ip(), $filteredIps)){
        //     abort(404);
        // }
        return $next($request);

    }
}
