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
    private string $port;
    private string $driver;
    private ?string $sslmode;
    private array $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    private function __construct()
    {
        $this->driver = env('DB_CONNECTION', 'mysql');
        $this->host = env('DB_HOST', 'localhost');
        $this->port = env('DB_PORT', $this->driver === 'pgsql' ? '5432' : '3306');
        $this->database = env('DB_DATABASE', env('DB_NAME', 'music_locker'));
        $this->username = env('DB_USERNAME', env('DB_USER', 'root'));
        $this->password = env('DB_PASSWORD', env('DB_PASS', ''));
        $this->sslmode = env('DB_SSLMODE');

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
            if ($this->driver === 'pgsql') {
                $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->database}";
                if ($this->sslmode) {
                    $dsn .= ";sslmode={$this->sslmode}";
                }
            } else {
                $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->database};charset=utf8mb4";
                $this->options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci";
            }

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

            $info = [
                'host' => $this->host,
                'database' => $this->database,
                'username' => $this->username,
                'driver' => $this->driver,
                'version' => $version['version'] ?? 'unknown'
            ];

            // Get charset/encoding info based on driver
            if ($this->driver === 'pgsql') {
                $encoding = $this->connection->query("SHOW server_encoding")->fetch();
                $info['encoding'] = $encoding['server_encoding'] ?? 'unknown';
            } else {
                $charset = $this->connection->query("SHOW VARIABLES LIKE 'character_set_database'")->fetch();
                $collation = $this->connection->query("SHOW VARIABLES LIKE 'collation_database'")->fetch();
                $info['charset'] = $charset['Value'] ?? 'unknown';
                $info['collation'] = $collation['Value'] ?? 'unknown';
            }

            return $info;
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Convert value to proper boolean for database
     */
    public function toBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_string($value)) {
            return in_array(strtolower($value), ['1', 'true', 'on', 'yes'], true);
        }
        return (bool)$value;
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

    /**
     * Get database driver
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * Get list of tables in the database
     */
    public function getTableList(): array
    {
        try {
            if ($this->driver === 'pgsql') {
                // PostgreSQL: Query information_schema
                $sql = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name";
            } else {
                // MySQL: Use SHOW TABLES
                $sql = "SHOW TABLES";
            }
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            
            if ($this->driver === 'pgsql') {
                // PostgreSQL returns array with 'table_name' key
                return array_column($result, 'table_name');
            } else {
                // MySQL returns array with numeric keys, need to extract table names
                $tables = [];
                foreach ($result as $row) {
                    $tables[] = current($row); // Get first value from each row
                }
                return $tables;
            }
        } catch (Exception $e) {
            error_log("Failed to get table list: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get count of tables in the database
     */
    public function getTableCount(): int
    {
        return count($this->getTableList());
    }

    /**
     * Check if required tables exist
     */
    public function checkRequiredTables(array $requiredTables = []): array
    {
        $tables = $this->getTableList();

        if (empty($requiredTables)) {
            $requiredTables = ['users', 'music_entries', 'tags', 'music_notes'];
        }

        $results = [];
        foreach ($requiredTables as $table) {
            $results[$table] = in_array($table, $tables);
        }

        return $results;
    }

    /**
     * Check if current driver is PostgreSQL
     */
    public function isPostgreSQL(): bool
    {
        return $this->driver === 'pgsql';
    }

    /**
     * Get LIKE operator for case-insensitive search
     */
    public function getLikeOperator(): string
    {
        return $this->isPostgreSQL() ? 'ILIKE' : 'LIKE';
    }

    /**
     * Get current timestamp in application timezone
     */
    public function getTimestampNow(): string
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Format database timestamp to readable format in application timezone
     */
    public function formatTimestamp(?string $timestamp, string $format = 'Y-m-d H:i:s'): string
    {
        if ($timestamp === null || $timestamp === '') {
            return 'Never';
        }

        try {
            $dateTime = new \DateTime($timestamp);
            return $dateTime->format($format);
        } catch (Exception $e) {
            error_log("Failed to format timestamp '$timestamp': " . $e->getMessage());
            return 'Invalid date';
        }
    }

    /**
     * Get timezone offset in seconds for Manila timezone
     */
    public function getTimezoneOffset(): int
    {
        try {
            $timezone = new \DateTimeZone(env('APP_TIMEZONE', 'Asia/Manila'));
            $now = new \DateTime('now', $timezone);
            return $timezone->getOffset($now);
        } catch (Exception $e) {
            // Default to UTC+8 Manila timezone
            return 28800; // 8 hours * 3600 seconds
        }
    }

    /**
     * Convert UTC timestamp to application timezone
     */
    public function convertToAppTimezone(string $utcTimestamp): string
    {
        try {
            $utc = new \DateTime($utcTimestamp, new \DateTimeZone('UTC'));
            $appTimezone = new \DateTimeZone(env('APP_TIMEZONE', 'Asia/Manila'));
            $utc->setTimezone($appTimezone);
            return $utc->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            error_log("Failed to convert UTC timestamp '$utcTimestamp': " . $e->getMessage());
            return $utcTimestamp;
        }
    }

    /**
     * Convert application timezone timestamp to UTC
     */
    public function convertToUTC(string $appTimestamp): string
    {
        try {
            $appTimezone = new \DateTimeZone(env('APP_TIMEZONE', 'Asia/Manila'));
            $app = new \DateTime($appTimestamp, $appTimezone);
            $app->setTimezone(new \DateTimeZone('UTC'));
            return $app->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            error_log("Failed to convert app timestamp '$appTimestamp' to UTC: " . $e->getMessage());
            return $appTimestamp;
        }
    }
}