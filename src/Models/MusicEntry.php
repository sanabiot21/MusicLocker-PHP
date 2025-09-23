<?php

namespace MusicLocker\Models;

use MusicLocker\Services\Database;
use PDO;
use Exception;

/**
 * Music Entry Model
 * Music Locker - Team NaturalStupidity
 * 
 * Handles music catalog entries with Spotify integration
 */
class MusicEntry
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create new music entry
     */
    public function create(array $data): ?int
    {
        try {
            $sql = "INSERT INTO music_entries (
                        user_id, title, artist, album, genre, release_year, duration,
                        spotify_id, spotify_url, album_art_url, preview_url, external_urls,
                        personal_rating, date_discovered, is_favorite
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['user_id'],
                $data['title'],
                $data['artist'],
                $data['album'] ?? null,
                $data['genre'] ?? null,
                $data['release_year'] ?? null,
                $data['duration'] ?? null,
                $data['spotify_id'] ?? null,
                $data['spotify_url'] ?? null,
                $data['album_art_url'] ?? null,
                $data['preview_url'] ?? null,
                $data['external_urls'] ?? null,
                $data['personal_rating'] ?? null,
                $data['date_discovered'] ?? date('Y-m-d'),
                $data['is_favorite'] ?? false
            ]);
            
            if ($result) {
                $entryId = (int)$this->db->lastInsertId();
                
                // Add default tags if specified
                if (isset($data['tags']) && is_array($data['tags'])) {
                    $this->addTagsToEntry($entryId, $data['tags']);
                }
                
                return $entryId;
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log("Music entry creation error: " . $e->getMessage());
            throw new Exception("Failed to create music entry");
        }
    }
    
    /**
     * Find music entry by ID
     */
    public function findById(int $id, int $userId = null): ?array
    {
        try {
            $sql = "SELECT me.*, 
                           GROUP_CONCAT(t.name) as tag_names,
                           GROUP_CONCAT(t.color) as tag_colors,
                           GROUP_CONCAT(t.id) as tag_ids
                    FROM music_entries me
                    LEFT JOIN music_entry_tags met ON me.id = met.music_entry_id
                    LEFT JOIN tags t ON met.tag_id = t.id
                    WHERE me.id = ?";
            
            $params = [$id];
            
            if ($userId !== null) {
                $sql .= " AND me.user_id = ?";
                $params[] = $userId;
            }
            
            $sql .= " GROUP BY me.id LIMIT 1";
            
            $result = $this->db->queryOne($sql, $params);
            
            if ($result) {
                $result = $this->formatMusicEntry($result);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Music entry lookup error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Find music entry by Spotify ID for a user
     */
    public function findBySpotifyId(string $spotifyId, int $userId): ?array
    {
        try {
            $sql = "SELECT * FROM music_entries WHERE spotify_id = ? AND user_id = ? LIMIT 1";
            $result = $this->db->queryOne($sql, [$spotifyId, $userId]);
            
            if ($result) {
                $result = $this->formatMusicEntry($result);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Music entry Spotify lookup error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get user's music collection with filtering and pagination
     */
    public function getUserCollection(int $userId, array $options = []): array
    {
        try {
            // Build base query
            $sql = "SELECT me.*, 
                           GROUP_CONCAT(t.name) as tag_names,
                           GROUP_CONCAT(t.color) as tag_colors,
                           GROUP_CONCAT(t.id) as tag_ids
                    FROM music_entries me
                    LEFT JOIN music_entry_tags met ON me.id = met.music_entry_id
                    LEFT JOIN tags t ON met.tag_id = t.id
                    WHERE me.user_id = ?";
            
            $params = [$userId];
            $conditions = [];
            
            // Add search filter
            if (!empty($options['search'])) {
                $conditions[] = "(me.title LIKE ? OR me.artist LIKE ? OR me.album LIKE ?)";
                $searchTerm = '%' . $options['search'] . '%';
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
            }
            
            // Add genre filter
            if (!empty($options['genre'])) {
                $conditions[] = "me.genre = ?";
                $params[] = $options['genre'];
            }
            
            // Add rating filter
            if (!empty($options['rating'])) {
                $conditions[] = "me.personal_rating = ?";
                $params[] = $options['rating'];
            }
            
            // Add favorites filter
            if (isset($options['favorites']) && $options['favorites']) {
                $conditions[] = "me.is_favorite = TRUE";
            }
            
            // Add tag filter
            if (!empty($options['tag_ids'])) {
                $tagIds = is_array($options['tag_ids']) ? $options['tag_ids'] : [$options['tag_ids']];
                $placeholders = str_repeat('?,', count($tagIds) - 1) . '?';
                $conditions[] = "me.id IN (SELECT DISTINCT met.music_entry_id FROM music_entry_tags met WHERE met.tag_id IN ($placeholders))";
                $params = array_merge($params, $tagIds);
            }
            
            // Add year range filter
            if (!empty($options['year_from'])) {
                $conditions[] = "me.release_year >= ?";
                $params[] = $options['year_from'];
            }
            
            if (!empty($options['year_to'])) {
                $conditions[] = "me.release_year <= ?";
                $params[] = $options['year_to'];
            }
            
            // Add conditions to query
            if (!empty($conditions)) {
                $sql .= " AND " . implode(" AND ", $conditions);
            }
            
            $sql .= " GROUP BY me.id";
            
            // Add sorting
            $sortBy = $options['sort_by'] ?? 'date_added';
            $sortOrder = $options['sort_order'] ?? 'DESC';
            
            $allowedSortFields = [
                'date_added', 'title', 'artist', 'album', 'personal_rating', 
                'release_year', 'last_played', 'times_played'
            ];
            
            if (in_array($sortBy, $allowedSortFields)) {
                $sql .= " ORDER BY me.$sortBy $sortOrder";
            } else {
                $sql .= " ORDER BY me.date_added DESC";
            }
            
            // Add pagination
            $limit = $options['limit'] ?? 20;
            $offset = $options['offset'] ?? 0;
            
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $results = $this->db->query($sql, $params);
            
            // Format results
            $entries = array_map([$this, 'formatMusicEntry'], $results);
            
            // Get total count for pagination
            $countSql = str_replace(
                "SELECT me.*, GROUP_CONCAT(t.name) as tag_names, GROUP_CONCAT(t.color) as tag_colors, GROUP_CONCAT(t.id) as tag_ids FROM music_entries me LEFT JOIN music_entry_tags met ON me.id = met.music_entry_id LEFT JOIN tags t ON met.tag_id = t.id",
                "SELECT COUNT(DISTINCT me.id) as total FROM music_entries me LEFT JOIN music_entry_tags met ON me.id = met.music_entry_id LEFT JOIN tags t ON met.tag_id = t.id",
                $sql
            );
            
            // Remove GROUP BY, ORDER BY, LIMIT clauses for count query
            $countSql = preg_replace('/ GROUP BY.*$/', '', $countSql);
            $countParams = array_slice($params, 0, -2); // Remove LIMIT and OFFSET params
            
            $totalResult = $this->db->queryOne($countSql, $countParams);
            $total = $totalResult['total'] ?? 0;
            
            return [
                'entries' => $entries,
                'pagination' => [
                    'total' => $total,
                    'limit' => $limit,
                    'offset' => $offset,
                    'has_more' => ($offset + $limit) < $total
                ]
            ];
            
        } catch (Exception $e) {
            error_log("User collection query error: " . $e->getMessage());
            return ['entries' => [], 'pagination' => ['total' => 0, 'limit' => 0, 'offset' => 0, 'has_more' => false]];
        }
    }
    
    /**
     * Update music entry
     */
    public function update(int $id, array $data, int $userId = null): bool
    {
        try {
            $updateFields = [];
            $params = [];
            
            $allowedFields = [
                'title', 'artist', 'album', 'genre', 'release_year', 'duration',
                'personal_rating', 'date_discovered', 'is_favorite', 'times_played',
                'last_played', 'spotify_url', 'album_art_url', 'preview_url', 'external_urls'
            ];
            
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
            
            $sql = "UPDATE music_entries SET " . implode(', ', $updateFields) . " WHERE id = ?";
            
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            $result = $this->db->execute($sql, $params);
            
            // Update tags if specified
            if (isset($data['tags']) && is_array($data['tags'])) {
                $this->updateEntryTags($id, $data['tags']);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Music entry update error: " . $e->getMessage());
            throw new Exception("Failed to update music entry");
        }
    }
    
    /**
     * Delete music entry
     */
    public function delete(int $id, int $userId = null): bool
    {
        try {
            $sql = "DELETE FROM music_entries WHERE id = ?";
            $params = [$id];
            
            if ($userId !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            return $this->db->execute($sql, $params);
            
        } catch (Exception $e) {
            error_log("Music entry deletion error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Add tags to music entry
     */
    public function addTagsToEntry(int $entryId, array $tagIds): void
    {
        try {
            // Remove existing tags first
            $this->db->execute("DELETE FROM music_entry_tags WHERE music_entry_id = ?", [$entryId]);
            
            // Add new tags
            $sql = "INSERT INTO music_entry_tags (music_entry_id, tag_id) VALUES (?, ?)";
            
            foreach ($tagIds as $tagId) {
                if (is_numeric($tagId)) {
                    $this->db->execute($sql, [$entryId, $tagId]);
                }
            }
            
        } catch (Exception $e) {
            error_log("Add tags to entry error: " . $e->getMessage());
        }
    }
    
    /**
     * Update entry tags
     */
    public function updateEntryTags(int $entryId, array $tagIds): void
    {
        $this->addTagsToEntry($entryId, $tagIds);
    }
    
    /**
     * Get entry's tags
     */
    public function getEntryTags(int $entryId): array
    {
        try {
            $sql = "SELECT t.* FROM tags t 
                    INNER JOIN music_entry_tags met ON t.id = met.tag_id 
                    WHERE met.music_entry_id = ? 
                    ORDER BY t.name";
            
            return $this->db->query($sql, [$entryId]);
            
        } catch (Exception $e) {
            error_log("Get entry tags error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update play count and last played timestamp
     */
    public function recordPlay(int $id, int $userId): bool
    {
        try {
            $sql = "UPDATE music_entries 
                    SET times_played = times_played + 1, 
                        last_played = NOW(),
                        updated_at = NOW()
                    WHERE id = ? AND user_id = ?";
            
            return $this->db->execute($sql, [$id, $userId]);
            
        } catch (Exception $e) {
            error_log("Record play error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Toggle favorite status
     */
    public function toggleFavorite(int $id, int $userId): bool
    {
        try {
            $sql = "UPDATE music_entries 
                    SET is_favorite = NOT is_favorite,
                        updated_at = NOW()
                    WHERE id = ? AND user_id = ?";
            
            return $this->db->execute($sql, [$id, $userId]);
            
        } catch (Exception $e) {
            error_log("Toggle favorite error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user's collection statistics
     */
    public function getCollectionStats(int $userId): array
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_entries,
                        COUNT(CASE WHEN personal_rating = 5 THEN 1 END) as five_star_count,
                        COUNT(CASE WHEN is_favorite = TRUE THEN 1 END) as favorites_count,
                        COUNT(CASE WHEN date_added >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as recent_additions,
                        COUNT(CASE WHEN last_played >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as recently_played,
                        AVG(CASE WHEN personal_rating IS NOT NULL THEN personal_rating END) as average_rating,
                        COUNT(DISTINCT artist) as unique_artists,
                        COUNT(DISTINCT album) as unique_albums,
                        COUNT(DISTINCT genre) as unique_genres,
                        SUM(times_played) as total_plays,
                        MAX(date_added) as latest_addition,
                        MIN(release_year) as earliest_year,
                        MAX(release_year) as latest_year
                    FROM music_entries 
                    WHERE user_id = ?";
            
            $result = $this->db->queryOne($sql, [$userId]);
            
            return [
                'total_entries' => (int)($result['total_entries'] ?? 0),
                'five_star_count' => (int)($result['five_star_count'] ?? 0),
                'favorites_count' => (int)($result['favorites_count'] ?? 0),
                'recent_additions' => (int)($result['recent_additions'] ?? 0),
                'recently_played' => (int)($result['recently_played'] ?? 0),
                'average_rating' => round((float)($result['average_rating'] ?? 0), 1),
                'unique_artists' => (int)($result['unique_artists'] ?? 0),
                'unique_albums' => (int)($result['unique_albums'] ?? 0),
                'unique_genres' => (int)($result['unique_genres'] ?? 0),
                'total_plays' => (int)($result['total_plays'] ?? 0),
                'latest_addition' => $result['latest_addition'],
                'earliest_year' => $result['earliest_year'],
                'latest_year' => $result['latest_year']
            ];
            
        } catch (Exception $e) {
            error_log("Collection stats error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get popular genres for user
     */
    public function getPopularGenres(int $userId, int $limit = 10): array
    {
        try {
            $sql = "SELECT genre, COUNT(*) as count 
                    FROM music_entries 
                    WHERE user_id = ? AND genre IS NOT NULL 
                    GROUP BY genre 
                    ORDER BY count DESC, genre ASC 
                    LIMIT ?";
            
            return $this->db->query($sql, [$userId, $limit]);
            
        } catch (Exception $e) {
            error_log("Popular genres error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get top artists for user
     */
    public function getTopArtists(int $userId, int $limit = 10): array
    {
        try {
            $sql = "SELECT artist, COUNT(*) as entry_count, SUM(times_played) as total_plays
                    FROM music_entries 
                    WHERE user_id = ? 
                    GROUP BY artist 
                    ORDER BY entry_count DESC, total_plays DESC, artist ASC 
                    LIMIT ?";
            
            return $this->db->query($sql, [$userId, $limit]);
            
        } catch (Exception $e) {
            error_log("Top artists error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Validate music entry data
     */
    public function validate(array $data, array $rules = []): array
    {
        $errors = [];
        
        // Required fields
        if (isset($rules['title']) && $rules['title']) {
            if (empty($data['title'])) {
                $errors['title'] = 'Title is required';
            } elseif (strlen($data['title']) > 255) {
                $errors['title'] = 'Title is too long (max 255 characters)';
            }
        }
        
        if (isset($rules['artist']) && $rules['artist']) {
            if (empty($data['artist'])) {
                $errors['artist'] = 'Artist is required';
            } elseif (strlen($data['artist']) > 255) {
                $errors['artist'] = 'Artist name is too long (max 255 characters)';
            }
        }
        
        // Optional field validations
        if (!empty($data['personal_rating'])) {
            $rating = (int)$data['personal_rating'];
            if ($rating < 1 || $rating > 5) {
                $errors['personal_rating'] = 'Rating must be between 1 and 5 stars';
            }
        }
        
        if (!empty($data['release_year'])) {
            $year = (int)$data['release_year'];
            if ($year < 1900 || $year > (date('Y') + 1)) {
                $errors['release_year'] = 'Please enter a valid release year';
            }
        }
        
        if (!empty($data['duration']) && (int)$data['duration'] < 0) {
            $errors['duration'] = 'Duration must be a positive number';
        }
        
        return $errors;
    }
    
    /**
     * Format music entry for display
     */
    private function formatMusicEntry(array $entry): array
    {
        // Process tags
        if (!empty($entry['tag_names'])) {
            $tagNames = explode(',', $entry['tag_names']);
            $tagColors = explode(',', $entry['tag_colors']);
            $tagIds = explode(',', $entry['tag_ids']);
            
            $entry['tags'] = [];
            for ($i = 0; $i < count($tagNames); $i++) {
                if (!empty($tagNames[$i])) {
                    $entry['tags'][] = [
                        'id' => $tagIds[$i],
                        'name' => $tagNames[$i],
                        'color' => $tagColors[$i] ?? '#6c757d'
                    ];
                }
            }
        } else {
            $entry['tags'] = [];
        }
        
        // Remove the concatenated tag fields
        unset($entry['tag_names'], $entry['tag_colors'], $entry['tag_ids']);
        
        // Format duration
        if ($entry['duration']) {
            $entry['duration_formatted'] = format_duration((int)$entry['duration']);
        }
        
        // Format dates
        if ($entry['date_added']) {
            $entry['date_added_formatted'] = format_date($entry['date_added']);
        }
        
        if ($entry['last_played']) {
            $entry['last_played_formatted'] = format_datetime($entry['last_played']);
            $entry['last_played_ago'] = time_ago($entry['last_played']);
        }
        
        // Format external URLs
        if (!empty($entry['external_urls'])) {
            $entry['external_urls'] = json_decode($entry['external_urls'], true) ?? [];
        }
        
        // Boolean conversions
        $entry['is_favorite'] = (bool)$entry['is_favorite'];
        
        return $entry;
    }
}