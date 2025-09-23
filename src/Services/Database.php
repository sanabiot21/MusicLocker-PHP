<?php

namespace MusicLocker\Services;

use PDO;
use PDOException;
use Exception;

/**
 * Database Service
 * Handles database connections and basic operations
 */
class Database
{
    private static ?self $instance = null;
    private ?PDO $connection = null;
    
    private string $host;
    private string $database;
    private string $username;
    private string $password;
    private array $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];
    
    private function __construct()
    {
        $this->host = env('DB_HOST', 'localhost');
        $this->database = env('DB_NAME', 'music_locker');
        $this->username = env('DB_USER', 'root');
        $this->password = env('DB_PASS', '');
        
        $this->connect();
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Get PDO connection
     */
    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $this->connect();
        }
        
        return $this->connection;
    }
    
    /**
     * Connect to database
     */
    private function connect(): void
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4";
            $this->connection = new PDO($dsn, $this->username, $this->password, $this->options);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Execute a query and return results
     */
    public function query(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            throw new Exception("Query execution failed");
        }
    }
    
    /**
     * Execute a query and return single row
     */
    public function queryOne(string $sql, array $params = []): ?array
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            throw new Exception("Query execution failed");
        }
    }
    
    /**
     * Execute an insert/update/delete query
     */
    public function execute(string $sql, array $params = []): bool
    {
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Database execute error: " . $e->getMessage());
            throw new Exception("Statement execution failed");
        }
    }
    
    /**
     * Get last insert ID
     */
    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit(): bool
    {
        return $this->connection->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback(): bool
    {
        return $this->connection->rollBack();
    }
    
    /**
     * Check if we're in a transaction
     */
    public function inTransaction(): bool
    {
        return $this->connection->inTransaction();
    }
    
    /**
     * Prepare a statement
     */
    public function prepare(string $sql): \PDOStatement
    {
        return $this->connection->prepare($sql);
    }
    
    /**
     * Test database connection
     */
    public function testConnection(): bool
    {
        try {
            $this->connection->query("SELECT 1");
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get database info
     */
    public function getInfo(): array
    {
        try {
            $version = $this->connection->query("SELECT VERSION() as version")->fetch();
            $charset = $this->connection->query("SHOW VARIABLES LIKE 'character_set_database'")->fetch();
            $collation = $this->connection->query("SHOW VARIABLES LIKE 'collation_database'")->fetch();
            
            return [
                'host' => $this->host,
                'database' => $this->database,
                'username' => $this->username,
                'version' => $version['version'] ?? 'unknown',
                'charset' => $charset['Value'] ?? 'unknown',
                'collation' => $collation['Value'] ?? 'unknown'
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Prevent cloning
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}