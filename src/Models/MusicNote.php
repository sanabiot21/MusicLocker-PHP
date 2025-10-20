<?php

namespace MusicLocker\Models;

use MusicLocker\Services\Database;
use PDO;
use Exception;

/**
 * Music Note Model
 * Music Locker - Team NaturalStupidity
 * 
 * Handles personal notes for music entries
 */
class MusicNote
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create new music note
     */
    public function create(array $data): ?int
    {
        try {
            $sql = "INSERT INTO music_notes (
                        music_entry_id, user_id, note_text, mood, 
                        memory_context, listening_context
                    ) VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['music_entry_id'],
                $data['user_id'],
                $data['note_text'],
                $data['mood'] ?? null,
                $data['memory_context'] ?? null,
                $data['listening_context'] ?? null
            ]);
            
            if ($result) {
                return (int)$this->db->lastInsertId();
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log("Music note creation error: " . $e->getMessage());
            throw new Exception("Failed to create music note");
        }
    }
    
    /**
     * Find note by ID
     */
    public function findById(int $id, ?int $userId = null): ?array
    {
        try {
            $sql = "SELECT * FROM music_notes WHERE id = ?";
            $params = [$id];
            
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            $sql .= " LIMIT 1";
            
            return $this->db->queryOne($sql, $params);
            
        } catch (Exception $e) {
            error_log("Music note lookup error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get note for music entry (only one note per entry)
     */
    public function getByMusicEntry(int $musicEntryId, int $userId): ?array
    {
        try {
            $sql = "SELECT * FROM music_notes 
                    WHERE music_entry_id = ? AND user_id = ? 
                    LIMIT 1";
            
            return $this->db->queryOne($sql, [$musicEntryId, $userId]);
            
        } catch (Exception $e) {
            error_log("Get music note error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update music note
     */
    public function update(int $id, array $data, ?int $userId = null): bool
    {
        try {
            $updateFields = [];
            $params = [];
            
            $allowedFields = ['note_text', 'mood', 'memory_context', 'listening_context'];
            
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
            
            $sql = "UPDATE music_notes SET " . implode(', ', $updateFields) . " WHERE id = ?";
            
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            return $this->db->execute($sql, $params);
            
        } catch (Exception $e) {
            error_log("Music note update error: " . $e->getMessage());
            throw new Exception("Failed to update music note");
        }
    }
    
    /**
     * Create or update note for music entry
     */
    public function createOrUpdate(int $musicEntryId, int $userId, array $data): bool
    {
        $existingNote = $this->getByMusicEntry($musicEntryId, $userId);
        
        if ($existingNote) {
            return $this->update($existingNote['id'], $data, $userId);
        } else {
            $data['music_entry_id'] = $musicEntryId;
            $data['user_id'] = $userId;
            return $this->create($data) !== null;
        }
    }
    
    /**
     * Delete music note
     */
    public function delete(int $id, ?int $userId = null): bool
    {
        try {
            $sql = "DELETE FROM music_notes WHERE id = ?";
            $params = [$id];
            
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            return $this->db->execute($sql, $params);
            
        } catch (Exception $e) {
            error_log("Music note deletion error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete all notes for a music entry
     */
    public function deleteByMusicEntry(int $musicEntryId, ?int $userId = null): bool
    {
        try {
            $sql = "DELETE FROM music_notes WHERE music_entry_id = ?";
            $params = [$musicEntryId];
            
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            return $this->db->execute($sql, $params);
            
        } catch (Exception $e) {
            error_log("Music notes deletion error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user's notes with music entry information
     */
    public function getUserNotes(int $userId, int $limit = 50, int $offset = 0): array
    {
        try {
            $sql = "SELECT mn.*, me.title, me.artist, me.album_art_url
                    FROM music_notes mn
                    INNER JOIN music_entries me ON mn.music_entry_id = me.id
                    WHERE mn.user_id = ?
                    ORDER BY mn.created_at DESC
                    LIMIT ? OFFSET ?";
            
            return $this->db->query($sql, [$userId, $limit, $offset]);
            
        } catch (Exception $e) {
            error_log("Get user notes error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Search notes by text
     */
    public function search(int $userId, string $searchTerm, int $limit = 50): array
    {
        try {
            $sql = "SELECT mn.*, me.title, me.artist
                    FROM music_notes mn
                    INNER JOIN music_entries me ON mn.music_entry_id = me.id
                    WHERE mn.user_id = ? 
                    AND (mn.note_text LIKE ? OR mn.mood LIKE ? OR mn.memory_context LIKE ?)
                    ORDER BY mn.created_at DESC
                    LIMIT ?";
            
            $searchParam = '%' . $searchTerm . '%';
            
            return $this->db->query($sql, [
                $userId, 
                $searchParam, 
                $searchParam, 
                $searchParam, 
                $limit
            ]);
            
        } catch (Exception $e) {
            error_log("Search notes error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get notes by mood
     */
    public function getByMood(int $userId, string $mood, int $limit = 50): array
    {
        try {
            $sql = "SELECT mn.*, me.title, me.artist, me.album_art_url
                    FROM music_notes mn
                    INNER JOIN music_entries me ON mn.music_entry_id = me.id
                    WHERE mn.user_id = ? AND mn.mood = ?
                    ORDER BY mn.created_at DESC
                    LIMIT ?";
            
            return $this->db->query($sql, [$userId, $mood, $limit]);
            
        } catch (Exception $e) {
            error_log("Get notes by mood error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get total notes count for user
     */
    public function getUserNotesCount(int $userId): int
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM music_notes WHERE user_id = ?";
            $result = $this->db->queryOne($sql, [$userId]);
            
            return (int)($result['count'] ?? 0);
            
        } catch (Exception $e) {
            error_log("Get notes count error: " . $e->getMessage());
            return 0;
        }
    }
}



