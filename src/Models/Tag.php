<?php

namespace MusicLocker\Models;

use MusicLocker\Services\Database;
use PDO;
use Exception;

/**
 * Tag Model
 * Music Locker - Team NaturalStupidity
 * 
 * Handles user-defined tags for music organization
 */
class Tag
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create new tag
     */
    public function create(array $data): ?int
    {
        try {
            $sql = "INSERT INTO tags (user_id, name, color, description, is_system_tag, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['user_id'],
                $data['name'],
                $data['color'] ?? '#6c757d',
                $data['description'] ?? '',
                $data['is_system_tag'] ?? false
            ]);
            
            return $result ? (int)$this->db->lastInsertId() : null;
            
        } catch (Exception $e) {
            error_log("Tag creation error: " . $e->getMessage());
            throw new Exception("Failed to create tag");
        }
    }
    
    /**
     * Find tag by ID
     */
    public function findById(int $id, int $userId = null): ?array
    {
        try {
            $sql = "SELECT * FROM tags WHERE id = ?";
            $params = [$id];
            
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            $sql .= " LIMIT 1";
            
            return $this->db->queryOne($sql, $params);
            
        } catch (Exception $e) {
            error_log("Tag lookup error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get all tags for a user
     */
    public function getUserTags(int $userId, bool $includeSystem = true): array
    {
        try {
            $sql = "SELECT t.*, 
                           COUNT(met.music_entry_id) as usage_count
                    FROM tags t
                    LEFT JOIN music_entry_tags met ON t.id = met.tag_id
                    WHERE t.user_id = ?";
            
            $params = [$userId];
            
            if (!$includeSystem) {
                $sql .= " AND t.is_system_tag = FALSE";
            }
            
            $sql .= " GROUP BY t.id ORDER BY t.is_system_tag DESC, t.name ASC";
            
            return $this->db->query($sql, $params);
            
        } catch (Exception $e) {
            error_log("User tags query error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Find tag by name for user
     */
    public function findByName(string $name, int $userId): ?array
    {
        try {
            $sql = "SELECT * FROM tags WHERE user_id = ? AND name = ? LIMIT 1";
            return $this->db->queryOne($sql, [$userId, $name]);
            
        } catch (Exception $e) {
            error_log("Tag name lookup error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update tag
     */
    public function update(int $id, array $data, int $userId = null): bool
    {
        try {
            $updateFields = [];
            $params = [];
            
            $allowedFields = ['name', 'color', 'description'];
            
            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $data)) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($updateFields)) {
                return false;
            }
            
            $updateFields[] = "updated_at = NOW()";
            $params[] = $id;
            
            $sql = "UPDATE tags SET " . implode(', ', $updateFields) . " WHERE id = ?";
            
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            return $this->db->execute($sql, $params);
            
        } catch (Exception $e) {
            error_log("Tag update error: " . $e->getMessage());
            throw new Exception("Failed to update tag");
        }
    }
    
    /**
     * Delete tag
     */
    public function delete(int $id, int $userId = null): bool
    {
        try {
            // Check if this is a system tag
            $tag = $this->findById($id, $userId);
            if ($tag && $tag['is_system_tag']) {
                throw new Exception("Cannot delete system tags");
            }
            
            $sql = "DELETE FROM tags WHERE id = ?";
            $params = [$id];
            
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            return $this->db->execute($sql, $params);
            
        } catch (Exception $e) {
            error_log("Tag deletion error: " . $e->getMessage());
            throw new Exception("Failed to delete tag");
        }
    }
    
    /**
     * Get most used tags for user
     */
    public function getPopularTags(int $userId, int $limit = 10): array
    {
        try {
            $sql = "SELECT t.*, COUNT(met.music_entry_id) as usage_count
                    FROM tags t
                    LEFT JOIN music_entry_tags met ON t.id = met.tag_id
                    WHERE t.user_id = ? 
                    GROUP BY t.id 
                    HAVING usage_count > 0
                    ORDER BY usage_count DESC, t.name ASC 
                    LIMIT ?";
            
            return $this->db->query($sql, [$userId, $limit]);
            
        } catch (Exception $e) {
            error_log("Popular tags query error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get tags with their usage statistics
     */
    public function getTagsWithStats(int $userId): array
    {
        try {
            $sql = "SELECT t.*, 
                           COUNT(met.music_entry_id) as usage_count,
                           COUNT(CASE WHEN me.is_favorite = TRUE THEN 1 END) as favorites_count,
                           AVG(me.personal_rating) as average_rating,
                           MAX(me.date_added) as latest_usage
                    FROM tags t
                    LEFT JOIN music_entry_tags met ON t.id = met.tag_id
                    LEFT JOIN music_entries me ON met.music_entry_id = me.id
                    WHERE t.user_id = ? 
                    GROUP BY t.id 
                    ORDER BY t.is_system_tag DESC, usage_count DESC, t.name ASC";
            
            $results = $this->db->query($sql, [$userId]);
            
            // Format the results
            return array_map(function($tag) {
                $tag['usage_count'] = (int)$tag['usage_count'];
                $tag['favorites_count'] = (int)$tag['favorites_count'];
                $tag['average_rating'] = $tag['average_rating'] ? round((float)$tag['average_rating'], 1) : null;
                $tag['is_system_tag'] = (bool)$tag['is_system_tag'];
                return $tag;
            }, $results);
            
        } catch (Exception $e) {
            error_log("Tags with stats query error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Search tags by name
     */
    public function searchTags(int $userId, string $query, int $limit = 10): array
    {
        try {
            $sql = "SELECT * FROM tags 
                    WHERE user_id = ? AND name LIKE ? 
                    ORDER BY is_system_tag DESC, name ASC 
                    LIMIT ?";
            
            $searchTerm = '%' . $query . '%';
            return $this->db->query($sql, [$userId, $searchTerm, $limit]);
            
        } catch (Exception $e) {
            error_log("Tag search error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get or create tag by name
     */
    public function getOrCreateTag(int $userId, string $name, string $color = '#6c757d'): array
    {
        try {
            // First, try to find existing tag
            $existing = $this->findByName($name, $userId);
            if ($existing) {
                return $existing;
            }
            
            // Create new tag
            $tagData = [
                'user_id' => $userId,
                'name' => $name,
                'color' => $color,
                'description' => "Auto-created tag for '$name'",
                'is_system_tag' => false
            ];
            
            $tagId = $this->create($tagData);
            
            if ($tagId) {
                return $this->findById($tagId, $userId);
            }
            
            throw new Exception("Failed to create tag");
            
        } catch (Exception $e) {
            error_log("Get or create tag error: " . $e->getMessage());
            throw new Exception("Failed to get or create tag");
        }
    }
    
    /**
     * Validate tag data
     */
    public function validate(array $data, array $rules = []): array
    {
        $errors = [];
        
        // Name validation
        if (isset($rules['name']) && $rules['name']) {
            if (empty($data['name'])) {
                $errors['name'] = 'Tag name is required';
            } elseif (strlen($data['name']) < 2) {
                $errors['name'] = 'Tag name must be at least 2 characters';
            } elseif (strlen($data['name']) > 50) {
                $errors['name'] = 'Tag name cannot exceed 50 characters';
            } elseif (!preg_match('/^[a-zA-Z0-9\s\-_]+$/', $data['name'])) {
                $errors['name'] = 'Tag name can only contain letters, numbers, spaces, hyphens, and underscores';
            }
        }
        
        // Color validation
        if (!empty($data['color'])) {
            if (!preg_match('/^#[0-9a-fA-F]{6}$/', $data['color'])) {
                $errors['color'] = 'Color must be a valid hex color code (e.g., #FF0000)';
            }
        }
        
        // Description validation
        if (!empty($data['description']) && strlen($data['description']) > 255) {
            $errors['description'] = 'Description cannot exceed 255 characters';
        }
        
        // Check for duplicate names (if user_id provided)
        if (isset($data['user_id']) && !empty($data['name']) && empty($errors['name'])) {
            $existing = $this->findByName($data['name'], $data['user_id']);
            if ($existing && (!isset($data['id']) || $existing['id'] != $data['id'])) {
                $errors['name'] = 'A tag with this name already exists';
            }
        }
        
        return $errors;
    }
    
    /**
     * Create default tags for new user (called from User model)
     */
    public function createDefaultTags(int $userId): void
    {
        $defaultTags = [
            ['Favorites', '#ff6b6b', 'Personal favorite tracks'],
            ['Chill', '#4ecdc4', 'Relaxing and calm music'],
            ['Workout', '#45b7d1', 'High energy tracks for exercise'],
            ['Study', '#96ceb4', 'Focus music for studying'],
            ['Party', '#feca57', 'Upbeat music for social gatherings'],
            ['Nostalgic', '#ff9ff3', 'Music that brings back memories'],
            ['Discover', '#00d4ff', 'Recently discovered tracks'],
            ['Top Rated', '#8a2be2', '5-star personal ratings']
        ];
        
        foreach ($defaultTags as $tagData) {
            try {
                $this->create([
                    'user_id' => $userId,
                    'name' => $tagData[0],
                    'color' => $tagData[1],
                    'description' => $tagData[2],
                    'is_system_tag' => true
                ]);
            } catch (Exception $e) {
                error_log("Error creating default tag '{$tagData[0]}': " . $e->getMessage());
            }
        }
    }
    
    /**
     * Get color palette for tag creation
     */
    public function getColorPalette(): array
    {
        return [
            '#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57',
            '#ff9ff3', '#00d4ff', '#8a2be2', '#f39c12', '#e74c3c',
            '#9b59b6', '#3498db', '#1abc9c', '#2ecc71', '#f1c40f',
            '#e67e22', '#95a5a6', '#34495e', '#d63031', '#00b894'
        ];
    }
}