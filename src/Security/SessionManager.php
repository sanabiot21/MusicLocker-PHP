<?php

namespace MusicLocker\Security;

/**
 * Secure Session Manager
 * 
 * Provides enhanced session security with regeneration and validation
 * Following OWASP session management best practices
 */
class SessionManager
{
    private const SESSION_LIFETIME = 7200; // 2 hours
    private const REGENERATE_INTERVAL = 1800; // 30 minutes
    
    /**
     * Start secure session
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        
        // Configure session security
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        // Use HTTPS cookies in production
        if (self::isHttps()) {
            ini_set('session.cookie_secure', 1);
        }
        
        session_start();
        
        // Validate and refresh session
        self::validateSession();
    }
    
    /**
     * Login user securely
     */
    public static function login(int $userId): void
    {
        // Regenerate session ID to prevent session fixation
        self::regenerateId(true);
        
        $_SESSION['user_id'] = $userId;
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['ip_address'] = self::getClientIp();
        $_SESSION['user_agent_hash'] = self::getUserAgentHash();
        
        // Set CSRF token for the session
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    
    /**
     * Logout user and destroy session
     */
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Clear session data
            $_SESSION = [];
            
            // Delete session cookie
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, 
                    $params['path'], $params['domain'],
                    $params['secure'], $params['httponly']
                );
            }
            
            // Destroy session
            session_destroy();
        }
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Get current user ID
     */
    public static function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Validate session security
     */
    private static function validateSession(): void
    {
        // Check if session exists
        if (empty($_SESSION)) {
            return;
        }
        
        // Check session lifetime
        if (isset($_SESSION['login_time'])) {
            if (time() - $_SESSION['login_time'] > self::SESSION_LIFETIME) {
                self::logout();
                return;
            }
        }
        
        // Check activity timeout
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > 3600) { // 1 hour inactivity
                self::logout();
                return;
            }
        }
        
        // Validate IP address (optional - can cause issues with mobile users)
        if (config('security.validate_ip', false) && isset($_SESSION['ip_address'])) {
            if ($_SESSION['ip_address'] !== self::getClientIp()) {
                self::logout();
                return;
            }
        }
        
        // Validate user agent (basic fingerprinting)
        if (isset($_SESSION['user_agent_hash'])) {
            if ($_SESSION['user_agent_hash'] !== self::getUserAgentHash()) {
                self::logout();
                return;
            }
        }
        
        // Regenerate session ID periodically
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > self::REGENERATE_INTERVAL) {
            self::regenerateId();
            $_SESSION['last_regeneration'] = time();
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Regenerate session ID
     */
    public static function regenerateId(bool $deleteOld = false): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id($deleteOld);
        }
    }
    
    /**
     * Set flash message
     */
    public static function setFlash(string $key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }
    
    /**
     * Get and clear flash message
     */
    public static function getFlash(string $key)
    {
        $value = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }
    
    /**
     * Get client IP address
     */
    private static function getClientIp(): string
    {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                return trim($ips[0]);
            }
        }
        
        return 'unknown';
    }
    
    /**
     * Get user agent hash for basic fingerprinting
     */
    private static function getUserAgentHash(): string
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        return hash('sha256', $userAgent);
    }
    
    /**
     * Check if HTTPS is being used
     */
    private static function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || $_SERVER['SERVER_PORT'] == 443
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    }
    
    /**
     * Get session info for debugging
     */
    public static function getSessionInfo(): array
    {
        if (!self::isLoggedIn()) {
            return ['logged_in' => false];
        }
        
        return [
            'logged_in' => true,
            'user_id' => $_SESSION['user_id'] ?? null,
            'login_time' => $_SESSION['login_time'] ?? null,
            'last_activity' => $_SESSION['last_activity'] ?? null,
            'session_lifetime_remaining' => self::SESSION_LIFETIME - (time() - ($_SESSION['login_time'] ?? time())),
            'session_id' => session_id(),
            'csrf_token' => $_SESSION['_csrf_token'] ?? null,
        ];
    }
}

// Override global helper functions to use SessionManager
if (!function_exists('session_login')) {
    function session_login(int $userId): void {
        \MusicLocker\Security\SessionManager::login($userId);
    }
}

if (!function_exists('session_logout')) {
    function session_logout(): void {
        \MusicLocker\Security\SessionManager::logout();
    }
}