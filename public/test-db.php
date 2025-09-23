<?php
/**
 * Quick Database Connection Test
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Connection Test</title>
    <style>
        body { background: #0a0a0a; color: #fff; font-family: Arial; padding: 20px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { background: #1a3d5f; padding: 15px; border-radius: 8px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🗄️ Database Connection Test</h1>
    
    <?php
    try {
        $host = 'localhost';
        $username = 'root';
        $password = '';
        
        // Test connection
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<div class='success'>✅ MySQL Connection: SUCCESS</div>";
        
        // Check if database exists
        $stmt = $pdo->query("SHOW DATABASES LIKE 'music_locker'");
        if ($stmt->rowCount() > 0) {
            echo "<div class='success'>✅ Database 'music_locker': EXISTS</div>";
        } else {
            echo "<div class='error'>❌ Database 'music_locker': NOT FOUND</div>";
            echo "<div class='info'>Run this command in XAMPP Shell:<br><code>mysql -u root<br>CREATE DATABASE music_locker;</code></div>";
        }
        
        // Show available databases
        $stmt = $pdo->query("SHOW DATABASES");
        $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<div class='info'><strong>Available databases:</strong><br>";
        foreach ($databases as $db) {
            echo "• $db<br>";
        }
        echo "</div>";
        
    } catch (PDOException $e) {
        echo "<div class='error'>❌ Database Connection FAILED</div>";
        echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
        
        echo "<div class='info'>";
        echo "<strong>Try these solutions:</strong><br>";
        echo "1. Make sure MySQL is running in XAMPP<br>";
        echo "2. Try: mysqladmin -u root password \"\"<br>";
        echo "3. Edit my.ini: add default_authentication_plugin=mysql_native_password<br>";
        echo "</div>";
    }
    ?>
    
    <p><a href="/">← Back to Home</a></p>
</body>
</html>