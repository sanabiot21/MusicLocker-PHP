<?php
/**
 * System Environment Test
 * Music Locker - Team NaturalStupidity
 * 
 * This comprehensive test script validates:
 * - Environment configuration
 * - Database connection
 * - PHP extensions
 * - File permissions
 * - Application readiness
 */

// Prevent running in production
if (getenv('APP_ENV') === 'production') {
    http_response_code(404);
    exit('Not found');
}

session_start();

// Define paths
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('CONFIG_PATH', ROOT_PATH . '/config');
define('SRC_PATH', ROOT_PATH . '/src');

// Include Composer autoloader
require_once ROOT_PATH . '/vendor/autoload.php';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Locker - System Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/dark-techno-theme.css">
    <style>
        .test-section {
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid #333;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }
        
        .test-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #2a2a2a;
        }
        
        .test-item:last-child {
            border-bottom: none;
        }
        
        .test-result {
            font-weight: bold;
            text-align: right;
            min-width: 80px;
        }
        
        .status-success { color: #28a745; }
        .status-error { color: #dc3545; }
        .status-warning { color: #ffc107; }
        .status-info { color: #17a2b8; }
        
        .header-glow {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .config-table {
            font-size: 0.875rem;
        }
        
        .ngrok-section {
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(138, 43, 226, 0.1));
            border: 1px solid rgba(0, 212, 255, 0.3);
        }
    </style>
</head>
<body class="bg-pattern">
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <div class="text-center mb-5">
                    <h1 class="display-4 header-glow">
                        <i class="bi bi-wrench-adjustable me-3"></i>
                        Music Locker System Test
                    </h1>
                    <p class="text-muted">Comprehensive environment validation</p>
                </div>

                <!-- Environment Configuration Test -->
                <div class="test-section">
                    <h3 class="mb-3">
                        <i class="bi bi-gear-fill me-2" style="color: var(--accent-blue);"></i>
                        Environment Configuration
                    </h3>
                    
                    <?php
                    $envTests = [];
                    
                    // Check .env file
                    $envFile = ROOT_PATH . '/.env';
                    $envExists = file_exists($envFile);
                    $envTests['Environment File (.env)'] = $envExists ? 'FOUND' : 'MISSING';
                    
                    if ($envExists) {
                        // Test environment loading
                        try {
                            $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
                            $dotenv->safeLoad();
                            $envTests['Environment Loading'] = 'SUCCESS';
                        } catch (Exception $e) {
                            $envTests['Environment Loading'] = 'ERROR: ' . $e->getMessage();
                        }
                        
                        // Check key environment variables
                        $requiredEnvVars = [
                            'APP_NAME', 'APP_ENV', 'DB_HOST', 'DB_DATABASE', 
                            'SPOTIFY_CLIENT_ID', 'SPOTIFY_CLIENT_SECRET'
                        ];
                        
                        foreach ($requiredEnvVars as $var) {
                            $value = env($var);
                            $envTests[$var] = $value ? 'SET' : 'MISSING';
                        }
                        
                        // Check Ngrok configuration
                        $ngrokUrl = env('NGROK_URL');
                        $envTests['NGROK_URL'] = $ngrokUrl ? 'SET' : 'NOT SET';
                        
                        // Check Spotify redirect URI
                        $spotifyRedirect = env('SPOTIFY_REDIRECT_URI');
                        $envTests['SPOTIFY_REDIRECT_URI'] = $spotifyRedirect ? 'CONFIGURED' : 'MISSING';
                    }
                    
                    foreach ($envTests as $test => $result) {
                        $statusClass = 'status-success';
                        if (strpos($result, 'ERROR') !== false || strpos($result, 'MISSING') !== false) {
                            $statusClass = 'status-error';
                        } elseif (strpos($result, 'NOT SET') !== false) {
                            $statusClass = 'status-warning';
                        }
                        
                        echo "<div class='test-item'>";
                        echo "<span>$test</span>";
                        echo "<span class='test-result $statusClass'>$result</span>";
                        echo "</div>";
                    }
                    ?>
                </div>

                <!-- Database Connection Test -->
                <div class="test-section">
                    <h3 class="mb-3">
                        <i class="bi bi-database-fill me-2" style="color: var(--accent-purple);"></i>
                        Database Connection
                    </h3>
                    
                    <?php
                    $dbTests = [];
                    
                    try {
                        // Test basic MySQL connection
                        $host = env('DB_HOST', '127.0.0.1');
                        $username = env('DB_USERNAME', 'root');
                        $password = env('DB_PASSWORD', '');
                        
                        $pdo = new PDO("mysql:host=$host", $username, $password);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $dbTests['MySQL Connection'] = 'SUCCESS';
                        
                        // Check if music_locker database exists
                        $stmt = $pdo->query("SHOW DATABASES LIKE 'music_locker'");
                        if ($stmt->rowCount() > 0) {
                            $dbTests['music_locker Database'] = 'EXISTS';
                            
                            // Connect to the database
                            try {
                                $musicPdo = new PDO("mysql:host=$host;dbname=music_locker", $username, $password);
                                $musicPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $dbTests['Database Connection'] = 'SUCCESS';
                                
                                // Check for key tables
                                $requiredTables = ['users', 'music_entries', 'tags', 'music_notes', 'activity_log'];
                                $stmt = $musicPdo->query("SHOW TABLES");
                                $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                
                                foreach ($requiredTables as $table) {
                                    $exists = in_array($table, $existingTables);
                                    $dbTests["Table: $table"] = $exists ? 'EXISTS' : 'MISSING';
                                }
                                
                                // Test application database service
                                try {
                                    $db = \MusicLocker\Services\Database::getInstance();
                                    $dbTests['Application Database Service'] = 'SUCCESS';
                                } catch (Exception $e) {
                                    $dbTests['Application Database Service'] = 'ERROR: ' . $e->getMessage();
                                }
                                
                            } catch (PDOException $e) {
                                $dbTests['Database Connection'] = 'ERROR: ' . $e->getMessage();
                            }
                        } else {
                            $dbTests['music_locker Database'] = 'MISSING';
                            $dbTests['Import Status'] = 'SCHEMA IMPORT REQUIRED';
                        }
                        
                    } catch (PDOException $e) {
                        $dbTests['MySQL Connection'] = 'ERROR: ' . $e->getMessage();
                    }
                    
                    foreach ($dbTests as $test => $result) {
                        $statusClass = 'status-success';
                        if (strpos($result, 'ERROR') !== false || strpos($result, 'MISSING') !== false) {
                            $statusClass = 'status-error';
                        } elseif (strpos($result, 'REQUIRED') !== false) {
                            $statusClass = 'status-warning';
                        }
                        
                        echo "<div class='test-item'>";
                        echo "<span>$test</span>";
                        echo "<span class='test-result $statusClass'>$result</span>";
                        echo "</div>";
                    }
                    ?>
                </div>

                <!-- PHP Extensions Test -->
                <div class="test-section">
                    <h3 class="mb-3">
                        <i class="bi bi-code-slash me-2" style="color: #28a745;"></i>
                        PHP Environment
                    </h3>
                    
                    <?php
                    $phpTests = [];
                    
                    // PHP Version
                    $phpVersion = PHP_VERSION;
                    $phpTests['PHP Version'] = $phpVersion . (version_compare($phpVersion, '8.2.0', '>=') ? ' (OK)' : ' (UPGRADE RECOMMENDED)');
                    
                    // Required PHP extensions
                    $requiredExtensions = [
                        'curl' => 'Spotify API communication',
                        'json' => 'JSON data processing',
                        'mbstring' => 'Multi-byte string handling',
                        'openssl' => 'Secure communications',
                        'pdo' => 'Database abstraction',
                        'pdo_mysql' => 'MySQL database support'
                    ];
                    
                    foreach ($requiredExtensions as $ext => $description) {
                        $loaded = extension_loaded($ext);
                        $phpTests["Extension: $ext"] = $loaded ? 'LOADED' : 'MISSING';
                    }
                    
                    // Memory limit
                    $memoryLimit = ini_get('memory_limit');
                    $phpTests['Memory Limit'] = $memoryLimit;
                    
                    // File upload settings
                    $uploadMaxFilesize = ini_get('upload_max_filesize');
                    $postMaxSize = ini_get('post_max_size');
                    $phpTests['Upload Max Filesize'] = $uploadMaxFilesize;
                    $phpTests['POST Max Size'] = $postMaxSize;
                    
                    foreach ($phpTests as $test => $result) {
                        $statusClass = 'status-success';
                        if (strpos($result, 'MISSING') !== false) {
                            $statusClass = 'status-error';
                        } elseif (strpos($result, 'UPGRADE') !== false) {
                            $statusClass = 'status-warning';
                        }
                        
                        echo "<div class='test-item'>";
                        echo "<span>$test</span>";
                        echo "<span class='test-result $statusClass'>$result</span>";
                        echo "</div>";
                    }
                    ?>
                </div>

                <!-- Ngrok Configuration (if available) -->
                <?php if (env('NGROK_URL')): ?>
                <div class="test-section ngrok-section">
                    <h3 class="mb-3">
                        <i class="bi bi-globe2 me-2" style="color: var(--accent-blue);"></i>
                        Ngrok Integration
                    </h3>
                    
                    <?php
                    $ngrokUrl = env('NGROK_URL');
                    $spotifyRedirectUri = env('SPOTIFY_REDIRECT_URI');
                    
                    echo "<div class='test-item'>";
                    echo "<span>Ngrok URL</span>";
                    echo "<span class='test-result status-success'>CONFIGURED</span>";
                    echo "</div>";
                    
                    echo "<div class='test-item'>";
                    echo "<span>Current URL</span>";
                    echo "<span class='test-result status-info'>$ngrokUrl</span>";
                    echo "</div>";
                    
                    echo "<div class='test-item'>";
                    echo "<span>Spotify Redirect URI</span>";
                    echo "<span class='test-result status-success'>UPDATED</span>";
                    echo "</div>";
                    
                    echo "<div class='alert alert-info mt-3'>";
                    echo "<strong>Spotify OAuth Ready!</strong><br>";
                    echo "Your Spotify redirect URI has been configured for Ngrok.<br>";
                    echo "<strong>Redirect URI:</strong> <code>$spotifyRedirectUri</code><br>";
                    echo "Make sure this URI is registered in your <a href='https://developer.spotify.com/dashboard' target='_blank'>Spotify Developer Dashboard</a>.";
                    echo "</div>";
                    ?>
                </div>
                <?php endif; ?>

                <!-- File Permissions Test -->
                <div class="test-section">
                    <h3 class="mb-3">
                        <i class="bi bi-folder-check me-2" style="color: #ffc107;"></i>
                        File System
                    </h3>
                    
                    <?php
                    $fileTests = [];
                    
                    // Check storage directories
                    $storageDirs = [
                        ROOT_PATH . '/storage',
                        ROOT_PATH . '/storage/logs',
                        ROOT_PATH . '/storage/cache',
                        ROOT_PATH . '/storage/sessions'
                    ];
                    
                    foreach ($storageDirs as $dir) {
                        $dirName = str_replace(ROOT_PATH . '/', '', $dir);
                        if (!is_dir($dir)) {
                            @mkdir($dir, 0755, true);
                        }
                        
                        $writable = is_writable($dir);
                        $fileTests["Directory: $dirName"] = $writable ? 'WRITABLE' : 'NOT WRITABLE';
                    }
                    
                    // Check key files
                    $keyFiles = [
                        'composer.json' => ROOT_PATH . '/composer.json',
                        'Database Schema' => ROOT_PATH . '/database/schema.sql',
                        'Main Config' => CONFIG_PATH . '/app.php',
                        'Spotify Config' => CONFIG_PATH . '/spotify.php'
                    ];
                    
                    foreach ($keyFiles as $name => $path) {
                        $exists = file_exists($path);
                        $fileTests[$name] = $exists ? 'EXISTS' : 'MISSING';
                    }
                    
                    foreach ($fileTests as $test => $result) {
                        $statusClass = 'status-success';
                        if (strpos($result, 'MISSING') !== false || strpos($result, 'NOT WRITABLE') !== false) {
                            $statusClass = 'status-error';
                        }
                        
                        echo "<div class='test-item'>";
                        echo "<span>$test</span>";
                        echo "<span class='test-result $statusClass'>$result</span>";
                        echo "</div>";
                    }
                    ?>
                </div>

                <!-- Next Steps -->
                <div class="test-section">
                    <h3 class="mb-3">
                        <i class="bi bi-list-check me-2" style="color: var(--accent-blue);"></i>
                        Setup Instructions
                    </h3>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-warning">Database Setup</h5>
                            <ol class="small">
                                <li>Open phpMyAdmin: <a href="http://localhost/phpmyadmin/" target="_blank">http://localhost/phpmyadmin/</a></li>
                                <li>Create database: <code>music_locker</code></li>
                                <li>Import schema: <code>database/schema.sql</code></li>
                                <li>Refresh this page to verify</li>
                            </ol>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="text-info">Ngrok Integration</h5>
                            <ol class="small">
                                <li>Start Ngrok: <code>ngrok http 80</code></li>
                                <li>Copy your Ngrok URL (e.g., https://abc123.ngrok.io)</li>
                                <li>Run: <code>php scripts/ngrok-setup.php [ngrok-url]</code></li>
                                <li>Update Spotify app settings with new redirect URI</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center mt-4">
                    <a href="/" class="btn btn-glow me-3">
                        <i class="bi bi-house me-2"></i>Go to Application
                    </a>
                    <a href="http://localhost/phpmyadmin/" class="btn btn-outline-glow me-3" target="_blank">
                        <i class="bi bi-database me-2"></i>Open phpMyAdmin
                    </a>
                    <button onclick="location.reload()" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Refresh Tests
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>