<?php

namespace Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * AuthController
 *
 * Handles authentication operations (login, logout)
 */
class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        return view('auth::login');
    }

    /**
     * Process login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        $login_type = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $login_type => $request->login,
            'password' => $request->password,
        ];

        $remember = $request->boolean('remember');

        // Log the credentials attempt
        \Log::info('Login Attempt', [
            'login' => $request->login,
            'password_length' => strlen($request->password),
            'password_value' => $request->password,
        ]);

        $user = \App\Models\User::where($login_type, $request->login)->first();
        if ($user) {
            \Log::info('User Found', [
                'email' => $user->email,
                'db_password' => $user->password,
                'is_hashed' => \Illuminate\Support\Facades\Hash::isHashed($user->password),
                'check_result' => \Illuminate\Support\Facades\Hash::check($request->password, $user->password)
            ]);
        } else {
            \Log::info('User Not Found in DB');
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Update last login timestamp
            Auth::user()->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // Redirect based on user role
            if (Auth::user()->hasRole('WALI_MURID')) {
                return redirect()->intended(route('walimurid.portal.dashboard'));
            }

            if (Auth::user()->hasRole('SUPER_ADMIN')) {
                return redirect()->intended(route('dashboard'));
            }
            
            if (Auth::user()->hasRole('GURU')) {
                return redirect()->intended(route('dashboard'));
            }
            
            if (Auth::user()->hasRole('SISWA')) {
                return redirect()->intended(route('dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'login' => __('The provided credentials do not match our records.'),
        ]);
    }

    /**
     * Logout the user
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
