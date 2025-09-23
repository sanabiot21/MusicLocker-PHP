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
        
        $email = $this->input('email');
        $password = $this->input('password');
        $rememberMe = $this->input('rememberMe') === 'on';
        
        // Basic validation
        if (empty($email) || empty($password)) {
            flash('error', 'Please provide both email and password.');
            $this->redirectBack();
            return;
        }
        
        try {
            $user = $this->userModel->authenticate($email, $password);
            
            if ($user) {
                // Start user session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                
                // Set extended session if remember me is checked
                if ($rememberMe) {
                    session_set_cookie_params(30 * 24 * 60 * 60); // 30 days
                }
                
                // Log activity
                log_activity('login', 'user', $user['id'], 'User logged in successfully');
                
                flash('success', 'Welcome back, ' . $user['first_name'] . '!');
                
                // Redirect to intended page or dashboard
                $redirectUrl = $_SESSION['intended_url'] ?? route_url('dashboard');
                unset($_SESSION['intended_url']);
                
                $this->redirect($redirectUrl);
                
            } else {
                flash('error', 'Invalid email or password.');
                $this->redirectBack();
            }
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            flash('error', 'Login failed. Please try again.');
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
            'first_name' => $this->input('first_name'),
            'last_name' => $this->input('last_name'),
            'email' => $this->input('email'),
            'password' => $this->input('password'),
        ];
        
        // Validate input
        $errors = $this->userModel->validate($userData, [
            'first_name' => true,
            'last_name' => true,
            'email' => true,
            'password' => true
        ]);
        
        // Check password confirmation
        $confirmPassword = $this->input('confirm_password');
        if (empty($confirmPassword)) {
            $errors['confirm_password'] = 'Password confirmation is required';
        } elseif ($userData['password'] !== $confirmPassword) {
            $errors['confirm_password'] = 'Password confirmation does not match';
        }
        
        // Handle validation errors
        if (!empty($errors)) {
            flash('error', 'Please correct the errors below.');
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
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please provide a valid email address.');
            $this->redirectBack();
            return;
        }
        
        try {
            $token = $this->userModel->generateResetToken($email);
            
            if ($token) {
                // TODO: Send reset email
                // For now, we'll just show the token (development only)
                if (config('app.env') === 'development') {
                    flash('info', 'Reset token: ' . $token . ' (development mode)');
                } else {
                    flash('success', 'If an account with that email exists, you will receive a password reset link.');
                }
                
                // Log activity
                log_activity('password_reset_request', 'user', null, "Password reset requested for email: $email");
            } else {
                // Don't reveal if email exists or not
                flash('success', 'If an account with that email exists, you will receive a password reset link.');
            }
            
            $this->redirect(route_url('login'));
            
        } catch (Exception $e) {
            error_log("Forgot password error: " . $e->getMessage());
            flash('error', 'Unable to process request. Please try again.');
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
        }
        
        // Destroy session
        session_destroy();
        session_start();
        
        flash('success', 'You have been logged out successfully.');
        $this->redirect(route_url('home'));
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
                // Update session data
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
}