<?php

namespace MusicLocker\Security;

/**
 * Input Validation Security Layer
 * 
 * Comprehensive input validation following security best practices
 * Prevents XSS, SQL injection, and data integrity issues
 */
class InputValidator
{
    /**
     * Validate email address
     */
    public static function email(string $email): array
    {
        $email = trim($email);
        
        if (empty($email)) {
            return ['valid' => false, 'error' => 'Email is required'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'error' => 'Invalid email format'];
        }
        
        if (strlen($email) > 255) {
            return ['valid' => false, 'error' => 'Email too long'];
        }
        
        return ['valid' => true, 'value' => strtolower($email)];
    }
    
    /**
     * Validate password strength
     */
    public static function password(string $password): array
    {
        if (strlen($password) < 8) {
            return ['valid' => false, 'error' => 'Password must be at least 8 characters'];
        }
        
        if (strlen($password) > 255) {
            return ['valid' => false, 'error' => 'Password too long'];
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            return ['valid' => false, 'error' => 'Password must contain uppercase letter'];
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            return ['valid' => false, 'error' => 'Password must contain lowercase letter'];
        }
        
        if (!preg_match('/\d/', $password)) {
            return ['valid' => false, 'error' => 'Password must contain number'];
        }
        
        return ['valid' => true, 'value' => $password];
    }
    
    /**
     * Validate music title
     */
    public static function musicTitle(string $title): array
    {
        $title = trim(strip_tags($title));
        
        if (empty($title)) {
            return ['valid' => false, 'error' => 'Title is required'];
        }
        
        if (strlen($title) > 255) {
            return ['valid' => false, 'error' => 'Title too long (max 255 characters)'];
        }
        
        // Remove dangerous characters but allow unicode
        $title = preg_replace('/[<>"\'\x00-\x1F\x7F]/', '', $title);
        
        return ['valid' => true, 'value' => $title];
    }
    
    /**
     * Validate artist name
     */
    public static function artistName(string $artist): array
    {
        $artist = trim(strip_tags($artist));
        
        if (empty($artist)) {
            return ['valid' => false, 'error' => 'Artist is required'];
        }
        
        if (strlen($artist) > 255) {
            return ['valid' => false, 'error' => 'Artist name too long'];
        }
        
        // Remove dangerous characters
        $artist = preg_replace('/[<>"\'\x00-\x1F\x7F]/', '', $artist);
        
        return ['valid' => true, 'value' => $artist];
    }
    
    /**
     * Validate rating (1-5)
     */
    public static function rating($rating): array
    {
        if ($rating === '' || $rating === null) {
            return ['valid' => true, 'value' => null];
        }
        
        $rating = (int)$rating;
        
        if ($rating < 1 || $rating > 5) {
            return ['valid' => false, 'error' => 'Rating must be between 1 and 5'];
        }
        
        return ['valid' => true, 'value' => $rating];
    }
    
    /**
     * Validate URL
     */
    public static function url(string $url): array
    {
        if (empty($url)) {
            return ['valid' => true, 'value' => null];
        }
        
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['valid' => false, 'error' => 'Invalid URL format'];
        }
        
        // Only allow HTTP/HTTPS
        $parsed = parse_url($url);
        if (!in_array($parsed['scheme'] ?? '', ['http', 'https'])) {
            return ['valid' => false, 'error' => 'Only HTTP/HTTPS URLs allowed'];
        }
        
        return ['valid' => true, 'value' => $url];
    }
    
    /**
     * Validate integer within range
     */
    public static function integer($value, int $min = null, int $max = null): array
    {
        if ($value === '' || $value === null) {
            return ['valid' => true, 'value' => null];
        }
        
        if (!is_numeric($value)) {
            return ['valid' => false, 'error' => 'Must be a number'];
        }
        
        $value = (int)$value;
        
        if ($min !== null && $value < $min) {
            return ['valid' => false, 'error' => "Must be at least {$min}"];
        }
        
        if ($max !== null && $value > $max) {
            return ['valid' => false, 'error' => "Must be at most {$max}"];
        }
        
        return ['valid' => true, 'value' => $value];
    }
    
    /**
     * Validate text field with length limits
     */
    public static function text(string $text, int $maxLength = 1000, bool $required = false): array
    {
        $text = trim($text);
        
        if ($required && empty($text)) {
            return ['valid' => false, 'error' => 'This field is required'];
        }
        
        if (strlen($text) > $maxLength) {
            return ['valid' => false, 'error' => "Text too long (max {$maxLength} characters)"];
        }
        
        // Strip dangerous HTML but allow basic formatting
        $allowedTags = '<br><p><strong><em><u>';
        $text = strip_tags($text, $allowedTags);
        
        return ['valid' => true, 'value' => $text];
    }
    
    /**
     * Validate search query
     */
    public static function searchQuery(string $query): array
    {
        $query = trim(strip_tags($query));
        
        if (strlen($query) > 255) {
            return ['valid' => false, 'error' => 'Search query too long'];
        }
        
        // Remove special characters that could break search
        $query = preg_replace('/[<>"\'\x00-\x1F\x7F]/', '', $query);
        
        return ['valid' => true, 'value' => $query];
    }
    
    /**
     * Validate array of IDs
     */
    public static function idArray(array $ids, int $maxCount = 100): array
    {
        if (count($ids) > $maxCount) {
            return ['valid' => false, 'error' => "Too many items (max {$maxCount})"];
        }
        
        $validIds = [];
        foreach ($ids as $id) {
            if (is_numeric($id) && (int)$id > 0) {
                $validIds[] = (int)$id;
            }
        }
        
        return ['valid' => true, 'value' => array_unique($validIds)];
    }
    
    /**
     * Validate date
     */
    public static function date(string $date): array
    {
        if (empty($date)) {
            return ['valid' => true, 'value' => null];
        }
        
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return ['valid' => false, 'error' => 'Invalid date format'];
        }
        
        // Check reasonable date range (1900 to current year + 1)
        $year = (int)date('Y', $timestamp);
        $currentYear = (int)date('Y');
        
        if ($year < 1900 || $year > ($currentYear + 1)) {
            return ['valid' => false, 'error' => 'Date out of valid range'];
        }
        
        return ['valid' => true, 'value' => date('Y-m-d', $timestamp)];
    }
}

// Global helper function for validation
if (!function_exists('validate_input')) {
    function validate_input(string $type, $value, array $options = []): array {
        return match($type) {
            'email' => \MusicLocker\Security\InputValidator::email($value),
            'password' => \MusicLocker\Security\InputValidator::password($value),
            'title' => \MusicLocker\Security\InputValidator::musicTitle($value),
            'artist' => \MusicLocker\Security\InputValidator::artistName($value),
            'rating' => \MusicLocker\Security\InputValidator::rating($value),
            'url' => \MusicLocker\Security\InputValidator::url($value),
            'integer' => \MusicLocker\Security\InputValidator::integer($value, $options['min'] ?? null, $options['max'] ?? null),
            'text' => \MusicLocker\Security\InputValidator::text($value, $options['max_length'] ?? 1000, $options['required'] ?? false),
            'search' => \MusicLocker\Security\InputValidator::searchQuery($value),
            'date' => \MusicLocker\Security\InputValidator::date($value),
            default => ['valid' => false, 'error' => 'Unknown validation type']
        };
    }
}