<?php

namespace MusicLocker\Controllers;

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
        
        $this->setTitle('Your Personal Music Universe');
        $this->addData('current_page', 'home');
        $this->view('home');
    }
}