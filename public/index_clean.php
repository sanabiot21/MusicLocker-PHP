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

use MusicLocker\Controllers\Web\MusicWebController;
use MusicLocker\Controllers\Api\MusicApiController;
use MusicLocker\Controllers\Api\MusicSearchController;
use MusicLocker\Controllers\AuthController;
use MusicLocker\Controllers\DashboardController;
use MusicLocker\Controllers\SpotifyController;

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
                redirect(route_url('login'));
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
            
        case $requestUri === '/dashboard':
            $controller = new DashboardController();
            $controller->index();
            break;
            
        // Music web routes
        case $requestUri === '/music':
            $controller = new MusicWebController();
            $controller->index();
            break;
            
        case preg_match('/^\/music\/(\d+)$/', $requestUri, $matches):
            $controller = new MusicWebController();
            $controller->show((int)$matches[1]);
            break;
            
        case preg_match('/^\/music\/(\d+)\/edit$/', $requestUri, $matches):
            $controller = new MusicWebController();
            $requestMethod === 'POST' ? $controller->update((int)$matches[1]) : $controller->edit((int)$matches[1]);
            break;
            
        case $requestUri === '/music/add':
            $controller = new MusicWebController();
            $controller->add();
            break;
            
        case preg_match('/^\/music\/(\d+)\/delete$/', $requestUri, $matches):
            $controller = new MusicWebController();
            $controller->delete((int)$matches[1]);
            break;
            
        // Music API routes
        case $requestUri === '/api/music/favorite':
            $controller = new MusicApiController();
            $controller->toggleFavorite();
            break;
            
        case $requestUri === '/api/music/play':
            $controller = new MusicApiController();
            $controller->recordPlay();
            break;
            
        case $requestUri === '/api/music/stats':
            $controller = new MusicApiController();
            $controller->stats();
            break;
            
        case preg_match('/^\/api\/music\/(\d+)$/', $requestUri, $matches):
            $controller = new MusicApiController();
            match($requestMethod) {
                'GET' => $controller->show((int)$matches[1]),
                'PUT', 'PATCH' => $controller->update((int)$matches[1]),
                'DELETE' => $controller->delete((int)$matches[1]),
                default => http_response_code(405)
            };
            break;
            
        // Search API routes
        case $requestUri === '/api/search':
            $controller = new MusicSearchController();
            $controller->search();
            break;
            
        case $requestUri === '/api/search/suggestions':
            $controller = new MusicSearchController();
            $controller->suggestions();
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
            
        default:
            http_response_code(404);
            echo "404 - Page Not Found";
    }
    
} catch (Exception $e) {
    error_log("Application Error: " . $e->getMessage());
    http_response_code(500);
    echo "500 - Internal Server Error";
}