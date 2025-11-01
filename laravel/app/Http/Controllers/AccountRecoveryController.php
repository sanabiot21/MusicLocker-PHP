<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\AccountRecoveryRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AccountRecoveryController extends Controller
{
    /**
     * Show the banned account page
     */
    public function showBanned()
    {
        return view('auth.banned');
    }

    /**
     * Show the account recovery form
     */
    public function showRecoveryForm(Request $request)
    {
        $email = $request->get('email', session('user_email'));
        return view('auth.account-recovery', ['email' => $email]);
    }

    /**
     * Handle account recovery request submission
     */
    public function submitRecoveryRequest(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'message' => 'required|string|min:10|max:1000',
        ]);

        // Verify user exists and is banned
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No account found with this email address.');
        }

        if ($user->status === 'active') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Your account is active. Please try logging in.');
        }

        // Get admin email (from config or first admin user)
        $adminUsers = User::admins()->get();
        $adminEmail = $adminUsers->isNotEmpty() ? $adminUsers->first()->email : config('mail.from.address', 'admin@musiclocker.local');

        try {
            // Send email to admin
            Mail::to($adminEmail)->send(new AccountRecoveryRequest(
                $user,
                $validated['message']
            ));

            return redirect()->route('auth.account-recovery')
                ->with('success', 'Your account recovery request has been sent to the administrator. We will review your request and respond soon.');
        } catch (\Exception $e) {
            Log::error('Failed to send account recovery email: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to send recovery request. Please try again later.');
        }
    }
}
