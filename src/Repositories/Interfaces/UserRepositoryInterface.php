<?php

namespace MusicLocker\Repositories\Interfaces;

/**
 * User Repository Interface
 * 
 * Defines the contract for user data access operations
 * Following Repository Pattern and Dependency Inversion Principle
 */
interface UserRepositoryInterface
{
    /**
     * Find user by ID
     */
    public function findById(int $userId): ?array;
    
    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array;
    
    /**
     * Find user by verification token
     */
    public function findByVerificationToken(string $token): ?array;
    
    /**
     * Find user by reset token
     */
    public function findByResetToken(string $token): ?array;
    
    /**
     * Create new user
     */
    public function create(array $data): int;
    
    /**
     * Update user
     */
    public function update(int $userId, array $data): bool;
    
    /**
     * Delete user
     */
    public function delete(int $userId): bool;
    
    /**
     * Check if email exists
     */
    public function emailExists(string $email, ?int $excludeUserId = null): bool;
    
    /**
     * Update last login timestamp
     */
    public function updateLastLogin(int $userId): bool;
    
    /**
     * Update Spotify tokens
     */
    public function updateSpotifyTokens(int $userId, array $tokenData): bool;
    
    /**
     * Clear Spotify tokens
     */
    public function clearSpotifyTokens(int $userId): bool;
    
    /**
     * Get user's Spotify tokens
     */
    public function getSpotifyTokens(int $userId): ?array;
}