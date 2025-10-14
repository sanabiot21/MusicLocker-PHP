<?php

namespace MusicLocker\Models;

use MusicLocker\Services\Database;
use PDO;
use Exception;

/**
 * Playlist Model
 * Music Locker - Team NaturalStupidity
 * 
 * Handles user playlists
 */
class Playlist
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create new playlist
     */
    public function create(array $data): ?int
    {
        try {
            $sql = "INSERT INTO playlists (
                        user_id, name, description, is_public, cover_image_url
                    ) VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['user_id'],
                $data['name'],
                $data['description'] ?? null,
                $data['is_public'] ?? false,
                $data['cover_image_url'] ?? null
            ]);
            
            if ($result) {
                return (int)$this->db->lastInsertId();
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log("Playlist creation error: " . $e->getMessage());
            throw new Exception("Failed to create playlist");
        }
    }
    
    /**
     * Find playlist by ID
     */
    public function findById(int $id, int $userId = null): ?array
    {
        try {
            $sql = "SELECT p.*, 
                           COUNT(DISTINCT pe.id) as track_count,
                           SUM(me.duration) as total_duration
                    FROM playlists p
                    LEFT JOIN playlist_entries pe ON p.id = pe.playlist_id
                    LEFT JOIN music_entries me ON pe.music_entry_id = me.id
                    WHERE p.id = ?";
            $params = [$id];
            
            if ($userId !== null) {
                $sql .= " AND p.user_id = ?";
                $params[] = $userId;
            }
            
            $sql .= " GROUP BY p.id LIMIT 1";
            
            return $this->db->queryOne($sql, $params);
            
        } catch (Exception $e) {
            error_log("Playlist lookup error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get user's playlists
     */
    public function getUserPlaylists(int $userId, array $options = []): array
    {
        try {
            $sql = "SELECT p.*, 
                           COUNT(DISTINCT pe.id) as track_count,
                           SUM(me.duration) as total_duration
                    FROM playlists p
                    LEFT JOIN playlist_entries pe ON p.id = pe.playlist_id
                    LEFT JOIN music_entries me ON pe.music_entry_id = me.id
                    WHERE p.user_id = ?
                    GROUP BY p.id
                    ORDER BY p.updated_at DESC";
            
            $limit = $options['limit'] ?? 50;
            $offset = $options['offset'] ?? 0;
            
            $sql .= " LIMIT ? OFFSET ?";
            
            return $this->db->query($sql, [$userId, $limit, $offset]);
            
        } catch (Exception $e) {
            error_log("Get user playlists error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update playlist
     */
    public function update(int $id, array $data, int $userId = null): bool
    {
        try {
            $updateFields = [];
            $params = [];
            
            $allowedFields = ['name', 'description', 'is_public', 'cover_image_url'];
            
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
            
            $sql = "UPDATE playlists SET " . implode(', ', $updateFields) . " WHERE id = ?";
            
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            return $this->db->execute($sql, $params);
            
        } catch (Exception $e) {
            error_log("Playlist update error: " . $e->getMessage());
            throw new Exception("Failed to update playlist");
        }
    }
    
    /**
     * Delete playlist
     */
    public function delete(int $id, int $userId = null): bool
    {
        try {
            $sql = "DELETE FROM playlists WHERE id = ?";
            $params = [$id];
            
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            return $this->db->execute($sql, $params);
            
        } catch (Exception $e) {
            error_log("Playlist deletion error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get playlist entries with full music info
     */
    public function getPlaylistEntries(int $playlistId, int $userId = null): array
    {
        try {
            $sql = "SELECT pe.*, me.*, pe.position, pe.id as entry_id
                    FROM playlist_entries pe
                    INNER JOIN music_entries me ON pe.music_entry_id = me.id
                    INNER JOIN playlists p ON pe.playlist_id = p.id
                    WHERE pe.playlist_id = ?";
            $params = [$playlistId];
            
            if ($userId !== null) {
                $sql .= " AND p.user_id = ?";
                $params[] = $userId;
            }
            
            $sql .= " ORDER BY pe.position ASC";
            
            return $this->db->query($sql, $params);
            
        } catch (Exception $e) {
            error_log("Get playlist entries error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Add track to playlist
     */
    public function addTrack(int $playlistId, int $musicEntryId, int $userId): bool
    {
        try {
            // Get next position
            $sql = "SELECT COALESCE(MAX(position), 0) + 1 as next_position 
                    FROM playlist_entries 
                    WHERE playlist_id = ?";
            $result = $this->db->queryOne($sql, [$playlistId]);
            $position = $result['next_position'];
            
            // Insert entry
            $sql = "INSERT INTO playlist_entries (playlist_id, music_entry_id, position, added_by_user_id)
                    VALUES (?, ?, ?, ?)";
            
            $success = $this->db->execute($sql, [$playlistId, $musicEntryId, $position, $userId]);
            
            if ($success) {
                // Update playlist timestamp
                $this->db->execute("UPDATE playlists SET updated_at = NOW() WHERE id = ?", [$playlistId]);
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("Add track to playlist error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remove track from playlist
     */
    public function removeTrack(int $playlistId, int $entryId, int $userId = null): bool
    {
        try {
            $sql = "DELETE pe FROM playlist_entries pe
                    INNER JOIN playlists p ON pe.playlist_id = p.id
                    WHERE pe.id = ? AND pe.playlist_id = ?";
            $params = [$entryId, $playlistId];
            
            if ($userId !== null) {
                $sql .= " AND p.user_id = ?";
                $params[] = $userId;
            }
            
            $success = $this->db->execute($sql, $params);
            
            if ($success) {
                // Reorder remaining entries
                $this->reorderPlaylist($playlistId);
                // Update playlist timestamp
                $this->db->execute("UPDATE playlists SET updated_at = NOW() WHERE id = ?", [$playlistId]);
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("Remove track from playlist error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reorder playlist entries
     */
    private function reorderPlaylist(int $playlistId): void
    {
        try {
            // Get all entries in order
            $sql = "SELECT id FROM playlist_entries WHERE playlist_id = ? ORDER BY position ASC";
            $entries = $this->db->query($sql, [$playlistId]);
            
            // Update positions
            $position = 1;
            foreach ($entries as $entry) {
                $this->db->execute(
                    "UPDATE playlist_entries SET position = ? WHERE id = ?",
                    [$position, $entry['id']]
                );
                $position++;
            }
            
        } catch (Exception $e) {
            error_log("Reorder playlist error: " . $e->getMessage());
        }
    }
    
    /**
     * Get user's playlist count
     */
    public function getUserPlaylistCount(int $userId): int
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM playlists WHERE user_id = ?";
            $result = $this->db->queryOne($sql, [$userId]);
            
            return (int)($result['count'] ?? 0);
            
        } catch (Exception $e) {
            error_log("Get playlist count error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Validate playlist data
     */
    public function validate(array $data, array $rules = []): array
    {
        $errors = [];
        
        // Name is required
        if (isset($rules['name']) && $rules['name']) {
            if (empty($data['name'])) {
                $errors['name'] = 'Playlist name is required';
            } elseif (strlen($data['name']) > 255) {
                $errors['name'] = 'Playlist name is too long (max 255 characters)';
            }
        }
        
        return $errors;
    }
}





