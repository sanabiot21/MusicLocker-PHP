<?php

namespace MusicLocker\Controllers;

use MusicLocker\Models\User;
use Exception;

/**
 * Admin Controller
 * Handles admin functionality - user management and system health
 */
class AdminController extends BaseController
{
    private User $userModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }
    
    /**
     * Admin Dashboard
     */
    public function dashboard(): void
    {
        $this->setTitle('Admin Dashboard - Music Locker');
        
        // Get system overview stats from database
        try {
            $userStats = [
                'total_users' => $this->userModel->getTotalUsers(),
                'active_users' => $this->userModel->getActiveUsers(),
                'new_users_today' => $this->userModel->getNewUsersToday(),
                'total_music_entries' => $this->userModel->getTotalMusicEntries()
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
        }
        
        $this->addData('userStats', $userStats);
        $this->view('admin.dashboard');
    }
    
    /**
     * User Management List
     */
    public function userList(): void
    {
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
        $this->setTitle('User Details - Admin');
        
        try {
            // Get real user data from database
            $user = $this->userModel->findById($userId);
            
            if (!$user) {
                flash('error', 'User not found.');
                $this->redirect(route_url('admin.users'));
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
            $this->redirect(route_url('admin.users'));
            return;
        }
        
        $this->view('admin.user-detail');
    }
    
    /**
     * System Health Dashboard
     */
    public function systemHealth(): void
    {
        $this->setTitle('System Health - Admin');
        $this->view('admin.system-health');
    }
    
}