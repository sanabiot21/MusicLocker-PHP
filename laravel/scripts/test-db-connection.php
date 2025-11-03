<?php

/**
 * Database Connection Test Script
 * 
 * This script tests the database connection independently of Laravel bootstrap.
 * Useful for diagnosing connection issues during deployment.
 * 
 * Usage: php scripts/test-db-connection.php
 */

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Get database configuration from environment
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '5432';
$database = getenv('DB_DATABASE') ?: 'laravel';
$username = getenv('DB_USERNAME') ?: 'postgres';
$password = getenv('DB_PASSWORD') ?: '';
$sslmode = getenv('DB_SSLMODE') ?: 'require';

echo "=== Database Connection Test ===\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "Database: $database\n";
echo "Username: $username\n";
echo "SSL Mode: $sslmode\n";
echo "\n";

// Build connection string
$dsn = "pgsql:host=$host;port=$port;dbname=$database";
if ($sslmode !== 'disable') {
    $dsn .= ";sslmode=$sslmode";
}

echo "Attempting connection...\n";

try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 10,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    echo "✓ Connection successful!\n\n";
    
    // Test a simple query
    echo "Testing query execution...\n";
    $stmt = $pdo->query("SELECT version()");
    $version = $stmt->fetchColumn();
    echo "✓ Query executed successfully\n";
    echo "PostgreSQL Version: $version\n\n";
    
    // Check if migrations table exists
    echo "Checking migrations table...\n";
    $stmt = $pdo->query("SELECT EXISTS (
        SELECT 1 FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_name = 'migrations'
    )");
    $migrationsExists = $stmt->fetchColumn();
    echo $migrationsExists ? "✓ Migrations table exists\n" : "⚠ Migrations table does not exist (this is normal for fresh deployments)\n";
    
    echo "\n=== Connection test completed successfully ===\n";
    exit(0);
    
} catch (PDOException $e) {
    echo "✗ Connection failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n\n";
    
    echo "Troubleshooting steps:\n";
    echo "1. Verify DB_HOST points to transaction pooler: *.pooler.supabase.com\n";
    echo "2. Verify DB_PORT is set to 6543 for transaction pooler\n";
    echo "3. Verify DB_SSLMODE is set to 'require'\n";
    echo "4. Check Supabase project is active in dashboard\n";
    echo "5. Verify credentials are correct\n";
    echo "6. Check Supabase network settings (IP whitelisting) allow connections from Render\n";
    
    exit(1);
}


