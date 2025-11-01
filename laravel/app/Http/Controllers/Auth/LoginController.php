<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Login Controller
 * Handles user authentication
 */
class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        // Check if user exists and get their status
        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            // Check if user is banned/inactive
            if ($user->status !== 'active') {
                return redirect()->route('auth.banned')
                    ->withInput($request->only('email'))
                    ->with('ban_reason', $user->ban_reason)
                    ->with('user_email', $user->email);
            }

            // Verify password before attempting auth
            if (Hash::check($credentials['password'], $user->password_hash)) {
                // User is active and password is correct, proceed with login
                Auth::login($user, $remember);
                $request->session()->regenerate();

                // Update last login
                $user->updateLastLogin();

                return redirect()->intended(route('dashboard'))
                    ->with('success', 'Welcome back, ' . $user->first_name . '!');
            }
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Log the user out
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'You have been logged out successfully.');
    }
}
