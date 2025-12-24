<?php
/**
 * Global Helper Functions
 * Music Locker Application - Enhanced with Environment Loading
 */

use Dotenv\Dotenv;

// Load environment variables
if (!function_exists('load_env')) {
    function load_env(): void
    {
        static $loaded = false;
        if (!$loaded && class_exists('Dotenv\\Dotenv')) {
            $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
            $dotenv->safeLoad();
            $loaded = true;
        }
    }
}

// Load environment on first call
load_env();

if (!function_exists('env')) {
    /**
     * Get environment variable with optional default
     */
    function env(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? getenv($key);
        
        if ($value === false) {
            return $default;
        }
        
        // Convert string representations to actual types
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }
        
        return $value;
    }
}

if (!function_exists('config')) {
    /**
     * Get configuration value
     */
    function config(string $key, $default = null)
    {
        static $configs = [];
        
        $keys = explode('.', $key);
        $configFile = array_shift($keys);
        
        if (!isset($configs[$configFile])) {
            $configPath = __DIR__ . '/../../config/' . $configFile . '.php';
            if (file_exists($configPath)) {
                $configs[$configFile] = require $configPath;
            } else {
                return $default;
            }
        }
        
        $config = $configs[$configFile];
        
        foreach ($keys as $segment) {
            if (is_array($config) && isset($config[$segment])) {
                $config = $config[$segment];
            } else {
                return $default;
            }
        }
        
        return $config;
    }
}

if (!function_exists('detect_ngrok_url')) {
    /**
     * Detect if running through Ngrok and get the tunnel URL
     */
    function detect_ngrok_url(): ?string
    {
        // Check if HTTP_HOST contains ngrok domain
        if (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], '.ngrok')) {
            // Always use HTTPS for Ngrok tunnels (they support HTTPS by default)
            return 'https://' . $_SERVER['HTTP_HOST'];
        }
        
        // Check for Ngrok headers
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            if (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], '.ngrok')) {
                return 'https://' . $_SERVER['HTTP_HOST'];
            }
        }
        
        // Check for Ngrok-specific headers
        if (isset($_SERVER['HTTP_X_ORIGINAL_HOST']) && str_contains($_SERVER['HTTP_X_ORIGINAL_HOST'], '.ngrok')) {
            return 'https://' . $_SERVER['HTTP_X_ORIGINAL_HOST'];
        }
        
        return null;
    }
}

