<?php

namespace MusicLocker\Repositories\Interfaces;

/**
 * Tag Repository Interface
 * 
 * Defines the contract for tag data access operations
 * Following Repository Pattern and Dependency Inversion Principle
 */
interface TagRepositoryInterface
{
    /**
     * Find tag by ID for a specific user
     */
    public function findById(int $tagId, int $userId): ?array;
    
    /**
     * Get all tags for a user
     */
    public function getUserTags(int $userId): array;
    
    /**
     * Get tags by multiple IDs for a user
     */
    public function getTagsByIds(array $tagIds, int $userId): array;
    
    /**
     * Get popular tags for a user
     */
    public function getPopularTags(int $userId, int $limit): array;
    
    /**
     * Create new tag
     */
    public function create(array $data): int;
    
    /**
     * Update tag
     */
    public function update(int $tagId, array $data): bool;
    
    /**
     * Delete tag
     */
    public function delete(int $tagId): bool;
    
    /**
     * Check if tag name exists for user
     */
    public function nameExists(string $name, int $userId, ?int $excludeId = null): bool;
    
    /**
     * Get tags assigned to a music entry
     */
    public function getMusicEntryTags(int $musicEntryId): array;
    
    /**
     * Assign tags to music entry
     */
    public function assignToMusicEntry(int $musicEntryId, array $tagIds): bool;
    
    /**
     * Remove tags from music entry
     */
    public function removeFromMusicEntry(int $musicEntryId, array $tagIds = []): bool;
}