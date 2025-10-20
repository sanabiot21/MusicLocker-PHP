<?php

namespace MusicLocker\Repositories;

use MusicLocker\Repositories\Interfaces\TagRepositoryInterface;

/**
 * Tag Repository
 * 
 * Concrete implementation of tag data access operations
 * Following Repository Pattern and Single Responsibility Principle
 */
class TagRepository extends BaseRepository implements TagRepositoryInterface
{
    /**
     * Find tag by ID for a specific user
     */
    public function findById(int $tagId, int $userId): ?array
    {
        $sql = "SELECT * FROM tags WHERE id = ? AND user_id = ?";
        return $this->queryOne($sql, [$tagId, $userId]);
    }
    
    /**
     * Get all tags for a user
     */
    public function getUserTags(int $userId): array
    {
        $sql = "SELECT * FROM tags WHERE user_id = ? ORDER BY name ASC";
        return $this->query($sql, [$userId]);
    }
    
    /**
     * Get tags by multiple IDs for a user
     */
    public function getTagsByIds(array $tagIds, int $userId): array
    {
        if (empty($tagIds)) {
            return [];
        }
        
        $placeholders = str_repeat('?,', count($tagIds) - 1) . '?';
        $params = array_merge($tagIds, [$userId]);
        
        $sql = "SELECT * FROM tags WHERE id IN ({$placeholders}) AND user_id = ? ORDER BY name ASC";
        return $this->query($sql, $params);
    }
    
    /**
     * Get popular tags for a user
     */
    public function getPopularTags(int $userId, int $limit): array
    {
        $sql = "SELECT t.*, COUNT(met.music_entry_id) as usage_count
                FROM tags t
                LEFT JOIN music_entry_tags met ON t.id = met.tag_id
                WHERE t.user_id = ?
                GROUP BY t.id
                ORDER BY usage_count DESC, t.name ASC
                LIMIT {$limit}";
        
        return $this->query($sql, [$userId]);
    }
    
    /**
     * Create new tag
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO tags (user_id, name, color, description, is_system_tag, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
        
        $params = [
            $data['user_id'],
            $this->sanitizeString($data['name']),
            $this->sanitizeString($data['color'] ?? '#6c757d'),
            $this->sanitizeString($data['description']),
            $this->sanitizeBool($data['is_system_tag'] ?? false)
        ];
        
        return $this->insert($sql, $params);
    }
    
    /**
     * Update tag
     */
    public function update(int $tagId, array $data): bool
    {
        $fields = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            if ($field === 'id' || $field === 'user_id' || $field === 'created_at') {
                continue; // Skip protected fields
            }
            
            $fields[] = "{$field} = ?";
            $params[] = $field === 'is_system_tag' ? $this->sanitizeBool($value) : $this->sanitizeString($value);
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $fields[] = "updated_at = NOW()";
        $params[] = $tagId;
        
        $sql = "UPDATE tags SET " . implode(', ', $fields) . " WHERE id = ?";
        
        return $this->execute($sql, $params) > 0;
    }
    
    /**
     * Delete tag
     */
    public function delete(int $tagId): bool
    {
        // First remove all tag associations
        $this->execute("DELETE FROM music_entry_tags WHERE tag_id = ?", [$tagId]);
        
        // Then delete the tag
        $sql = "DELETE FROM tags WHERE id = ?";
        return $this->execute($sql, [$tagId]) > 0;
    }
    
    /**
     * Check if tag name exists for user
     */
    public function nameExists(string $name, int $userId, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM tags WHERE name = ? AND user_id = ?";
        $params = [$name, $userId];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->queryOne($sql, $params);
        return (int)($result['count'] ?? 0) > 0;
    }
    
    /**
     * Get tags assigned to a music entry
     */
    public function getMusicEntryTags(int $musicEntryId): array
    {
        $sql = "SELECT t.* FROM tags t
                JOIN music_entry_tags met ON t.id = met.tag_id
                WHERE met.music_entry_id = ?
                ORDER BY t.name ASC";
        
        return $this->query($sql, [$musicEntryId]);
    }
    
    /**
     * Assign tags to music entry
     */
    public function assignToMusicEntry(int $musicEntryId, array $tagIds): bool
    {
        if (empty($tagIds)) {
            return true;
        }
        
        // Remove existing tags first
        $this->removeFromMusicEntry($musicEntryId);
        
        // Add new tags
        $values = [];
        $params = [];
        
        foreach ($tagIds as $tagId) {
            $values[] = "(?, ?, NOW())";
            $params[] = $musicEntryId;
            $params[] = $tagId;
        }
        
        $sql = "INSERT INTO music_entry_tags (music_entry_id, tag_id, created_at) VALUES " . implode(', ', $values);
        
        return $this->execute($sql, $params) > 0;
    }
    
    /**
     * Remove tags from music entry
     */
    public function removeFromMusicEntry(int $musicEntryId, array $tagIds = []): bool
    {
        if (empty($tagIds)) {
            // Remove all tags
            $sql = "DELETE FROM music_entry_tags WHERE music_entry_id = ?";
            $params = [$musicEntryId];
        } else {
            // Remove specific tags
            $placeholders = str_repeat('?,', count($tagIds) - 1) . '?';
            $sql = "DELETE FROM music_entry_tags WHERE music_entry_id = ? AND tag_id IN ({$placeholders})";
            $params = array_merge([$musicEntryId], $tagIds);
        }
        
        return $this->execute($sql, $params) >= 0; // >= 0 because removing 0 tags is still successful
    }
}