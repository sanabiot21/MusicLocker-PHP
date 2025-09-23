<?php
/**
 * Music Locker Application Entry Point - Clean Architecture
 * Team NaturalStupidity
 * 
 * Under 100 lines - only routing and bootstrapping
 */

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session and define paths
session_start();
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('CONFIG_PATH', ROOT_PATH . '/config');
define('SRC_PATH', ROOT_PATH . '/src');

// Include Composer autoloader
require_once ROOT_PATH . '/vendor/autoload.php';

// Load helper functions
require_once SRC_PATH . '/Utils/HelperFunctions.php';
require_once SRC_PATH . '/Security/CsrfManager.php';
require_once SRC_PATH . '/Utils/UrlHelper.php';

use MusicLocker\Controllers\AuthController;
use MusicLocker\Controllers\HomeController;
use MusicLocker\Controllers\DashboardController;
use MusicLocker\Controllers\SpotifyController;
use MusicLocker\Controllers\MusicController;
use MusicLocker\Controllers\AdminController;

// Get request info
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Simple routing
try {
    switch (true) {
        // Authentication routes
        case $requestUri === '/' && $requestMethod === 'GET':
            if (is_logged_in()) {
                redirect(route_url('dashboard'));
            } else {
                $controller = new HomeController();
                $controller->index();
            }
            break;
            
        case $requestUri === '/login':
            $controller = new AuthController();
            $requestMethod === 'POST' ? $controller->login() : $controller->showLogin();
            break;
            
        case $requestUri === '/register':
            $controller = new AuthController();
            $requestMethod === 'POST' ? $controller->register() : $controller->showRegister();
            break;
            
        case $requestUri === '/logout':
            $controller = new AuthController();
            $controller->logout();
            break;
            
        case $requestUri === '/forgot':
            $controller = new AuthController();
            $requestMethod === 'POST' ? $controller->forgotPassword() : $controller->showForgotPassword();
            break;
            
        case $requestUri === '/profile':
            $controller = new AuthController();
            $requestMethod === 'POST' ? $controller->updateProfile() : $controller->showProfile();
            break;
            
        case $requestUri === '/profile/password' && $requestMethod === 'POST':
            $controller = new AuthController();
            $controller->changePassword();
            break;
            
        case $requestUri === '/dashboard':
            $controller = new DashboardController();
            $controller->index();
            break;
            
        // Music Collection Routes (Clean Architecture)
        case $requestUri === '/music':
            $controller = new MusicController();
            $controller->index();
            break;
            
        case preg_match('/^\/music\/add$/', $requestUri):
            $controller = new MusicController();
            $controller->add();
            break;
            
        case preg_match('/^\/music\/(\d+)$/', $requestUri, $matches):
            $controller = new MusicController();
            $controller->show((int)$matches[1]);
            break;
            
        case preg_match('/^\/music\/(\d+)\/edit$/', $requestUri, $matches):
            $controller = new MusicController();
            $controller->edit((int)$matches[1]);
            break;
            
        case preg_match('/^\/music\/(\d+)\/delete$/', $requestUri, $matches):
            if ($requestMethod === 'POST') {
                $controller = new MusicController();
                $controller->delete((int)$matches[1]);
            } else {
                redirect(route_url('music'));
            }
            break;
            
        // Music API Routes (AJAX)
        case $requestUri === '/api/music/favorite':
            if ($requestMethod === 'POST') {
                $controller = new MusicController();
                $controller->toggleFavorite();
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;
            
        // Admin routes (no auth required as per spec)
        case $requestUri === '/admin':
            $controller = new AdminController();
            $controller->dashboard();
            break;
            
        case $requestUri === '/admin/users':
            $controller = new AdminController();
            $controller->userList();
            break;
            
        case preg_match('/^\/admin\/users\/(\d+)$/', $requestUri, $matches) === 1:
            $controller = new AdminController();
            $controller->userDetail((int)$matches[1]);
            break;
            
        case $requestUri === '/admin/system':
            $controller = new AdminController();
            $controller->systemHealth();
            break;
            
        // Spotify routes
        case $requestUri === '/api/spotify/search':
            $controller = new SpotifyController();
            $controller->search();
            break;
            
        case $requestUri === '/spotify/callback':
            $controller = new SpotifyController();
            $controller->callback();
            break;
            
        // Static file serving (development only)
        case $requestUri === '/assets/css/dark-techno-theme.css':
            serveStaticFile(ROOT_PATH . '/music-locker-bootstrap/assets/css/dark-techno-theme.css', 'text/css');
            break;
            
        default:
            http_response_code(404);
            echo "404 - Page Not Found";
    }
    
} catch (Exception $e) {
    error_log("Application Error: " . $e->getMessage());
    http_response_code(500);
    echo "500 - Internal Server Error";
}