<?php
/**
 * Music Locker Application Entry Point
 * Team NaturalStupidity
 * 
 * This file serves as the front controller for the application,
 * handling all incoming requests and routing them appropriately.
 */

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Define application paths
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('CONFIG_PATH', ROOT_PATH . '/config');
define('SRC_PATH', ROOT_PATH . '/src');

// Include Composer autoloader
require_once ROOT_PATH . '/vendor/autoload.php';

// Define essential helper functions if not already defined
if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('route_url')) {
    function route_url(string $name, $param = null): string {
        $routes = [
            'home' => '/',
            'login' => '/login',
            'register' => '/register',
            'logout' => '/logout',
            'dashboard' => '/dashboard',
            'music' => '/music',
            'music.index' => '/music',
            'music.add' => '/music/add',
            'spotify.search' => '/api/spotify/search',
            'spotify.track' => '/api/spotify/track',
        ];
        
        $path = $routes[$name] ?? $name;
        
        // Basic URL construction for Ngrok compatibility
        $isNgrok = isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], '.ngrok');
        $baseUrl = $isNgrok ? 'https://' . $_SERVER['HTTP_HOST'] : 'http://musiclocker.local';
        
        return rtrim($baseUrl, '/') . $path;
    }
}

if (!function_exists('flash')) {
    function flash(string $key, $value = null) {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
        } else {
            $flash = $_SESSION['_flash'][$key] ?? null;
            unset($_SESSION['_flash'][$key]);
            return $flash;
        }
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

if (!function_exists('current_user_id')) {
    function current_user_id(): ?int {
        return $_SESSION['user_id'] ?? null;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void {
        header("Location: $url");
        exit;
    }
}

if (!function_exists('format_time_ago')) {
    function format_time_ago($datetime): string {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'just now';
        if ($time < 3600) return floor($time/60) . ' min ago';
        if ($time < 86400) return floor($time/3600) . ' hr ago';
        if ($time < 2592000) return floor($time/86400) . ' days ago';
        
        return date('M j, Y', strtotime($datetime));
    }
}

if (!function_exists('config')) {
    function config(string $key, $default = null) {
        $configMap = [
            'app.name' => 'Music Locker',
            'app.url' => 'https://12878091ec44.ngrok-free.app',
            'spotify.client_id' => '356702eb81d0499381fcf5222ab757fb',
            'spotify.client_secret' => '3a826c32f5dc41e9939b4ec3229a5647',
            'spotify.api.base_url' => 'https://api.spotify.com/v1'
        ];
        
        return $configMap[$key] ?? $default;
    }
}


if (!function_exists('json_response')) {
    function json_response(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

if (!function_exists('validate_csrf')) {
    function validate_csrf(string $token): bool {
        return isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);
    }
}

if (!function_exists('log_activity')) {
    function log_activity(string $action, string $targetType = null, int $targetId = null, string $description = null): void {
        // Simple activity logging - you can enhance this to write to database
        error_log("Activity: $action - User: " . (current_user_id() ?? 'guest') . " - $description");
    }
}

if (!function_exists('format_duration')) {
    function format_duration(int $seconds): string {
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;
        return sprintf('%d:%02d', $minutes, $seconds);
    }
}

use MusicLocker\Controllers\AuthController;
use MusicLocker\Controllers\HomeController;
use MusicLocker\Controllers\DashboardController;
use MusicLocker\Controllers\MusicController;
use MusicLocker\Controllers\SpotifyController;
// DeezerController removed - music catalog is metadata only

try {
    // Get the request URI and method
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    
    // Remove query string from URI
    $uri = parse_url($requestUri, PHP_URL_PATH);
    
    // Dynamic routing system with parameter support
    // Handle dynamic routes first
    if (preg_match('/^\/api\/music\/(\d+)$/', $uri, $matches)) {
        $controller = new MusicController();
        $songId = (int)$matches[1];
        
        if ($requestMethod === 'GET') {
            $controller->apiShow($songId);
        } elseif ($requestMethod === 'POST' && isset($_POST['_method'])) {
            if ($_POST['_method'] === 'DELETE') {
                $controller->apiDelete($songId);
            } elseif ($_POST['_method'] === 'PUT') {
                $controller->apiUpdate($songId);
            }
        }
        exit; // End execution after handling dynamic route
    }
    
    if (preg_match('/^\/music\/(\d+)$/', $uri, $matches)) {
        $controller = new MusicController();
        $songId = (int)$matches[1];
        
        if ($requestMethod === 'GET') {
            $controller->show($songId);
        } elseif ($requestMethod === 'POST' && isset($_POST['_method'])) {
            if ($_POST['_method'] === 'DELETE') {
                $controller->delete($songId);
            } elseif ($_POST['_method'] === 'PUT') {
                $controller->update($songId);
            }
        }
        exit; // End execution after handling dynamic route
    }
    
    if (preg_match('/^\/music\/(\d+)\/edit$/', $uri, $matches)) {
        $controller = new MusicController();
        $songId = (int)$matches[1];
        $controller->showEdit($songId);
        exit; // End execution after handling dynamic route
    }
    
    // Static routes
    switch ($uri) {
        // Home/Landing Page
        case '/':
            $controller = new HomeController();
            $controller->index();
            break;
            
        // Authentication Routes
        case '/login':
            $controller = new AuthController();
            if ($requestMethod === 'POST') {
                $controller->login();
            } else {
                $controller->showLogin();
            }
            break;
            
        case '/register':
            $controller = new AuthController();
            if ($requestMethod === 'POST') {
                $controller->register();
            } else {
                $controller->showRegister();
            }
            break;
            
        case '/logout':
            $controller = new AuthController();
            $controller->logout();
            break;
            
        case '/forgot-password':
        case '/forgot':
            $controller = new AuthController();
            if ($requestMethod === 'POST') {
                $controller->forgotPassword();
            } else {
                $controller->showForgotPassword();
            }
            break;
            
        case '/reset-password':
        case '/reset':
            $controller = new AuthController();
            if ($requestMethod === 'POST') {
                $controller->resetPassword();
            } else {
                $controller->showResetPassword();
            }
            break;
            
        case '/profile':
            $controller = new AuthController();
            if ($requestMethod === 'POST') {
                // Check if it's password change or profile update
                if (isset($_POST['current_password'])) {
                    $controller->changePassword();
                } else {
                    $controller->updateProfile();
                }
            } else {
                $controller->showProfile();
            }
            break;
            
        // Dashboard Routes
        case '/dashboard':
            $controller = new DashboardController();
            $controller->index();
            break;
            
        // Music Collection Routes
        case '/music':
            $controller = new MusicController();
            $controller->index();
            break;
            
        case '/music/add':
            // Keep POST route for form processing, remove GET route
            $controller = new MusicController();
            if ($requestMethod === 'POST') {
                $controller->add();
            } else {
                // Redirect to main music page instead of showing old add form
                header('Location: ' . route_url('music'));
                exit;
            }
            break;
            
        // Music API Routes
        case '/api/music/search':
            $controller = new MusicController();
            $controller->search();
            break;
            
        case '/api/music/favorite':
            $controller = new MusicController();
            $controller->toggleFavorite();
            break;
            
        case '/api/music/play':
            $controller = new MusicController();
            $controller->recordPlay();
            break;
            
        case '/api/music/stats':
            $controller = new MusicController();
            $controller->stats();
            break;
            
        case '/api/music/search-hybrid':
            $controller = new MusicController();
            $controller->searchHybrid();
            break;
            
        // Modal Data API Routes
        case (preg_match('/^\/api\/music\/(\d+)$/', $uri, $matches) ? $uri : false):
            $controller = new MusicController();
            $controller->getEntry((int)$matches[1]);
            break;
            
        case (preg_match('/^\/api\/music\/(\d+)\/details$/', $uri, $matches) ? $uri : false):
            $controller = new MusicController();
            $controller->getEntryDetails((int)$matches[1]);
            break;
            
        // Spotify Integration Routes (Simple Client Credentials API)
        case '/api/spotify/search':
            $controller = new SpotifyController();
            $controller->search();
            break;
            
        case '/api/spotify/search-preview':
            $controller = new SpotifyController();
            $controller->searchWithPreview();
            break;
            
        case '/api/spotify/track':
            $controller = new SpotifyController();
            $controller->track();
            break;
            
        case '/api/spotify/test':
            $controller = new SpotifyController();
            $controller->test();
            break;
            
        // Deezer routes removed - music catalog is metadata only
            
        // Static file serving (development only)
        case '/assets/css/dark-techno-theme.css':
            serveStaticFile(ROOT_PATH . '/music-locker-bootstrap/assets/css/dark-techno-theme.css', 'text/css');
            break;
            
        // 404 Not Found
        default:
            http_response_code(404);
            
            // Simple 404 page
            ?>
            <!DOCTYPE html>
            <html lang="en" data-bs-theme="dark">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Page Not Found - Music Locker</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
                <link rel="stylesheet" href="/assets/css/dark-techno-theme.css">
            </head>
            <body class="bg-pattern d-flex align-items-center justify-content-center min-vh-100">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-6 text-center">
                            <div class="feature-card">
                                <i class="bi bi-exclamation-triangle display-1 mb-4" 
                                   style="background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                                <h1 class="h2 mb-3">Page Not Found</h1>
                                <p class="text-muted mb-4">
                                    The page you're looking for doesn't exist or has been moved.
                                </p>
                                <div class="d-flex gap-3 justify-content-center">
                                    <a href="/" class="btn btn-glow">
                                        <i class="bi bi-house me-2"></i>Go Home
                                    </a>
                                    <?php if (is_logged_in()): ?>
                                        <a href="/dashboard" class="btn btn-outline-glow">
                                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </body>
            </html>
            <?php
            break;
    }
    
} catch (Exception $e) {
    // Handle application errors
    error_log("Application Error: " . $e->getMessage());
    
    // Show error page in development, generic message in production
    if (env('APP_DEBUG', true)) {
        echo "<h1>Application Error</h1>";
        echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        http_response_code(500);
        echo "<h1>Internal Server Error</h1>";
        echo "<p>Something went wrong. Please try again later.</p>";
    }
}

/**
 * Serve static files (development helper)
 */
function serveStaticFile(string $filePath, string $contentType): void
{
    if (file_exists($filePath)) {
        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
    } else {
        http_response_code(404);
        echo "File not found";
    }
    exit;
}

/**
 * Helper function to get storage path
 */
function storage_path(string $path = ''): string
{
    $storagePath = ROOT_PATH . '/storage';
    if (!is_dir($storagePath)) {
        mkdir($storagePath, 0755, true);
    }
    return $storagePath . ($path ? '/' . ltrim($path, '/') : '');
}