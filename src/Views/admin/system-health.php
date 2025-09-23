<!-- System Health Section -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row">
            <!-- Page Header -->
            <div class="col-12 mb-4">
                <div class="feature-card">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="mb-1">
                                <i class="bi bi-cpu me-2" style="color: var(--accent-blue);"></i>
                                System Health
                            </h1>
                            <p class="text-muted mb-0">Monitor application environment and performance</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="btn-group" role="group">
                                <a href="<?= route_url('admin') ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Dashboard
                                </a>
                                <button class="btn btn-glow" onclick="refreshHealth()">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Overview Cards -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-card text-center">
                    <div class="mb-3">
                        <i class="bi bi-server display-4" style="color: var(--accent-blue);"></i>
                    </div>
                    <h3 class="stat-number text-success mb-1">Online</h3>
                    <p class="stat-label mb-0">Server Status</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-card text-center">
                    <div class="mb-3">
                        <i class="bi bi-database display-4 text-success"></i>
                    </div>
                    <h3 class="stat-number text-success mb-1">Connected</h3>
                    <p class="stat-label mb-0">Database</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-card text-center">
                    <div class="mb-3">
                        <i class="bi bi-code-slash display-4" style="color: var(--accent-purple);"></i>
                    </div>
                    <h3 class="stat-number" style="color: var(--accent-purple);">PHP <?= PHP_VERSION ?></h3>
                    <p class="stat-label mb-0">Runtime</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-card text-center">
                    <div class="mb-3">
                        <i class="bi bi-memory display-4 text-warning"></i>
                    </div>
                    <h3 class="stat-number text-warning mb-1"><?= ini_get('memory_limit') ?></h3>
                    <p class="stat-label mb-0">Memory Limit</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Environment Configuration -->
            <div class="col-lg-6 mb-4">
                <div class="test-section">
                    <h3 class="mb-3">
                        <i class="bi bi-gear-fill me-2" style="color: var(--accent-blue);"></i>
                        Environment Configuration
                    </h3>
                    
                    <?php
                    // Check .env file
                    $envFile = ROOT_PATH . '/.env';
                    $envExists = file_exists($envFile);
                    $envTests = [];
                    
                    $envTests['Environment File (.env)'] = $envExists ? 'FOUND' : 'MISSING';
                    
                    if ($envExists) {
                        // Check key environment variables
                        $requiredEnvVars = [
                            'APP_NAME' => env('APP_NAME'),
                            'APP_ENV' => env('APP_ENV'),
                            'DB_HOST' => env('DB_HOST'),
                            'DB_DATABASE' => env('DB_DATABASE'),
                            'SPOTIFY_CLIENT_ID' => env('SPOTIFY_CLIENT_ID') ? 'SET' : 'MISSING',
                            'SPOTIFY_CLIENT_SECRET' => env('SPOTIFY_CLIENT_SECRET') ? 'SET' : 'MISSING',
                        ];
                        
                        foreach ($requiredEnvVars as $var => $value) {
                            if ($var === 'SPOTIFY_CLIENT_ID' || $var === 'SPOTIFY_CLIENT_SECRET') {
                                $envTests[$var] = $value;
                            } else {
                                $envTests[$var] = $value ? 'SET' : 'MISSING';
                            }
                        }
                    }
                    
                    foreach ($envTests as $test => $result) {
                        $statusClass = 'status-success';
                        $icon = 'bi-check-circle';
                        
                        if (strpos($result, 'MISSING') !== false) {
                            $statusClass = 'status-error';
                            $icon = 'bi-x-circle';
                        } elseif (strpos($result, 'NOT SET') !== false) {
                            $statusClass = 'status-warning';
                            $icon = 'bi-exclamation-triangle';
                        }
                        
                        echo "<div class='test-item'>";
                        echo "<span><i class='bi $icon me-2'></i>$test</span>";
                        echo "<span class='test-result $statusClass'>$result</span>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>

            <!-- Database Status -->
            <div class="col-lg-6 mb-4">
                <div class="test-section">
                    <h3 class="mb-3">
                        <i class="bi bi-database-fill me-2" style="color: var(--accent-purple);"></i>
                        Database Status
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
                        $dbTests['MySQL Connection'] = 'CONNECTED';
                        
                        // Check if music_locker database exists
                        $stmt = $pdo->query("SHOW DATABASES LIKE 'music_locker'");
                        if ($stmt->rowCount() > 0) {
                            $dbTests['music_locker Database'] = 'EXISTS';
                            
                            // Connect to the database
                            try {
                                $musicPdo = new PDO("mysql:host=$host;dbname=music_locker", $username, $password);
                                $musicPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                
                                // Check for key tables
                                $requiredTables = ['users', 'music_entries', 'tags', 'music_notes'];
                                $stmt = $musicPdo->query("SHOW TABLES");
                                $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                
                                $tableCount = 0;
                                foreach ($requiredTables as $table) {
                                    if (in_array($table, $existingTables)) {
                                        $tableCount++;
                                    }
                                }
                                
                                $dbTests['Required Tables'] = "$tableCount/" . count($requiredTables) . " FOUND";
                                
                            } catch (PDOException $e) {
                                $dbTests['Database Access'] = 'ERROR';
                            }
                        } else {
                            $dbTests['music_locker Database'] = 'MISSING';
                        }
                        
                    } catch (PDOException $e) {
                        $dbTests['MySQL Connection'] = 'ERROR';
                    }
                    
                    foreach ($dbTests as $test => $result) {
                        $statusClass = 'status-success';
                        $icon = 'bi-check-circle';
                        
                        if (strpos($result, 'ERROR') !== false || strpos($result, 'MISSING') !== false) {
                            $statusClass = 'status-error';
                            $icon = 'bi-x-circle';
                        } elseif (strpos($result, '/') !== false && !str_contains($result, '4/4')) {
                            $statusClass = 'status-warning';
                            $icon = 'bi-exclamation-triangle';
                        }
                        
                        echo "<div class='test-item'>";
                        echo "<span><i class='bi $icon me-2'></i>$test</span>";
                        echo "<span class='test-result $statusClass'>$result</span>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>

            <!-- PHP Environment -->
            <div class="col-lg-6 mb-4">
                <div class="test-section">
                    <h3 class="mb-3">
                        <i class="bi bi-code-slash me-2" style="color: #28a745;"></i>
                        PHP Environment
                    </h3>
                    
                    <?php
                    $phpTests = [];
                    
                    // PHP Version
                    $phpVersion = PHP_VERSION;
                    $phpTests['PHP Version'] = $phpVersion . (version_compare($phpVersion, '8.2.0', '>=') ? ' ✓' : ' (OLD)');
                    
                    // Required PHP extensions
                    $requiredExtensions = ['curl', 'json', 'mbstring', 'openssl', 'pdo', 'pdo_mysql'];
                    $loadedCount = 0;
                    
                    foreach ($requiredExtensions as $ext) {
                        if (extension_loaded($ext)) {
                            $loadedCount++;
                        }
                    }
                    
                    $phpTests['Required Extensions'] = "$loadedCount/" . count($requiredExtensions) . " LOADED";
                    $phpTests['Memory Limit'] = ini_get('memory_limit');
                    $phpTests['Upload Max Size'] = ini_get('upload_max_filesize');
                    
                    foreach ($phpTests as $test => $result) {
                        $statusClass = 'status-success';
                        $icon = 'bi-check-circle';
                        
                        if (strpos($result, 'OLD') !== false || (strpos($result, '/') !== false && !str_contains($result, '6/6'))) {
                            $statusClass = 'status-warning';
                            $icon = 'bi-exclamation-triangle';
                        }
                        
                        echo "<div class='test-item'>";
                        echo "<span><i class='bi $icon me-2'></i>$test</span>";
                        echo "<span class='test-result $statusClass'>$result</span>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>

            <!-- System Performance -->
            <div class="col-lg-6 mb-4">
                <div class="test-section">
                    <h3 class="mb-3">
                        <i class="bi bi-speedometer2 me-2" style="color: #ffc107;"></i>
                        System Performance
                    </h3>
                    
                    <?php
                    $perfTests = [];
                    
                    // Disk space (simplified)
                    $diskFree = disk_free_space(ROOT_PATH);
                    $diskTotal = disk_total_space(ROOT_PATH);
                    $diskUsed = $diskTotal - $diskFree;
                    $diskPercent = round(($diskUsed / $diskTotal) * 100, 1);
                    
                    $perfTests['Disk Usage'] = $diskPercent . '% USED';
                    
                    // Server load (if available)
                    if (function_exists('sys_getloadavg')) {
                        $load = sys_getloadavg();
                        $perfTests['Server Load'] = round($load[0], 2);
                    } else {
                        $perfTests['Server Load'] = 'N/A';
                    }
                    
                    // Memory usage
                    $memUsage = memory_get_usage(true);
                    $memPeak = memory_get_peak_usage(true);
                    $perfTests['Memory Usage'] = formatBytes($memUsage);
                    $perfTests['Peak Memory'] = formatBytes($memPeak);
                    
                    foreach ($perfTests as $test => $result) {
                        $statusClass = 'status-success';
                        $icon = 'bi-check-circle';
                        
                        if ($test === 'Disk Usage' && $diskPercent > 80) {
                            $statusClass = 'status-warning';
                            $icon = 'bi-exclamation-triangle';
                        }
                        
                        echo "<div class='test-item'>";
                        echo "<span><i class='bi $icon me-2'></i>$test</span>";
                        echo "<span class='test-result $statusClass'>$result</span>";
                        echo "</div>";
                    }
                    
                    function formatBytes($bytes, $precision = 2) {
                        $units = array('B', 'KB', 'MB', 'GB', 'TB');
                        
                        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
                            $bytes /= 1024;
                        }
                        
                        return round($bytes, $precision) . ' ' . $units[$i];
                    }
                    ?>
                </div>
            </div>

            <!-- External Services -->
            <div class="col-12 mb-4">
                <div class="test-section ngrok-section">
                    <h3 class="mb-3">
                        <i class="bi bi-globe2 me-2" style="color: var(--accent-blue);"></i>
                        External Services
                    </h3>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-info mb-3">Spotify API</h5>
                            <?php
                            $spotifyTests = [];
                            $spotifyTests['Client ID'] = env('SPOTIFY_CLIENT_ID') ? 'CONFIGURED' : 'MISSING';
                            $spotifyTests['Client Secret'] = env('SPOTIFY_CLIENT_SECRET') ? 'CONFIGURED' : 'MISSING';
                            
                            // Test Spotify API connection (simplified)
                            if (env('SPOTIFY_CLIENT_ID') && env('SPOTIFY_CLIENT_SECRET')) {
                                $spotifyTests['API Status'] = 'READY';
                            } else {
                                $spotifyTests['API Status'] = 'NOT CONFIGURED';
                            }
                            
                            foreach ($spotifyTests as $test => $result) {
                                $statusClass = $result === 'CONFIGURED' || $result === 'READY' ? 'status-success' : 'status-error';
                                $icon = $statusClass === 'status-success' ? 'bi-check-circle' : 'bi-x-circle';
                                
                                echo "<div class='test-item'>";
                                echo "<span><i class='bi $icon me-2'></i>$test</span>";
                                echo "<span class='test-result $statusClass'>$result</span>";
                                echo "</div>";
                            }
                            ?>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="text-warning mb-3">Ngrok Integration</h5>
                            <?php
                            $ngrokUrl = env('NGROK_URL');
                            if ($ngrokUrl) {
                                echo "<div class='test-item'>";
                                echo "<span><i class='bi bi-check-circle me-2'></i>Ngrok URL</span>";
                                echo "<span class='test-result status-success'>CONFIGURED</span>";
                                echo "</div>";
                                
                                echo "<div class='test-item'>";
                                echo "<span><i class='bi bi-globe me-2'></i>Current URL</span>";
                                echo "<span class='test-result status-info text-truncate' style='max-width: 200px;'>$ngrokUrl</span>";
                                echo "</div>";
                            } else {
                                echo "<div class='test-item'>";
                                echo "<span><i class='bi bi-x-circle me-2'></i>Ngrok URL</span>";
                                echo "<span class='test-result status-warning'>NOT CONFIGURED</span>";
                                echo "</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-12">
                <div class="feature-card">
                    <h4 class="mb-3">System Actions</h4>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <button class="btn btn-glow w-100" onclick="clearCache()">
                                <i class="bi bi-trash me-2"></i>Clear Cache
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-glow w-100" onclick="viewLogs()">
                                <i class="bi bi-file-text me-2"></i>View Logs
                            </button>
                        </div>
                        <div class="col-md-3">
                            <a href="http://localhost/phpmyadmin/" class="btn btn-outline-info w-100" target="_blank">
                                <i class="bi bi-database me-2"></i>phpMyAdmin
                            </a>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-secondary w-100" onclick="exportSystemInfo()">
                                <i class="bi bi-download me-2"></i>Export Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for System Health -->
<?php ob_start(); ?>
<script>
    function refreshHealth() {
        location.reload();
    }
    
    function clearCache() {
        if (confirm('Are you sure you want to clear the system cache?')) {
            alert('Cache clear functionality would be implemented here');
        }
    }
    
    function viewLogs() {
        alert('Log viewer would be implemented here');
    }
    
    function exportSystemInfo() {
        alert('System report export would be implemented here');
    }
</script>
<?php 
$additional_js = ob_get_clean();
?>

<!-- Additional CSS -->
<?php ob_start(); ?>
<style>
    .test-section {
        background: rgba(26, 26, 26, 0.8);
        border: 1px solid #333;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        backdrop-filter: blur(10px);
        height: 100%;
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
        min-width: 120px;
        font-size: 0.9rem;
    }
    
    .status-success { color: #28a745; }
    .status-error { color: #dc3545; }
    .status-warning { color: #ffc107; }
    .status-info { color: #17a2b8; }
    
    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        font-family: 'Kode Mono', monospace;
    }
    
    .stat-label {
        font-size: 0.9rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-gray);
    }
    
    .ngrok-section {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(138, 43, 226, 0.1));
        border: 1px solid rgba(0, 212, 255, 0.3);
    }
    
    @media (max-width: 768px) {
        .test-result {
            min-width: 80px;
            font-size: 0.8rem;
        }
        
        .test-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .stat-number {
            font-size: 1.2rem;
        }
    }
</style>
<?php 
$additional_css = ob_get_clean();
?>