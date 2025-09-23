<?php

namespace MusicLocker\Controllers;

use MusicLocker\Models\User;

/**
 * Dashboard Controller
 * Handles user dashboard and main application interface
 */
class DashboardController extends BaseController
{
    private User $userModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }
    
    /**
     * Show user dashboard
     */
    public function index(): void
    {
        $this->requireAuth();
        
        // Get user stats
        $userStats = $this->userModel->getUserStats(current_user_id());
        $user = $this->userModel->findById(current_user_id());
        
        $this->setTitle('Dashboard');
        $this->addData('current_page', 'dashboard');
        $this->addData('user', $user);
        $this->addData('userStats', $userStats);
        $this->view('dashboard');
    }
}