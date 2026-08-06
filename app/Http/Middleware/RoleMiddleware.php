<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            Log::info('RoleMiddleware: User not authenticated, redirecting to login');
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        Log::info('RoleMiddleware: User ' . $user->email . ' with role ' . $user->role . ' trying to access route');
        Log::info('RoleMiddleware: Required roles: ' . implode(', ', $roles));

        // Check if user has any of the allowed roles
        foreach ($roles as $role) {
            if ($user->role === $role) {
                Log::info('RoleMiddleware: ✅ Access granted for ' . $user->role);
                return $next($request);
            }
        }

        // If no matching role, abort with 403
        Log::warning('RoleMiddleware: ❌ Access denied for ' . $user->email . ' (role: ' . $user->role . ')');
        abort(403, 'Unauthorized access. You need to be a ' . implode(' or ', $roles) . ' to access this page.');
    }
}