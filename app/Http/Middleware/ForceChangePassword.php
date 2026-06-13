<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ForceChangePassword
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Check if must_change_password is true (or 1)
            if ($user->must_change_password) {
                // Allow access to edit profile, update password, and logout
                $allowedRouteNames = [
                    'profile.edit',
                    'profile.update',
                    'profile.update-avatar',
                    'password.update',
                    'logout'
                ];

                $currentRouteName = $request->route() ? $request->route()->getName() : null;

                if (!in_array($currentRouteName, $allowedRouteNames)) {
                    return redirect()->route('profile.edit')
                        ->with('error', 'Keamanan Sandi: Demi keamanan akun Anda, silakan ubah password default Anda terlebih dahulu sebelum menggunakan sistem.');
                }
            }
        }

        return $next($request);
    }
}
