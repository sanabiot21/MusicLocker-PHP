<?php

namespace MusicLocker\Utils;

/**
 * Configuration Manager
 * 
 * Handles application configuration with environment support
 * Following clean architecture principles
 */
class ConfigManager
{
    private static array $config = [];
    private static bool $loaded = false;
    
    /**
     * Load configuration from environment and defaults
     */
    public static function load(): void
    {
        if (self::$loaded) return;
        
        // Load from .env file if it exists
        $envFile = ROOT_PATH . '/.env';
        if (file_exists($envFile)) {
            $env = parse_ini_file($envFile);
            foreach ($env as $key => $value) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
        
        // Set configuration values
        self::$config = [
            'app' => [
                'name' => $_ENV['APP_NAME'] ?? 'Music Locker',
                'url' => $_ENV['APP_URL'] ?? 'http://musiclocker.local',
                'debug' => (bool)($_ENV['APP_DEBUG'] ?? false),
                'env' => $_ENV['APP_ENV'] ?? 'development',
            ],
            'database' => [
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'database' => $_ENV['DB_DATABASE'] ?? 'music_locker',
                'username' => $_ENV['DB_USERNAME'] ?? 'root',
                'password' => $_ENV['DB_PASSWORD'] ?? '',
                'port' => $_ENV['DB_PORT'] ?? 3306,
            ],
            'spotify' => [
                'client_id' => $_ENV['SPOTIFY_CLIENT_ID'] ?? '356702eb81d0499381fcf5222ab757fb',
                'client_secret' => $_ENV['SPOTIFY_CLIENT_SECRET'] ?? '3a826c32f5dc41e9939b4ec3229a5647',
                'redirect_uri' => $_ENV['SPOTIFY_REDIRECT_URI'] ?? null,
                'api_base_url' => 'https://api.spotify.com/v1',
            ],
            'security' => [
                'session_lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 7200),
                'csrf_protection' => (bool)($_ENV['CSRF_PROTECTION'] ?? true),
                'rate_limit_enabled' => (bool)($_ENV['RATE_LIMIT_ENABLED'] ?? true),
            ],
            'cache' => [
                'default' => $_ENV['CACHE_DRIVER'] ?? 'file',
                'ttl' => (int)($_ENV['CACHE_TTL'] ?? 3600),
            ]
        ];
        
        self::$loaded = true;
    }
    
    /**
     * Get configuration value
     */
    public static function get(string $key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }
        
        $keys = explode('.', $key);
        $value = self::$config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    /**
     * Set configuration value
     */
    public static function set(string $key, $value): void
    {
        if (!self::$loaded) {
            self::load();
        }
        
        $keys = explode('.', $key);
        $config = &self::$config;
        
        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }
        
        $config = $value;
    }
    
    /**
     * Get all configuration
     */
    public static function all(): array
    {
        if (!self::$loaded) {
            self::load();
        }
        
        return self::$config;
    }
}

// Global helper function
if (!function_exists('config')) {
    function config(string $key, $default = null) {
        return \MusicLocker\Utils\ConfigManager::get($key, $default);
    }
}