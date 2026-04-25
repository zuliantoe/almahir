<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckPermission Middleware
 * 
 * Verify that the authenticated user has the required permission(s).
 */
class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!Auth::check()) {
            abort(401, 'Unauthenticated.');
        }

        $user = Auth::user();
        
        // Loop array of requested permissions. If user has any of them (or if we want them to have ALL).
        // Standard is: user needs at least ONE of the piped permissions. OR we check individually.
        // For standard Spatie-like `permission:edit,delete` logic, usually it means ANY of them.
        // Let's implement ANY of the provided permissions.
        $hasAccess = false;
        foreach ($permissions as $perm) {
            if ($user->hasPermission($perm)) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            abort(403, 'Unauthorized action. Missing required permissions.');
        }

        return $next($request);
    }
}