if (!function_exists('base_url')) {
    /**
     * Generate base URL for the application with Ngrok detection
     */
    function base_url(string $path = ''): string
    {
        // First try to detect Ngrok tunnel
        $ngrokUrl = detect_ngrok_url();
        if ($ngrokUrl) {
            $baseUrl = $ngrokUrl;
        } else {
            // Fall back to configured URL
            $baseUrl = config('app.url', 'http://musiclocker.local');
        }
        
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset_url')) {
    /**
     * Generate URL for static assets
     */
    function asset_url(string $path): string
    {
        return base_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('route_url')) {
    /**
     * Generate URL for a named route
     */
    function route_url(string $name, array $params = []): string
    {
        // Simple routing for now - can be enhanced later
        $routes = [
            'home' => '/',
            'login' => '/login',
            'register' => '/register',
            'logout' => '/logout',
            'dashboard' => '/dashboard',
            'music.index' => '/music',
            'music.add' => '/music/add',
            'music.edit' => '/music/edit',
            'spotify.auth' => '/api/spotify/auth',
            'spotify.callback' => '/api/spotify/callback',
            'admin.dashboard' => '/admin',
            'admin.users' => '/admin/users',
            'admin.system' => '/admin/system',
            'admin.settings' => '/admin/settings',
        ];
        
        $path = $routes[$name] ?? $name;
        
        // Replace route parameters
        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', $value, $path);
        }
        
        return base_url($path);
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generate CSRF token
     */
    function csrf_token(): string
    {
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate hidden CSRF token field for forms
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('validate_csrf')) {
    /**
     * Validate CSRF token
     */
    function validate_csrf(?string $token = null): bool
    {
        $token = $token ?? ($_POST['_token'] ?? $_GET['_token'] ?? '');
        $sessionToken = $_SESSION['_csrf_token'] ?? '';
        
        return $token !== '' && hash_equals($sessionToken, $token);
    }
}

if (!function_exists('sanitize_input')) {
    /**
     * Sanitize user input
     */
    function sanitize_input($input): string
    {
        if (is_array($input)) {
            return array_map('sanitize_input', $input);
        }
        
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('format_duration')) {
    /**
     * Format duration from seconds to human readable format
     */
    function format_duration(int $seconds): string
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        
        return sprintf('%d:%02d', $minutes, $remainingSeconds);
    }
}

if (!function_exists('format_date')) {
    /**
     * Format date for display
     */
    function format_date($date, string $format = 'M j, Y'): string
    {
        if (is_string($date)) {
            $date = new DateTime($date);
        }
        
        if (!$date instanceof DateTime) {
            return '';
        }
        
        return $date->format($format);
    }
}

if (!function_exists('flash')) {
    /**
     * Set or get flash messages
     */
    function flash(?string $key = null, $value = null)
    {
        if ($key === null) {
            return $_SESSION['_flash'] ?? [];
        }
        
        if ($value === null) {
            $message = $_SESSION['_flash'][$key] ?? null;
            unset($_SESSION['_flash'][$key]);
            return $message;
        }
        
        $_SESSION['_flash'][$key] = $value;
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to URL
     */
    function redirect(string $url, int $code = 302): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $url = base_url($url);
        }
        
        header("Location: $url", true, $code);
        exit;
    }
}

if (!function_exists('json_response')) {
    /**
     * Send JSON response
     */
    function json_response(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

if (!function_exists('abort')) {
    /**
     * Abort with HTTP error
     */
    function abort(int $code = 404, ?string $message = null): void
    {
        http_response_code($code);
        
        $messages = [
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
        ];
        
        $message = $message ?? $messages[$code] ?? 'Error';
        
        // TODO: Load error view template
        echo "<h1>Error $code</h1><p>$message</p>";
        exit;
    }
}

if (!function_exists('is_logged_in')) {
    /**
     * Check if user is logged in
     */
    function is_logged_in(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

if (!function_exists('current_user_id')) {
    /**
     * Get current logged in user ID
     */
    function current_user_id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
}

if (!function_exists('require_auth')) {
    /**
     * Require user to be authenticated
     */
    function require_auth(): void
    {
        if (!is_logged_in()) {
            flash('error', 'Please log in to access this page.');
            redirect(route_url('login'));
        }
    }
}

if (!function_exists('log_activity')) {
    /**
     * Log user activity
     */
    function log_activity(string $action, ?string $targetType = null, ?int $targetId = null, ?string $description = null): void
    {
        if (!is_logged_in()) {
            return;
        }
        
        try {
            $db = \MusicLocker\Services\Database::getInstance();
            
            $stmt = $db->prepare("
                INSERT INTO activity_log (user_id, action, target_type, target_id, description, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                current_user_id(),
                $action,
                $targetType,
                $targetId,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            // Log the error but don't break the application
            error_log("Failed to log activity: " . $e->getMessage());
        }
    }
}

/**
 * Ngrok Integration and Spotify Helper Functions
 */
if (!function_exists('get_ngrok_url')) {
    /**
     * Get Ngrok URL if available
     */
    function get_ngrok_url(): ?string
    {
        return env('NGROK_URL');
    }
}

if (!function_exists('update_spotify_redirect_uri')) {
    /**
     * Update Spotify redirect URI for Ngrok
     */
    function update_spotify_redirect_uri(?string $ngrokUrl = null): string
    {
        $ngrokUrl = $ngrokUrl ?? get_ngrok_url();
        
        if ($ngrokUrl) {
            $redirectUri = rtrim($ngrokUrl, '/') . '/api/spotify/callback';
            
            // Update environment variable in memory
            $_ENV['SPOTIFY_REDIRECT_URI'] = $redirectUri;
            
            return $redirectUri;
        }
        
        // Fallback to default redirect URI
        return env('SPOTIFY_REDIRECT_URI', 'http://127.0.0.1:8888/api/spotify/callback');
    }
}

if (!function_exists('get_spotify_redirect_uri')) {
    /**
     * Get dynamic Spotify redirect URI (Ngrok-aware)
     */
    function get_spotify_redirect_uri(): string
    {
        // Check if Ngrok URL is available and use it
        $ngrokUrl = get_ngrok_url();
        if ($ngrokUrl) {
            return update_spotify_redirect_uri($ngrokUrl);
        }
        
        // Otherwise use configured redirect URI
        return config('spotify.redirect_uri');
    }
}

if (!function_exists('old')) {
    /**
     * Get old input value from flash data
     */
    function old(string $key, $default = '')
    {
        $oldInput = flash('old_input');
        return is_array($oldInput) ? ($oldInput[$key] ?? $default) : $default;
    }
}

if (!function_exists('validation_error')) {
    /**
     * Get validation error for a field
     */
    function validation_error(string $key): ?string
    {
        $errors = flash('validation_errors');
        return is_array($errors) ? ($errors[$key] ?? null) : null;
    }
}

if (!function_exists('has_validation_error')) {
    /**
     * Check if field has validation error
     */
    function has_validation_error(string $key): bool
    {
        return validation_error($key) !== null;
    }
}

if (!function_exists('star_rating')) {
    /**
     * Generate star rating HTML
     */
    function star_rating(int $rating, int $maxStars = 5): string
    {
        $stars = '';
        for ($i = 1; $i <= $maxStars; $i++) {
            if ($i <= $rating) {
                $stars .= '<i class="bi bi-star-fill text-warning"></i>';
            } else {
                $stars .= '<i class="bi bi-star text-muted"></i>';
            }
        }
        return $stars;
    }
}

/**
 * Timezone Helper Functions
 * Support for UTC+8 Manila timezone
 */

if (!function_exists('format_time_ago')) {
    /**
     * Format timestamp as "time ago" string
     * Handles NULL values and timezone conversion
     */
    function format_time_ago(?string $timestamp): string
    {
        if (empty($timestamp)) {
            return 'Never';
        }

        try {
            $timestamp = new DateTime($timestamp);
            $now = new DateTime();
            $diff = $now->diff($timestamp);
            
            if ($diff->days > 0) {
                if ($diff->days == 1) {
                    return '1 day ago';
                } elseif ($diff->days < 7) {
                    return $diff->days . ' days ago';
                } elseif ($diff->days < 30) {
                    $weeks = floor($diff->days / 7);
                    return $weeks . ($weeks == 1 ? ' week ago' : ' weeks ago');
                } elseif ($diff->days < 365) {
                    $months = floor($diff->days / 30);
                    return $months . ($months == 1 ? ' month ago' : ' months ago');
                } else {
                    $years = floor($diff->days / 365);
                    return $years . ($years == 1 ? ' year ago' : ' years ago');
                }
            } elseif ($diff->h > 0) {
                return $diff->h . ($diff->h == 1 ? ' hour ago' : ' hours ago');
            } elseif ($diff->i > 0) {
                return $diff->i . ($diff->i == 1 ? ' minute ago' : ' minutes ago');
            } else {
                return 'Just now';
            }
        } catch (Exception $e) {
            error_log("Format time ago error: " . $e->getMessage());
            return 'Unknown';
        }
    }
}

if (!function_exists('format_timestamp')) {
    /**
     * Format timestamp to readable format in application timezone
     */
    function format_timestamp(?string $timestamp, string $format = 'M j, Y g:i A'): string
    {
        if (empty($timestamp)) {
            return '';
        }

        try {
            $timestamp = new DateTime($timestamp);
            return $timestamp->format($format);
        } catch (Exception $e) {
            error_log("Format timestamp error: " . $e->getMessage());
            return $timestamp;
        }
    }
}

if (!function_exists('get_timezone_offset')) {
    /**
     * Get timezone offset in seconds
     */
    function get_timezone_offset(): int
    {
        try {
            // Default to UTC+8 Manila timezone
            return 28800; // 8 hours * 3600 seconds
        } catch (Exception $e) {
            // Default to UTC+8 if error
            return 28800;
        }
    }
}

if (!function_exists('now_in_timezone')) {
    /**
     * Get current timestamp in application timezone
     */
    function now_in_timezone(): string
    {
        try {
            return date('Y-m-d H:i:s');
        } catch (Exception $e) {
            return date('Y-m-d H:i:s');
        }
    }
}