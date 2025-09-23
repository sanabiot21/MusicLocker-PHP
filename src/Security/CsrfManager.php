<?php

namespace MusicLocker\Security;

/**
 * CSRF Protection Manager
 * 
 * Handles CSRF token generation and validation
 * Following security best practices
 */
class CsrfManager
{
    private const TOKEN_KEY = '_csrf_token';
    
    /**
     * Generate CSRF token
     */
    public static function generateToken(): string
    {
        if (!isset($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::TOKEN_KEY];
    }
    
    /**
     * Get current CSRF token
     */
    public static function getToken(): string
    {
        return self::generateToken();
    }
    
    /**
     * Generate CSRF field HTML
     */
    public static function field(): string
    {
        return '<input type="hidden" name="_token" value="' . self::getToken() . '">';
    }
    
    /**
     * Validate CSRF token
     */
    public static function validate(string $token): bool
    {
        return isset($_SESSION[self::TOKEN_KEY]) 
            && hash_equals($_SESSION[self::TOKEN_KEY], $token);
    }
    
    /**
     * Validate CSRF from request
     */
    public static function validateFromRequest(): bool
    {
        $token = $_POST['_token'] ?? $_GET['_token'] ?? '';
        return self::validate($token);
    }
}

// Global helper functions for CSRF
if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        return \MusicLocker\Security\CsrfManager::getToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        return \MusicLocker\Security\CsrfManager::field();
    }
}

if (!function_exists('validate_csrf')) {
    function validate_csrf(string $token): bool {
        return \MusicLocker\Security\CsrfManager::validate($token);
    }
}