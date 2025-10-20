<?php

namespace MusicLocker\Models;

use MusicLocker\Services\Database;
use Exception;

/**
 * Tag Model
 * Handles tag operations for music entries
 */
class Tag
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all tags for a user
     */
    public function getUserTags(int $userId): array
    {
        try {
            $sql = "SELECT * FROM tags WHERE user_id = ? ORDER BY name ASC";
            return $this->db->query($sql, [$userId]);
        } catch (Exception $e) {
            error_log("Get user tags error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get or create a tag for a user
     */
    public function getOrCreateTag(int $userId, string $tagName): ?array
    {
        try {
            // Check if tag exists
            $sql = "SELECT * FROM tags WHERE user_id = ? AND name = ?";
            $existing = $this->db->queryOne($sql, [$userId, $tagName]);
            
            if ($existing) {
                return $existing;
            }
            
            // Create new tag
            $sql = "INSERT INTO tags (user_id, name, color, description, is_system_tag, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId,
                $tagName,
                '#6c757d', // default gray color
                null,
                false
            ]);
            
            $tagId = (int)$this->db->lastInsertId();
            
            // Return the created tag
            return $this->getTagById($tagId, $userId);
            
        } catch (Exception $e) {
            error_log("Get or create tag error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get tag by ID for a specific user
     */
    public function getTagById(int $tagId, int $userId): ?array
    {
        try {
            $sql = "SELECT * FROM tags WHERE id = ? AND user_id = ?";
            return $this->db->queryOne($sql, [$tagId, $userId]);
        } catch (Exception $e) {
            error_log("Get tag by ID error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create new tag
     */
    public function create(int $userId, string $name, ?string $color = null, ?string $description = null): ?int
    {
        try {
            $sql = "INSERT INTO tags (user_id, name, color, description, is_system_tag, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId,
                $name,
                $color ?? '#6c757d',
                $description,
                false
            ]);
            
            return (int)$this->db->lastInsertId();
            
        } catch (Exception $e) {
            error_log("Create tag error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update tag
     */
    public function update(int $tagId, int $userId, array $data): bool
    {
        try {
            $fields = [];
            $params = [];
            
            foreach ($data as $field => $value) {
                if ($field === 'id' || $field === 'user_id' || $field === 'created_at') {
                    continue; // Skip protected fields
                }
                
                $fields[] = "{$field} = ?";
                $params[] = $value;
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $fields[] = "updated_at = NOW()";
            $params[] = $tagId;
            $params[] = $userId;
            
            $sql = "UPDATE tags SET " . implode(', ', $fields) . " WHERE id = ? AND user_id = ?";
            
            return $this->db->execute($sql, $params);
            
        } catch (Exception $e) {
            error_log("Update tag error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete tag
     */
    public function delete(int $tagId, int $userId): bool
    {
        try {
            // First remove all tag associations
            $this->db->execute("DELETE FROM music_entry_tags WHERE tag_id = ?", [$tagId]);
            
            // Then delete the tag
            $sql = "DELETE FROM tags WHERE id = ? AND user_id = ?";
            return $this->db->execute($sql, [$tagId, $userId]);
            
        } catch (Exception $e) {
            error_log("Delete tag error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if tag name exists for user
     */
    public function nameExists(string $name, int $userId, ?int $excludeId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM tags WHERE name = ? AND user_id = ?";
            $params = [$name, $userId];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $result = $this->db->queryOne($sql, $params);
            return (int)($result['count'] ?? 0) > 0;
            
        } catch (Exception $e) {
            error_log("Check tag name exists error: " . $e->getMessage());
            return false;
        }
    }
}
