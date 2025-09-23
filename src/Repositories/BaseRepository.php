<?php

namespace MusicLocker\Repositories;

use MusicLocker\Services\Database;
use PDO;
use Exception;

/**
 * Base Repository
 * 
 * Provides common database operations and utilities
 * Following Repository Pattern and DRY principles
 */
abstract class BaseRepository
{
    protected Database $db;
    protected PDO $pdo;
    
    public function __construct(Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
        $this->pdo = $this->db->getConnection();
    }
    
    /**
     * Execute prepared statement and return results
     */
    protected function query(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Database query error: " . $e->getMessage());
            throw new Exception("Database query failed");
        }
    }
    
    /**
     * Execute prepared statement and return first result
     */
    protected function queryOne(string $sql, array $params = []): ?array
    {
        $results = $this->query($sql, $params);
        return $results ? $results[0] : null;
    }
    
    /**
     * Execute prepared statement and return affected rows count
     */
    protected function execute(string $sql, array $params = []): int
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (Exception $e) {
            error_log("Database execute error: " . $e->getMessage());
            throw new Exception("Database operation failed");
        }
    }
    
    /**
     * Execute insert and return last insert ID
     */
    protected function insert(string $sql, array $params = []): int
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return (int)$this->pdo->lastInsertId();
        } catch (Exception $e) {
            error_log("Database insert error: " . $e->getMessage());
            throw new Exception("Database insert failed");
        }
    }
    
    /**
     * Build WHERE conditions from filters
     */
    protected function buildWhereConditions(array $filters): array
    {
        $conditions = [];
        $params = [];
        
        foreach ($filters as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            
            switch ($field) {
                case 'search':
                    $searchTerm = '%' . $value . '%';
                    $conditions[] = "(title LIKE ? OR artist LIKE ? OR album LIKE ?)";
                    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
                    break;
                    
                case 'genre':
                    $conditions[] = "genre = ?";
                    $params[] = $value;
                    break;
                    
                case 'rating':
                    $conditions[] = "personal_rating >= ?";
                    $params[] = (int)$value;
                    break;
                    
                case 'favorites':
                    if ($value) {
                        $conditions[] = "is_favorite = 1";
                    }
                    break;
                    
                case 'year_from':
                    $conditions[] = "release_year >= ?";
                    $params[] = (int)$value;
                    break;
                    
                case 'year_to':
                    $conditions[] = "release_year <= ?";
                    $params[] = (int)$value;
                    break;
            }
        }
        
        return [
            'conditions' => $conditions,
            'params' => $params
        ];
    }
    
    /**
     * Build ORDER BY clause
     */
    protected function buildOrderBy(string $sortBy, string $sortOrder): string
    {
        $allowedSortFields = [
            'date_added' => 'created_at',
            'title' => 'title',
            'artist' => 'artist',
            'album' => 'album',
            'rating' => 'personal_rating',
            'play_count' => 'times_played'
        ];
        
        $field = $allowedSortFields[$sortBy] ?? 'created_at';
        $order = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        
        return "{$field} {$order}";
    }
    
    /**
     * Sanitize string input
     */
    protected function sanitizeString(?string $value): ?string
    {
        return $value ? trim(strip_tags($value)) : null;
    }
    
    /**
     * Validate and sanitize integer
     */
    protected function sanitizeInt($value): ?int
    {
        return is_numeric($value) ? (int)$value : null;
    }
    
    /**
     * Validate boolean
     */
    protected function sanitizeBool($value): bool
    {
        return (bool)$value;
    }
}