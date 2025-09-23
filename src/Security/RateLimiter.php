<?php

namespace MusicLocker\Security;

/**
 * Rate Limiter
 * 
 * Protects against abuse and DoS attacks
 * Uses sliding window approach with file-based storage
 */
class RateLimiter
{
    private const CACHE_DIR = ROOT_PATH . '/storage/cache/rate_limits';
    
    private static array $limits = [
        'api' => ['requests' => 100, 'window' => 3600], // 100 requests per hour
        'login' => ['requests' => 5, 'window' => 900],   // 5 attempts per 15 minutes
        'search' => ['requests' => 60, 'window' => 3600], // 60 searches per hour
        'register' => ['requests' => 3, 'window' => 3600], // 3 registrations per hour
    ];
    
    /**
     * Check if request is allowed
     */
    public static function isAllowed(string $key, string $identifier, string $type = 'api'): bool
    {
        $limit = self::$limits[$type] ?? self::$limits['api'];
        $cacheKey = self::getCacheKey($key, $identifier, $type);
        
        // Create cache directory if it doesn't exist
        if (!is_dir(self::CACHE_DIR)) {
            mkdir(self::CACHE_DIR, 0755, true);
        }
        
        $now = time();
        $windowStart = $now - $limit['window'];
        
        // Get current request log
        $requests = self::getRequestLog($cacheKey);
        
        // Remove old requests outside the window
        $requests = array_filter($requests, fn($timestamp) => $timestamp > $windowStart);
        
        // Check if limit exceeded
        if (count($requests) >= $limit['requests']) {
            return false;
        }
        
        // Add current request
        $requests[] = $now;
        
        // Save updated log
        self::saveRequestLog($cacheKey, $requests);
        
        return true;
    }
    
    /**
     * Get remaining requests for a key
     */
    public static function getRemaining(string $key, string $identifier, string $type = 'api'): int
    {
        $limit = self::$limits[$type] ?? self::$limits['api'];
        $cacheKey = self::getCacheKey($key, $identifier, $type);
        
        $now = time();
        $windowStart = $now - $limit['window'];
        
        $requests = self::getRequestLog($cacheKey);
        $currentRequests = array_filter($requests, fn($timestamp) => $timestamp > $windowStart);
        
        return max(0, $limit['requests'] - count($currentRequests));
    }
    
    /**
     * Get time until rate limit resets
     */
    public static function getResetTime(string $key, string $identifier, string $type = 'api'): int
    {
        $limit = self::$limits[$type] ?? self::$limits['api'];
        $cacheKey = self::getCacheKey($key, $identifier, $type);
        
        $requests = self::getRequestLog($cacheKey);
        if (empty($requests)) {
            return 0;
        }
        
        $oldestRequest = min($requests);
        $resetTime = $oldestRequest + $limit['window'];
        
        return max(0, $resetTime - time());
    }
    
    /**
     * Clear rate limit for a key (admin function)
     */
    public static function clear(string $key, string $identifier, string $type = 'api'): void
    {
        $cacheKey = self::getCacheKey($key, $identifier, $type);
        $filePath = self::CACHE_DIR . '/' . $cacheKey . '.json';
        
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    
    /**
     * Middleware function for API endpoints
     */
    public static function middleware(string $type = 'api'): void
    {
        $identifier = self::getClientIdentifier();
        $key = $_SERVER['REQUEST_URI'] ?? 'unknown';
        
        if (!self::isAllowed($key, $identifier, $type)) {
            $resetTime = self::getResetTime($key, $identifier, $type);
            $limit = self::$limits[$type] ?? self::$limits['api'];
            
            http_response_code(429);
            header('X-RateLimit-Limit: ' . $limit['requests']);
            header('X-RateLimit-Remaining: 0');
            header('X-RateLimit-Reset: ' . (time() + $resetTime));
            header('Retry-After: ' . $resetTime);
            
            json_response([
                'error' => 'Rate limit exceeded',
                'retry_after' => $resetTime,
                'limit' => $limit['requests'],
                'window' => $limit['window']
            ], 429);
        }
        
        // Add rate limit headers for successful requests
        $remaining = self::getRemaining($key, $identifier, $type);
        $limit = self::$limits[$type] ?? self::$limits['api'];
        
        header('X-RateLimit-Limit: ' . $limit['requests']);
        header('X-RateLimit-Remaining: ' . $remaining);
        header('X-RateLimit-Window: ' . $limit['window']);
    }
    
    /**
     * Get client identifier for rate limiting
     */
    private static function getClientIdentifier(): string
    {
        // Use user ID if logged in, otherwise IP address
        if (function_exists('current_user_id') && current_user_id()) {
            return 'user:' . current_user_id();
        }
        
        // Get real IP address (considering proxies)
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 
              $_SERVER['HTTP_X_REAL_IP'] ?? 
              $_SERVER['REMOTE_ADDR'] ?? 
              'unknown';
        
        // Take first IP if there are multiple
        $ip = explode(',', $ip)[0];
        $ip = trim($ip);
        
        // Anonymize IPv6 addresses
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $ip = substr($ip, 0, 19); // Keep first 19 chars
        }
        
        return 'ip:' . $ip;
    }
    
    /**
     * Generate cache key
     */
    private static function getCacheKey(string $key, string $identifier, string $type): string
    {
        return md5($type . ':' . $identifier . ':' . $key);
    }
    
    /**
     * Get request log from file
     */
    private static function getRequestLog(string $cacheKey): array
    {
        $filePath = self::CACHE_DIR . '/' . $cacheKey . '.json';
        
        if (!file_exists($filePath)) {
            return [];
        }
        
        $content = file_get_contents($filePath);
        $data = json_decode($content, true);
        
        return is_array($data) ? $data : [];
    }
    
    /**
     * Save request log to file
     */
    private static function saveRequestLog(string $cacheKey, array $requests): void
    {
        $filePath = self::CACHE_DIR . '/' . $cacheKey . '.json';
        file_put_contents($filePath, json_encode($requests), LOCK_EX);
    }
    
    /**
     * Clean up old rate limit files (run periodically)
     */
    public static function cleanup(): void
    {
        if (!is_dir(self::CACHE_DIR)) {
            return;
        }
        
        $files = glob(self::CACHE_DIR . '/*.json');
        $maxAge = 86400; // 24 hours
        
        foreach ($files as $file) {
            if (time() - filemtime($file) > $maxAge) {
                unlink($file);
            }
        }
    }
}

// Helper function for easy middleware use
if (!function_exists('rate_limit')) {
    function rate_limit(string $type = 'api'): void {
        \MusicLocker\Security\RateLimiter::middleware($type);
    }
}