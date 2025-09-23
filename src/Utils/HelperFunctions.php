<?php
/**
 * Global Helper Functions
 * 
 * Essential helper functions for template and global use
 * Separate from the entry point following clean architecture
 */

if (!function_exists('e')) {
    /**
     * Escape HTML entities
     */
    function e($value): string {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('json_response')) {
    /**
     * Send JSON response and exit
     */
    function json_response(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_SLASHES);
        exit;
    }
}

if (!function_exists('redirect')) {
    /**
     * HTTP redirect
     */
    function redirect(string $url): void {
        header("Location: $url");
        exit;
    }
}

if (!function_exists('flash')) {
    /**
     * Flash message system
     */
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
    /**
     * Check if user is authenticated
     */
    function is_logged_in(): bool {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

if (!function_exists('current_user_id')) {
    /**
     * Get current user ID
     */
    function current_user_id(): ?int {
        return $_SESSION['user_id'] ?? null;
    }
}

if (!function_exists('format_duration')) {
    /**
     * Format duration in seconds to MM:SS
     */
    function format_duration(?int $seconds): string {
        if (!$seconds) return '0:00';
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;
        return sprintf('%d:%02d', $minutes, $seconds);
    }
}

if (!function_exists('format_time_ago')) {
    /**
     * Format datetime as time ago
     */
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
    /**
     * Get configuration value
     */
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

if (!function_exists('log_activity')) {
    /**
     * Log user activity
     */
    function log_activity(string $action, ?string $targetType = null, ?int $targetId = null, ?string $description = null): void {
        error_log("Activity: $action - User: " . (current_user_id() ?? 'guest') . " - $description");
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get storage path
     */
    function storage_path(string $path = ''): string {
        $storagePath = ROOT_PATH . '/storage';
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }
        return $storagePath . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('env')) {
    /**
     * Get environment variable
     */
    function env(string $key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('base_url')) {
    /**
     * Get base URL
     */
    function base_url(): string {
        $isNgrok = isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], '.ngrok');
        $baseUrl = $isNgrok ? 'https://' . $_SERVER['HTTP_HOST'] : 'http://musiclocker.local';
        return rtrim($baseUrl, '/');
    }
}

if (!function_exists('route_url')) {
    /**
     * Generate route URL
     */
    function route_url(string $route): string {
        $routes = [
            'dashboard' => '/dashboard',
            'music' => '/music',
            'music.add' => '/music/add',
            'login' => '/login',
            'logout' => '/logout',
            'register' => '/register'
        ];
        
        $path = $routes[$route] ?? '/' . $route;
        return base_url() . $path;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generate CSRF token
     */
    function csrf_token(): string {
        if (empty($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate CSRF token hidden field
     */
    function csrf_field(): string {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('validate_csrf')) {
    /**
     * Validate CSRF token
     */
    function validate_csrf(string $token): bool {
        return isset($_SESSION['_token']) && hash_equals($_SESSION['_token'], $token);
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