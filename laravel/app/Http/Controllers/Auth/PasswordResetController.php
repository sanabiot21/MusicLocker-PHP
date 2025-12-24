<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use App\Models\User;

/**
 * Password Reset Controller
 * Handles password reset functionality
 */
class PasswordResetController extends Controller
{
    /**
     * Show the forgot password form
     */
    public function showForgotForm(Request $request)
    {
        $approvedRequest = null;
        $pendingMessage = null;

        if ($request->filled('email')) {
            $user = User::where('email', $request->email)->first();

            if ($user && $user->reset_token) {
                $approvedRequest = [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'token' => $user->reset_token,
                ];
            } elseif ($user && $user->reset_requested_at) {
                $pendingMessage = 'Your request is awaiting admin approval. We will notify you once it is approved.';
            }
        }

        return view('auth.forgot-password', [
            'approvedRequest' => $approvedRequest,
            'pendingMessage' => $pendingMessage
        ]);
    }

    /**
     * Send a password reset link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['We could not find a user with that email address.'],
            ]);
        }

        // If already approved, direct the user to the reset form
        if ($user->reset_token) {
            return redirect()->route('password.reset', ['token' => $user->reset_token, 'email' => $user->email])
                ->with('info', 'Your reset was already approved. Set your new password below.');
        }

        // If a request is pending, avoid duplicate submissions
        if ($user->reset_requested_at && !$user->reset_token) {
            return back()->with('info', 'A reset request is already pending admin approval.');
        }

        $user->requestPasswordReset();

        return back()->with('success', 'Request submitted. An administrator will approve your reset.');
    }

    /**
     * Show the password reset form
     */
    public function showResetForm(Request $request, $token)
    {
        $user = User::where('reset_token', $token)->first();

        if (!$user || !$user->reset_token) {
            return redirect()->route('password.request')
                ->with('error', 'Invalid or expired reset token.');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email ?? $user->email
        ]);
    }

    /**
     * Reset the password
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $user = User::where('email', $request->email)
            ->where('reset_token', $request->token)
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Invalid reset token or email.'],
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($request->password)
        ])->save();

        $user->clearPasswordResetRequest();

        return redirect()->route('login')
            ->with('success', 'Password reset successfully! You can now login.');
    }
}
