<?php

namespace MusicLocker\Utils;

/**
 * URL Helper Class
 * 
 * Handles URL generation with Ngrok compatibility
 * Following clean architecture principles
 */
class UrlHelper
{
    private static array $routes = [
        'home' => '/',
        'login' => '/login',
        'register' => '/register',
        'logout' => '/logout',
        'forgot' => '/forgot',
        'profile' => '/profile',
        'dashboard' => '/dashboard',
        'music' => '/music',
        'music.index' => '/music',
        'music.show' => '/music/{id}',
        'music.edit' => '/music/{id}/edit',
        'music.create' => '/music/add',
        'api.music.favorite' => '/api/music/favorite',
        'api.music.play' => '/api/music/play',
        'api.spotify.search' => '/api/spotify/search',
        'spotify.callback' => '/spotify/callback',
        'admin' => '/admin',
        'admin.users' => '/admin/users',
        'admin.users.show' => '/admin/users/{id}',
        'admin.system' => '/admin/system',
    ];
    
    /**
     * Generate URL for route
     */
    public static function route(string $name, $param = null): string
    {
        $path = self::$routes[$name] ?? $name;
        
        // Replace parameter placeholder
        if ($param !== null && str_contains($path, '{id}')) {
            $path = str_replace('{id}', (string)$param, $path);
        }
        
        return self::getBaseUrl() . $path;
    }
    
    /**
     * Get base URL with Ngrok compatibility
     */
    public static function getBaseUrl(): string
    {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
                  || $_SERVER['SERVER_PORT'] == 443
                  || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        
        $isNgrok = isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], '.ngrok');
        
        $protocol = ($isNgrok || $isHttps) ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'musiclocker.local';
        
        return $protocol . $host;
    }
    
    /**
     * Build URL with query parameters
     */
    public static function buildUrl(string $base, array $params = []): string
    {
        if (empty($params)) {
            return $base;
        }
        
        $query = http_build_query($params);
        $separator = str_contains($base, '?') ? '&' : '?';
        
        return $base . $separator . $query;
    }
    
    /**
     * Check if current request is HTTPS
     */
    public static function isSecure(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || $_SERVER['SERVER_PORT'] == 443
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    }
}

// Global helper function
if (!function_exists('route_url')) {
    function route_url(string $name, $param = null): string {
        return \MusicLocker\Utils\UrlHelper::route($name, $param);
    }
}