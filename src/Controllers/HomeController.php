<?php

namespace MusicLocker\Controllers;
use MusicLocker\Services\Database;

/**
 * Home Controller
 * Handles the landing page and general public pages
 */
class HomeController extends BaseController
{
    /**
     * Show the landing page
     */
    public function index(): void
    {
        // Redirect to dashboard if already logged in
        if (is_logged_in()) {
            $this->redirect(route_url('dashboard'));
            return;
        }
        
        $db = Database::getInstance();
        $stats = [
            'active_users' => (int)(($db->queryOne("SELECT COUNT(*) AS c FROM users WHERE status = 'active' ")['c'] ?? 0)),
            'songs_cataloged' => (int)(($db->queryOne("SELECT COUNT(*) AS c FROM music_entries ")['c'] ?? 0)),
            'personal_notes' => (int)(($db->queryOne("SELECT COUNT(*) AS c FROM music_notes ")['c'] ?? 0)),
            'mood_tags_created' => (int)(($db->queryOne("SELECT COUNT(*) AS c FROM tags WHERE is_system_tag = FALSE ")['c'] ?? 0))
        ];

        $this->setTitle('Your Personal Music Universe');
        $this->addData('current_page', 'home');
        $this->addData('stats', $stats);
        $this->view('home');
    }
}