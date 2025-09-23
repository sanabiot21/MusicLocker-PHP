<?php

namespace MusicLocker\Repositories;

use MusicLocker\Repositories\Interfaces\UserRepositoryInterface;

/**
 * User Repository
 * 
 * Concrete implementation of user data access operations
 * Following Repository Pattern and Single Responsibility Principle
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Find user by ID
     */
    public function findById(int $userId): ?array
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->queryOne($sql, [$userId]);
    }
    
    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        return $this->queryOne($sql, [$email]);
    }
    
    /**
     * Find user by verification token
     */
    public function findByVerificationToken(string $token): ?array
    {
        $sql = "SELECT * FROM users WHERE verification_token = ? AND email_verified = 0";
        return $this->queryOne($sql, [$token]);
    }
    
    /**
     * Find user by reset token
     */
    public function findByResetToken(string $token): ?array
    {
        $sql = "SELECT * FROM users WHERE reset_token = ? AND reset_token_expires > NOW()";
        return $this->queryOne($sql, [$token]);
    }
    
    /**
     * Create new user
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO users (
                    first_name, last_name, email, password_hash, 
                    email_verified, verification_token, status, 
                    created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $params = [
            $this->sanitizeString($data['first_name']),
            $this->sanitizeString($data['last_name']),
            $this->sanitizeString($data['email']),
            $data['password_hash'], // Already hashed, don't sanitize
            $this->sanitizeBool($data['email_verified'] ?? false),
            $this->sanitizeString($data['verification_token']),
            $this->sanitizeString($data['status'] ?? 'active')
        ];
        
        return $this->insert($sql, $params);
    }
    
    /**
     * Update user
     */
    public function update(int $userId, array $data): bool
    {
        $fields = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            if ($field === 'id' || $field === 'created_at') {
                continue; // Skip protected fields
            }
            
            $fields[] = "{$field} = ?";
            
            // Handle specific field types
            switch ($field) {
                case 'email_verified':
                    $params[] = $this->sanitizeBool($value);
                    break;
                case 'password_hash':
                    $params[] = $value; // Don't sanitize hashed passwords
                    break;
                default:
                    $params[] = $this->sanitizeString($value);
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $fields[] = "updated_at = NOW()";
        $params[] = $userId;
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        
        return $this->execute($sql, $params) > 0;
    }
    
    /**
     * Delete user
     */
    public function delete(int $userId): bool
    {
        // Note: This will cascade delete music entries due to foreign key constraints
        $sql = "DELETE FROM users WHERE id = ?";
        return $this->execute($sql, [$userId]) > 0;
    }
    
    /**
     * Check if email exists
     */
    public function emailExists(string $email, int $excludeUserId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
        $params = [$email];
        
        if ($excludeUserId) {
            $sql .= " AND id != ?";
            $params[] = $excludeUserId;
        }
        
        $result = $this->queryOne($sql, $params);
        return (int)($result['count'] ?? 0) > 0;
    }
    
    /**
     * Update last login timestamp
     */
    public function updateLastLogin(int $userId): bool
    {
        $sql = "UPDATE users SET last_login = NOW(), updated_at = NOW() WHERE id = ?";
        return $this->execute($sql, [$userId]) > 0;
    }
    
    /**
     * Update Spotify tokens
     */
    public function updateSpotifyTokens(int $userId, array $tokenData): bool
    {
        $sql = "UPDATE users SET 
                    spotify_access_token = ?, 
                    spotify_refresh_token = ?, 
                    spotify_token_expires = ?,
                    spotify_user_id = ?,
                    updated_at = NOW() 
                WHERE id = ?";
        
        $params = [
            $tokenData['access_token'] ?? null,
            $tokenData['refresh_token'] ?? null,
            $tokenData['expires_at'] ?? null,
            $this->sanitizeString($tokenData['spotify_user_id'] ?? null),
            $userId
        ];
        
        return $this->execute($sql, $params) > 0;
    }
    
    /**
     * Clear Spotify tokens
     */
    public function clearSpotifyTokens(int $userId): bool
    {
        $sql = "UPDATE users SET 
                    spotify_access_token = NULL, 
                    spotify_refresh_token = NULL, 
                    spotify_token_expires = NULL,
                    spotify_user_id = NULL,
                    updated_at = NOW() 
                WHERE id = ?";
        
        return $this->execute($sql, [$userId]) > 0;
    }
    
    /**
     * Get user's Spotify tokens
     */
    public function getSpotifyTokens(int $userId): ?array
    {
        $sql = "SELECT spotify_access_token, spotify_refresh_token, 
                       spotify_token_expires, spotify_user_id
                FROM users WHERE id = ?";
        
        $result = $this->queryOne($sql, [$userId]);
        
        if (!$result || !$result['spotify_access_token']) {
            return null;
        }
        
        return [
            'access_token' => $result['spotify_access_token'],
            'refresh_token' => $result['spotify_refresh_token'],
            'expires_at' => $result['spotify_token_expires'],
            'spotify_user_id' => $result['spotify_user_id']
        ];
    }
}