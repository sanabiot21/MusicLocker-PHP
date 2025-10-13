<?php

namespace MusicLocker\Controllers;

use MusicLocker\Models\User;
use Exception;

/**
 * Authentication Controller
 * Handles user registration, login, logout, and password reset
 */
class AuthController extends BaseController
{
    private User $userModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }
    
    /**
     * Show login form
     */
    public function showLogin(): void
    {
        // Redirect if already logged in
        if (is_logged_in()) {
            $this->redirect(route_url('dashboard'));
            return;
        }
        
        $this->setTitle('Login');
        $this->view('auth.login');
    }
    
    /**
     * Process login form
     */
    public function login(): void
    {
        if (!$this->isMethod('POST') || !$this->validateCSRF()) {
            $this->redirect(route_url('login'));
            return;
        }
        
        $email = trim($this->input('email') ?? '');
        $password = $this->input('password') ?? '';
        $rememberMe = $this->input('rememberMe') === 'on';
        
        // Detailed validation with per-field messages
        $errors = [];
        
        if (empty($email)) {
            $errors['email'] = 'Email address is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        }
        
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        }
        
        if (!empty($errors)) {
            $errorCount = count($errors);
            $summaryMessage = $errorCount === 1 
                ? 'Please fix the error below to continue.' 
                : "Please fix the {$errorCount} errors below to continue.";
            
            flash('error', $summaryMessage);
            flash('validation_errors', $errors);
            flash('old_input', ['email' => $email]);
            $this->redirectBack();
            return;
        }
        
        try {
            $user = $this->userModel->authenticate($email, $password);
            
            if ($user) {
                // Check if account is active
                if ($user['status'] !== 'active') {
                    flash('error', 'Your account is currently on hold. Please contact support for assistance.');
                    flash('old_input', ['email' => $email]);
                    $this->redirectBack();
                    return;
                }
                
                // Invalidate any existing sessions for this user (single session enforcement)
                $this->invalidateUserSessions($user['id']);
                
                // Start user session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_role'] = $user['role'] ?? 'user';
                
                // Create session record
                $this->createSessionRecord($user['id'], $rememberMe);
                
                // Set extended session if remember me is checked
                if ($rememberMe) {
                    session_set_cookie_params(30 * 24 * 60 * 60); // 30 days
                }
                
                // Log activity
                log_activity('login', 'user', $user['id'], 'User logged in successfully');
                
                flash('success', 'Welcome back, ' . $user['first_name'] . '!');

                // Redirect admins to admin panel, regular users to dashboard
                if (!empty($_SESSION['intended_url'])) {
                    $redirectUrl = $_SESSION['intended_url'];
                    unset($_SESSION['intended_url']);
                } else {
                    $redirectUrl = is_admin() ? '/admin' : route_url('dashboard');
                }

                $this->redirect($redirectUrl);
                
            } else {
                // Authentication failed - provide helpful message without revealing which field is wrong
                flash('error', 'Invalid email or password. Please check your credentials and try again.');
                flash('validation_errors', [
                    'email' => 'Invalid email or password',
                    'password' => 'Invalid email or password'
                ]);
                flash('old_input', ['email' => $email]);
                $this->redirectBack();
            }
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            flash('error', 'Login failed. Please try again later.');
            flash('validation_errors', ['email' => 'An error occurred. Please try again.']);
            flash('old_input', ['email' => $email]);
            $this->redirectBack();
        }
    }
    
    /**
     * Show registration form
     */
    public function showRegister(): void
    {
        // Redirect if already logged in
        if (is_logged_in()) {
            $this->redirect(route_url('dashboard'));
            return;
        }
        
        $this->setTitle('Register');
        $this->view('auth.register');
    }
    
    /**
     * Process registration form
     */
    public function register(): void
    {
        if (!$this->isMethod('POST') || !$this->validateCSRF()) {
            $this->redirect(route_url('register'));
            return;
        }
        
        $userData = [
            'first_name' => trim($this->input('first_name') ?? ''),
            'last_name' => trim($this->input('last_name') ?? ''),
            'email' => trim($this->input('email') ?? ''),
            'password' => $this->input('password') ?? '',
        ];
        
        $errors = [];
        
        // Validate first name
        if (empty($userData['first_name'])) {
            $errors['first_name'] = 'First name is required';
        } elseif (strlen($userData['first_name']) < 2) {
            $errors['first_name'] = 'First name must be at least 2 characters';
        } elseif (strlen($userData['first_name']) > 50) {
            $errors['first_name'] = 'First name is too long (max 50 characters)';
        }
        
        // Validate last name
        if (empty($userData['last_name'])) {
            $errors['last_name'] = 'Last name is required';
        } elseif (strlen($userData['last_name']) < 2) {
            $errors['last_name'] = 'Last name must be at least 2 characters';
        } elseif (strlen($userData['last_name']) > 50) {
            $errors['last_name'] = 'Last name is too long (max 50 characters)';
        }
        
        // Validate email
        if (empty($userData['email'])) {
            $errors['email'] = 'Email address is required';
        } elseif (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        } elseif (strlen($userData['email']) > 255) {
            $errors['email'] = 'Email address is too long';
        } elseif ($this->userModel->emailExists($userData['email'])) {
            $errors['email'] = 'This email address is already registered. Please use a different email or try logging in.';
        }
        
        // Validate password
        if (empty($userData['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($userData['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters long';
        } elseif (strlen($userData['password']) > 255) {
            $errors['password'] = 'Password is too long (max 255 characters)';
        }
        
        // Check password confirmation
        $confirmPassword = $this->input('confirm_password') ?? '';
        if (empty($confirmPassword)) {
            $errors['confirm_password'] = 'Please confirm your password';
        } elseif ($userData['password'] !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match. Please make sure both passwords are identical.';
        }
        
        // Enforce Terms of Service / Privacy acceptance
        $agreeTerms = $this->input('agreeTerms');
        if ($agreeTerms !== 'on' && $agreeTerms !== '1') {
            $errors['agreeTerms'] = 'You must agree to the Terms of Service and Privacy Policy to create an account';
        }

        // Handle validation errors with specific toast message
        if (!empty($errors)) {
            // Create a user-friendly summary message
            $errorCount = count($errors);
            $summaryMessage = $errorCount === 1 
                ? 'Please fix the error below to continue.' 
                : "Please fix the {$errorCount} errors below to continue.";
            
            flash('error', $summaryMessage);
            flash('validation_errors', $errors);
            flash('old_input', $userData);
            $this->redirectBack();
            return;
        }
        
        try {
            $userId = $this->userModel->create($userData);
            
            if ($userId) {
                // Log activity
                log_activity('register', 'user', $userId, 'New user account created');
                
                flash('success', 'Account created successfully! You can now log in.');
                $this->redirect(route_url('login'));
                
            } else {
                flash('error', 'Failed to create account. Please try again.');
                $this->redirectBack();
            }
            
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            flash('error', 'Registration failed: ' . $e->getMessage());
            flash('old_input', $userData);
            $this->redirectBack();
        }
    }
    
    /**
     * Show forgot password form
     */
    public function showForgotPassword(): void
    {
        $this->setTitle('Forgot Password');

        // Check if showing approved reset form
        $approvedRequest = null;
        if (isset($_GET['approved']) && isset($_GET['email'])) {
            $email = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL);
            if ($email) {
                $approvedRequest = $this->userModel->hasApprovedResetRequest($email);
            }
        }

        $this->addData('approvedRequest', $approvedRequest);
        $this->view('auth.forgot');
    }
    
    /**
     * Process forgot password form
     */
    public function forgotPassword(): void
    {
        if (!$this->isMethod('POST') || !$this->validateCSRF()) {
            $this->redirect(route_url('login'));
            return;
        }

        $email = $this->input('email');
        $newPassword = $this->input('new_password');
        $userId = $this->input('user_id');

        // Case 1: User completing approved reset
        if ($newPassword && $userId) {
            $this->completePasswordReset((int)$userId, $newPassword);
            return;
        }

        // Case 2: Check status or submit new request
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please provide a valid email address.');
            $this->redirectBack();
            return;
        }

        try {
            // First, check if there's an approved request
            $approvedRequest = $this->userModel->hasApprovedResetRequest($email);

            if ($approvedRequest) {
                // Redirect to show reset form
                flash('success', 'Your reset was approved! Set your new password below.');
                $this->redirect(route_url('forgot') . '?email=' . urlencode($email) . '&approved=1');
                return;
            }

            // Check if there's a pending request
            $pendingRequest = $this->userModel->hasPendingResetRequest($email);

            if ($pendingRequest) {
                flash('info', 'Your request is pending admin approval. Please wait or contact admin.');
                $this->redirectBack();
                return;
            }

            // No existing request - create new one
            $user = $this->userModel->findByEmail($email);

            if ($user) {
                // Log password reset request for admin notification
                $this->userModel->logPasswordResetRequest($user['id'], $email);

                flash('success', 'Request submitted! An admin will approve it soon. Come back here and enter your email to check status.');
            } else {
                // Don't reveal if email exists or not (security)
                flash('success', 'Request submitted! An admin will approve it soon. Come back here and enter your email to check status.');
            }

            $this->redirectBack();
            
        } catch (Exception $e) {
            error_log("Forgot password error: " . $e->getMessage());
            flash('error', 'Unable to process request. Please try again.');
            $this->redirectBack();
        }
    }
    
    /**
     * Complete password reset (after admin approval)
     */
    private function completePasswordReset(int $userId, string $newPassword): void
    {
        $confirmPassword = $this->input('confirm_password');

        if (strlen($newPassword) < 8) {
            flash('error', 'Password must be at least 8 characters.');
            $this->redirectBack();
            return;
        }

        if ($newPassword !== $confirmPassword) {
            flash('error', 'Passwords do not match.');
            $this->redirectBack();
            return;
        }

        try {
            if ($this->userModel->adminResetPassword($userId, $newPassword)) {
                // Clear the approved request
                $this->userModel->clearPasswordResetRequest($userId);

                flash('success', 'Password reset successfully! You can now login with your new password.');
                $this->redirect(route_url('login'));
            } else {
                flash('error', 'Failed to reset password. Please try again.');
                $this->redirectBack();
            }
        } catch (Exception $e) {
            flash('error', 'An error occurred. Please try again.');
            $this->redirectBack();
        }
    }

    /**
     * Show reset password form
     */
    public function showResetPassword(): void
    {
        $token = $this->input('token');
        
        if (empty($token)) {
            flash('error', 'Invalid reset token.');
            $this->redirect(route_url('login'));
            return;
        }
        
        $this->setTitle('Reset Password');
        $this->addData('reset_token', $token);
        $this->view('auth.reset');
    }
    
    /**
     * Process reset password form
     */
    public function resetPassword(): void
    {
        if (!$this->isMethod('POST') || !$this->validateCSRF()) {
            $this->redirect(route_url('login'));
            return;
        }
        
        $token = $this->input('token');
        $password = $this->input('password');
        $confirmPassword = $this->input('confirm_password');
        
        // Validate inputs
        if (empty($token) || empty($password) || empty($confirmPassword)) {
            flash('error', 'All fields are required.');
            $this->redirectBack();
            return;
        }
        
        if ($password !== $confirmPassword) {
            flash('error', 'Password confirmation does not match.');
            $this->redirectBack();
            return;
        }
        
        if (strlen($password) < 8) {
            flash('error', 'Password must be at least 8 characters long.');
            $this->redirectBack();
            return;
        }
        
        try {
            if ($this->userModel->resetPassword($token, $password)) {
                flash('success', 'Password reset successfully! You can now log in.');
                
                // Log activity
                log_activity('password_reset_complete', 'user', null, 'Password reset completed');
                
                $this->redirect(route_url('login'));
            } else {
                flash('error', 'Invalid or expired reset token.');
                $this->redirect(route_url('login'));
            }
            
        } catch (Exception $e) {
            error_log("Reset password error: " . $e->getMessage());
            flash('error', 'Failed to reset password. Please try again.');
            $this->redirectBack();
        }
    }
    
    /**
     * Logout user
     */
    public function logout(): void
    {
        if (is_logged_in()) {
            // Log activity before destroying session
            log_activity('logout', 'user', current_user_id(), 'User logged out');
            
            // Invalidate session in database
            $this->invalidateCurrentSession();
        }
        
        // Destroy session
        session_destroy();
        session_start();
        
        flash('success', 'You have been logged out successfully.');
        $this->redirect(route_url('home'));
    }
    
    /**
     * Invalidate current session in database
     */
    private function invalidateCurrentSession(): void
    {
        try {
            $db = \MusicLocker\Services\Database::getInstance();
            $sessionId = session_id();
            $db->execute(
                "UPDATE user_sessions SET is_active = FALSE WHERE id = ?",
                [$sessionId]
            );
        } catch (Exception $e) {
            error_log("Failed to invalidate current session: " . $e->getMessage());
        }
    }
    
    /**
     * Show user profile
     */
    public function showProfile(): void
    {
        $this->requireAuth();
        
        $user = $this->userModel->findById(current_user_id());
        $userStats = $this->userModel->getUserStats(current_user_id());
        
        if (!$user) {
            flash('error', 'User not found.');
            $this->redirect(route_url('dashboard'));
            return;
        }
        
        $this->setTitle('Profile');
        $this->addData('user', $user);
        $this->addData('userStats', $userStats);
        $this->view('auth.profile');
    }
    
    /**
     * Update user profile
     */
    public function updateProfile(): void
    {
        $this->requireAuth();
        
        if (!$this->isMethod('POST') || !$this->validateCSRF()) {
            $this->redirect(route_url('profile'));
            return;
        }
        
        $profileData = [
            'first_name' => $this->input('first_name'),
            'last_name' => $this->input('last_name'),
            'email' => $this->input('email'),
        ];
        
        // Validate input
        $errors = $this->userModel->validate($profileData, [
            'first_name' => true,
            'last_name' => true,
            'email' => false // Don't check uniqueness for existing user
        ]);
        
        // Check email uniqueness manually (excluding current user)
        if (!empty($profileData['email'])) {
            $existingUser = $this->userModel->findByEmail($profileData['email']);
            if ($existingUser && $existingUser['id'] != current_user_id()) {
                $errors['email'] = 'This email address is already registered';
            }
        }
        
        if (!empty($errors)) {
            $this->handleValidationErrors($errors);
            return;
        }
        
        try {
            if ($this->userModel->updateProfile(current_user_id(), $profileData)) {
                // Update session data (keep role unchanged)
                $_SESSION['user_email'] = $profileData['email'];
                $_SESSION['user_name'] = $profileData['first_name'] . ' ' . $profileData['last_name'];
                
                log_activity('profile_update', 'user', current_user_id(), 'Profile information updated');
                
                flash('success', 'Profile updated successfully!');
            } else {
                flash('error', 'Failed to update profile.');
            }
            
        } catch (Exception $e) {
            error_log("Profile update error: " . $e->getMessage());
            flash('error', 'Failed to update profile: ' . $e->getMessage());
        }
        
        $this->redirect(route_url('profile'));
    }
    
    /**
     * Change user password
     */
    public function changePassword(): void
    {
        $this->requireAuth();
        
        if (!$this->isMethod('POST') || !$this->validateCSRF()) {
            $this->redirect(route_url('profile'));
            return;
        }
        
        $currentPassword = $this->input('current_password');
        $newPassword = $this->input('new_password');
        $confirmPassword = $this->input('confirm_password');
        
        // Basic validation
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            flash('error', 'All password fields are required.');
            $this->redirectBack();
            return;
        }
        
        if ($newPassword !== $confirmPassword) {
            flash('error', 'New password confirmation does not match.');
            $this->redirectBack();
            return;
        }
        
        if (strlen($newPassword) < 8) {
            flash('error', 'New password must be at least 8 characters long.');
            $this->redirectBack();
            return;
        }
        
        try {
            if ($this->userModel->changePassword(current_user_id(), $currentPassword, $newPassword)) {
                log_activity('password_change', 'user', current_user_id(), 'Password changed successfully');
                
                flash('success', 'Password changed successfully!');
            } else {
                flash('error', 'Failed to change password. Please check your current password.');
            }
            
        } catch (Exception $e) {
            error_log("Password change error: " . $e->getMessage());
            flash('error', 'Failed to change password: ' . $e->getMessage());
        }
        
        $this->redirect(route_url('profile'));
    }
    
    /**
     * Invalidate all existing sessions for a user (single session enforcement)
     */
    private function invalidateUserSessions(int $userId): void
    {
        try {
            $db = \MusicLocker\Services\Database::getInstance();
            $db->execute(
                "UPDATE user_sessions SET is_active = FALSE WHERE user_id = ? AND is_active = TRUE",
                [$userId]
            );
        } catch (Exception $e) {
            error_log("Failed to invalidate user sessions: " . $e->getMessage());
        }
    }
    
    /**
     * Create a session record in the database
     */
    private function createSessionRecord(int $userId, bool $rememberMe = false): void
    {
        try {
            $db = \MusicLocker\Services\Database::getInstance();
            $sessionId = session_id();
            $expiresAt = $rememberMe 
                ? date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)) // 30 days
                : date('Y-m-d H:i:s', time() + 3600); // 1 hour
            
            $db->execute(
                "INSERT INTO user_sessions (id, user_id, ip_address, user_agent, csrf_token, expires_at, is_active) 
                 VALUES (?, ?, ?, ?, ?, ?, TRUE)",
                [
                    $sessionId,
                    $userId,
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null,
                    csrf_token(),
                    $expiresAt
                ]
            );
        } catch (Exception $e) {
            error_log("Failed to create session record: " . $e->getMessage());
        }
    }
}