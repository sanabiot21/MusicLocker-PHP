<?php

namespace MusicLocker\Controllers;

use MusicLocker\Models\User;
use MusicLocker\Models\SystemSetting;
use Exception;

/**
 * Admin Controller
 * Handles admin functionality - user management and system health
 */
class AdminController extends BaseController
{
    private User $userModel;
    private SystemSetting $settingModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->settingModel = new SystemSetting();
    }
    
    /**
     * Admin Dashboard
     */
    public function dashboard(): void
    {
        $this->requireAdmin();

        $this->setTitle('Admin Dashboard - Music Locker');
        
        // Get system overview stats from database
        try {
            $userStats = [
                'total_users' => $this->userModel->getTotalUsers(),
                'active_users' => $this->userModel->getActiveUsers(),
                'new_users_today' => $this->userModel->getNewUsersToday(),
                'total_music_entries' => $this->userModel->getTotalMusicEntries()
            ];

            // Get recent activity
            $recentActivity = $this->userModel->getRecentActivity(5);

            // Get pending password reset requests
            $resetRequests = $this->userModel->getPendingResetRequests();

            // Get weekly analytics
            $weeklyStats = [
                'new_users' => $this->userModel->getWeeklyUserGrowth(),
                'new_music' => $this->userModel->getWeeklyMusicCount(),
                'most_active' => $this->userModel->getMostActiveUser(),
                'popular_tag' => $this->userModel->getPopularTag()
            ];

        } catch (Exception $e) {
            error_log("Admin dashboard stats error: " . $e->getMessage());
            flash('warning', 'Some statistics may not be available due to a database issue.');
            $userStats = [
                'total_users' => 0,
                'active_users' => 0,
                'new_users_today' => 0,
                'total_music_entries' => 0
            ];
            $recentActivity = [];
            $resetRequests = [];
            $weeklyStats = [
                'new_users' => 0,
                'new_music' => 0,
                'most_active' => null,
                'popular_tag' => null
            ];
        }

        $this->addData('userStats', $userStats);
        $this->addData('recentActivity', $recentActivity);
        $this->addData('resetRequests', $resetRequests);
        $this->addData('weeklyStats', $weeklyStats);
        $this->view('admin.dashboard');
    }
    
    /**
     * User Management List
     */
    public function userList(): void
    {
        $this->requireAdmin();

        $this->setTitle('User Management - Admin');
        
        try {
            // Get real users from database
            $users = $this->userModel->getAllUsers(50, 0);
            $this->addData('users', $users);
        } catch (Exception $e) {
            error_log("Admin user list error: " . $e->getMessage());
            flash('error', 'Failed to load users: ' . $e->getMessage());
            $this->addData('users', []);
        }
        
        $this->view('admin.users');
    }
    
    /**
     * Individual User Detail
     */
    public function userDetail(int $userId): void
    {
        $this->requireAdmin();

        $this->setTitle('User Details - Admin');
        
        try {
            // Get real user data from database
            $user = $this->userModel->findById($userId);

            if (!$user) {
                flash('error', 'User not found.');
                $this->redirect('/admin/users');
                return;
            }
            
            // Remove sensitive data
            unset($user['password_hash'], $user['verification_token'], $user['reset_token']);
            
            // Get user statistics
            $userStats = $this->userModel->getUserStats($userId);
            
            // Get recent activity
            $recentActivity = $this->userModel->getUserActivity($userId, 10);
            
            $this->addData('user', $user);
            $this->addData('userStats', $userStats);
            $this->addData('recentActivity', $recentActivity);
            
        } catch (Exception $e) {
            error_log("Admin user detail error: " . $e->getMessage());
            flash('error', 'Failed to load user details: ' . $e->getMessage());
            $this->redirect('/admin/users');
            return;
        }
        
        $this->view('admin.user-detail');
    }
    
    /**
     * System Health Dashboard
     */
    public function systemHealth(): void
    {
        $this->requireAdmin();

        $this->setTitle('System Health - Admin');
        $this->view('admin.system-health');
    }

    /**
     * Toggle user status (AJAX)
     */
    public function toggleUserStatus(): void
    {
        $this->requireAdmin();

        if (!$this->isMethod('POST') || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
            return;
        }

        $userId = (int)$this->input('user_id');

        if (!$userId) {
            $this->json(['success' => false, 'message' => 'User ID required'], 400);
            return;
        }

        try {
            if ($this->userModel->toggleStatus($userId)) {
                $user = $this->userModel->findById($userId);
                $desc = 'Toggled user status: ' . ($user['email'] ?? ('User ID ' . $userId));
                log_activity('admin_toggle_user_status', 'user', $userId, $desc);
                $this->json([
                    'success' => true,
                    'message' => 'User status updated',
                    'newStatus' => $user['status']
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to update status'], 500);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update user account (AJAX)
     */
    public function updateUser(): void
    {
        $this->requireAdmin();

        if (!$this->isMethod('POST') || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
            return;
        }

        $userId = (int)$this->input('user_id');
        $firstName = trim($this->input('first_name') ?? '');
        $lastName = trim($this->input('last_name') ?? '');
        $email = trim($this->input('email') ?? '');
        $status = trim($this->input('status') ?? '');

        if (!$userId) {
            $this->json(['success' => false, 'message' => 'User ID required'], 400);
            return;
        }

        if (empty($firstName) || empty($lastName) || empty($email)) {
            $this->json(['success' => false, 'message' => 'All fields are required'], 400);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['success' => false, 'message' => 'Invalid email address'], 400);
            return;
        }

        if (!in_array($status, ['active', 'inactive'])) {
            $this->json(['success' => false, 'message' => 'Invalid status'], 400);
            return;
        }

        try {
            // Check if email is already taken by another user
            $existingUser = $this->userModel->findByEmail($email);
            if ($existingUser && $existingUser['id'] != $userId) {
                $this->json(['success' => false, 'message' => 'Email already in use'], 400);
                return;
            }

            $updateData = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'status' => $status
            ];

            if ($this->userModel->updateUser($userId, $updateData)) {
                $desc = 'Updated user account: ' . $email;
                log_activity('admin_update_user', 'user', $userId, $desc);
                $this->json(['success' => true, 'message' => 'User updated successfully']);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to update user'], 500);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete user (AJAX)
     */
    public function deleteUser(): void
    {
        $this->requireAdmin();

        if (!$this->isMethod('POST') || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
            return;
        }

        $userId = (int)$this->input('user_id');

        if (!$userId) {
            $this->json(['success' => false, 'message' => 'User ID required'], 400);
            return;
        }

        try {
            if ($this->userModel->deleteUser($userId)) {
                $deleted = ['email' => null];
                $targetUser = $this->userModel->findById($userId);
                if ($targetUser) { $deleted['email'] = $targetUser['email']; }
                $desc = 'Deleted user: ' . ($deleted['email'] ?? ('User ID ' . $userId));
                log_activity('admin_delete_user', 'user', $userId, $desc);
                $this->json(['success' => true, 'message' => 'User deleted successfully']);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to delete user'], 500);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Approve password reset request (AJAX)
     */
    public function approveResetRequest(): void
    {
        $this->requireAdmin();

        if (!$this->isMethod('POST') || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
            return;
        }

        $userId = (int)$this->input('user_id');

        if (!$userId) {
            $this->json(['success' => false, 'message' => 'User ID required'], 400);
            return;
        }

        try {
            if ($this->userModel->approveResetRequest($userId)) {
                log_activity('admin_approve_reset', 'user', $userId, 'Admin approved password reset for user ID: ' . $userId);

                $this->json(['success' => true, 'message' => 'Reset request approved!']);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to approve request'], 500);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Admin reset user password (AJAX)
     */
    public function adminResetUserPassword(): void
    {
        $this->requireAdmin();

        if (!$this->isMethod('POST') || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
            return;
        }

        $userId = (int)$this->input('user_id');
        $newPassword = trim($this->input('new_password'));

        if (!$userId || !$newPassword) {
            $this->json(['success' => false, 'message' => 'User ID and new password required'], 400);
            return;
        }

        if (strlen($newPassword) < 8) {
            $this->json(['success' => false, 'message' => 'Password must be at least 8 characters'], 400);
            return;
        }

        try {
            // Reset the password
            $resetSuccess = $this->userModel->adminResetPassword($userId, $newPassword);

            if ($resetSuccess) {
                // Clear the password reset request from activity log
                $this->userModel->clearPasswordResetRequest($userId);

                // Log the action
                log_activity('admin_password_reset', 'user', $userId, 'Admin reset password for user ID: ' . $userId);

                $this->json(['success' => true, 'message' => 'Password reset successfully']);
            } else {
                error_log("AdminController: Password reset returned false for user ID: $userId");
                $this->json(['success' => false, 'message' => 'Failed to reset password - database update failed'], 500);
            }
        } catch (Exception $e) {
            error_log("AdminController: Exception during password reset: " . $e->getMessage());
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * System Settings Management
     */
    public function settings(): void
    {
        $this->requireAdmin();
        
        if ($this->isMethod('POST')) {
            $this->updateSettings();
            return;
        }
        
        $this->setTitle('System Settings - Admin');
        
        try {
            $settings = $this->settingModel->getGrouped();
            $this->addData('settings', $settings);
        } catch (Exception $e) {
            error_log("Admin settings error: " . $e->getMessage());
            flash('error', 'Failed to load settings: ' . $e->getMessage());
            $this->addData('settings', []);
        }
        
        $this->view('admin.settings');
    }
    
    /**
     * Update system settings
     */
    private function updateSettings(): void
    {
        if (!$this->validateCSRF()) {
            flash('error', 'Invalid security token');
            $this->redirect(route_url('admin.settings'));
            return;
        }
        
        try {
            // Get all settings from POST
            $settingsToUpdate = [];
            foreach ($_POST as $key => $value) {
                if ($key !== 'csrf_token' && strpos($key, 'setting_') === 0) {
                    $settingKey = substr($key, 8); // Remove 'setting_' prefix
                    $settingsToUpdate[$settingKey] = $value;
                }
            }
            
            $success = $this->settingModel->updateMultiple($settingsToUpdate);
            
            if ($success) {
                $desc = 'Updated system settings (' . count($settingsToUpdate) . ' keys)';
                log_activity('admin_update_settings', 'system_setting', null, $desc);
                flash('success', 'System settings updated successfully!');
            } else {
                flash('error', 'Failed to update some settings');
            }
            
        } catch (Exception $e) {
            error_log("Update settings error: " . $e->getMessage());
            flash('error', 'Failed to update settings: ' . $e->getMessage());
        }
        
        $this->redirect(route_url('admin.settings'));
    }

    /**
     * View user's music collection (read-only for admin)
     */
    public function userMusicCollection(int $userId): void
    {
        $this->requireAdmin();

        $this->setTitle('User Music Collection - Admin');
        
        try {
            // Get user info
            $user = $this->userModel->findById($userId);

            if (!$user) {
                flash('error', 'User not found.');
                $this->redirect('/admin/users');
                return;
            }

            // Get user's music entries
            $musicModel = new \MusicLocker\Models\MusicEntry();
            $entries = $musicModel->getUserEntries($userId);
            
            $this->addData('user', $user);
            $this->addData('entries', $entries);
            $this->addData('isAdminView', true);
            
        } catch (Exception $e) {
            error_log("Admin view user music error: " . $e->getMessage());
            flash('error', 'Failed to load user music collection: ' . $e->getMessage());
            $this->redirect('/admin/users/' . $userId);
            return;
        }
        
        $this->view('admin.user-music');
    }

    /**
     * Save admin notes for a user (AJAX)
     */
    public function saveUserNotes(): void
    {
        $this->requireAdmin();

        if (!$this->isMethod('POST') || !$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
            return;
        }

        $userId = (int)$this->input('user_id');
        $notes = trim($this->input('notes') ?? '');

        if (!$userId) {
            $this->json(['success' => false, 'message' => 'User ID required'], 400);
            return;
        }

        try {
            // For now, we'll use the activity log to store notes
            // In a production app, you'd want a dedicated user_notes table
            $desc = 'Admin notes: ' . $notes;
            log_activity('admin_notes', 'user', $userId, $desc);
            
            $this->json(['success' => true, 'message' => 'Notes saved successfully']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

}