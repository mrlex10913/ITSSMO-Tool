<?php

namespace App\Http\Middleware;

use App\Models\Roles;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        // Get role directly from database using role_id
        if (!$user->role_id) {
            abort(403, 'No role assigned');
        }

        $roleRecord = Roles::find($user->role_id);

        if (!$roleRecord) {
            abort(403, 'Invalid role assigned');
        }

        // Get role slug for comparison
        $userRole = strtolower($roleRecord->slug);

        // Check if user's role is in the allowed roles
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized - Role: ' . $roleRecord->name . ' not allowed');

    }
}
