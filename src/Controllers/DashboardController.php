<?php

namespace MusicLocker\Controllers;

use MusicLocker\Models\User;
use MusicLocker\Services\Database;

/**
 * Dashboard Controller
 * Handles user dashboard and main application interface
 */
class DashboardController extends BaseController
{
    private User $userModel;
    private Database $db;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->db = Database::getInstance();
    }
    
    /**
     * Show user dashboard
     */
    public function index(): void
    {
        $this->requireAuth();
        
        $userId = current_user_id();
        
        // Get user stats
        $userStats = $this->userModel->getUserStats($userId);
        $user = $this->userModel->findById($userId);
        
        // Get playlist count
        $playlistCount = $this->getPlaylistCount($userId);
        $userStats['playlist_count'] = $playlistCount;
        
        // Get recent activity (user-scoped)
        $recentActivity = $this->getUserActivity($userId, 10);
        
        $this->setTitle('Dashboard');
        $this->addData('current_page', 'dashboard');
        $this->addData('user', $user);
        $this->addData('userStats', $userStats);
        $this->addData('recentActivity', $recentActivity);
        $this->view('dashboard');
    }
    
    /**
     * Get user's playlist count
     */
    private function getPlaylistCount(int $userId): int
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM playlists WHERE user_id = ?";
            $result = $this->db->queryOne($sql, [$userId]);
            return (int)($result['count'] ?? 0);
        } catch (\Exception $e) {
            error_log("Get playlist count error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get user's recent activity
     */
    private function getUserActivity(int $userId, int $limit = 20): array
    {
        try {
            $activity = [];
            
            // Get recent music entries
            $sql = "SELECT 'music_add' as type, 'Added song' as action, 
                           CONCAT('Added \"', title, '\" by ', artist) as description,
                           date_added as timestamp
                    FROM music_entries 
                    WHERE user_id = ?
                    ORDER BY date_added DESC
                    LIMIT ?";
            
            $musicActivity = $this->db->query($sql, [$userId, $limit]);
            $activity = array_merge($activity, $musicActivity);
            
            // Get login activity
            $user = $this->userModel->findById($userId);
            if ($user && $user['last_login']) {
                $activity[] = [
                    'type' => 'login',
                    'action' => 'Login',
                    'description' => 'Logged in to account',
                    'timestamp' => $user['last_login']
                ];
            }
            
            // Sort by timestamp desc
            usort($activity, function($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });
            
            return array_slice($activity, 0, $limit);
            
        } catch (\Exception $e) {
            error_log("Get user activity error: " . $e->getMessage());
            return [];
        }
    }
}