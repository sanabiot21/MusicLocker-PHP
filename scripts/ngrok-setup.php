<?php
/**
 * Ngrok Setup Helper Script
 * Music Locker - Team NaturalStupidity
 * 
 * This script helps configure Ngrok integration for Spotify OAuth
 * Run this script when starting Ngrok to update the environment variables
 * 
 * Usage: php scripts/ngrok-setup.php <ngrok-url>
 * Example: php scripts/ngrok-setup.php https://abc123.ngrok.io
 */

if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

require_once __DIR__ . '/../vendor/autoload.php';

function updateEnvFile(string $ngrokUrl): bool
{
    $envPath = __DIR__ . '/../.env';
    
    if (!file_exists($envPath)) {
        echo "❌ Error: .env file not found at: $envPath\n";
        echo "Please ensure the .env file exists before running this script.\n";
        return false;
    }
    
    $envContent = file_get_contents($envPath);
    
    if ($envContent === false) {
        echo "❌ Error: Could not read .env file\n";
        return false;
    }
    
    // Calculate new redirect URI
    $redirectUri = rtrim($ngrokUrl, '/') . '/api/spotify/callback';
    
    // Update APP_URL to use Ngrok HTTPS URL
    if (preg_match('/^APP_URL=.*$/m', $envContent)) {
        $envContent = preg_replace('/^APP_URL=.*$/m', 'APP_URL=' . $ngrokUrl, $envContent);
    } else {
        $envContent .= "\nAPP_URL=" . $ngrokUrl . "\n";
    }
    
    // Update NGROK_URL
    if (preg_match('/^NGROK_URL=.*$/m', $envContent)) {
        $envContent = preg_replace('/^NGROK_URL=.*$/m', 'NGROK_URL=' . $ngrokUrl, $envContent);
    } else {
        $envContent .= "\n# Ngrok Configuration\nNGROK_URL=" . $ngrokUrl . "\n";
    }
    
    // Update SPOTIFY_REDIRECT_URI
    if (preg_match('/^SPOTIFY_REDIRECT_URI=.*$/m', $envContent)) {
        $envContent = preg_replace('/^SPOTIFY_REDIRECT_URI=.*$/m', 'SPOTIFY_REDIRECT_URI=' . $redirectUri, $envContent);
    } else {
        $envContent .= "SPOTIFY_REDIRECT_URI=" . $redirectUri . "\n";
    }
    
    // Write back to file
    if (file_put_contents($envPath, $envContent) === false) {
        echo "❌ Error: Could not write to .env file\n";
        return false;
    }
    
    return true;
}

function displayInstructions(string $ngrokUrl, string $redirectUri): void
{
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "🎵 MUSIC LOCKER - NGROK SETUP COMPLETE\n";
    echo str_repeat("=", 60) . "\n";
    
    echo "\n✅ Configuration Updated:\n";
    echo "   App URL: $ngrokUrl\n";
    echo "   Ngrok URL: $ngrokUrl\n";
    echo "   Redirect URI: $redirectUri\n";
    
    echo "\n📝 Next Steps:\n";
    echo "1. Update your Spotify App settings:\n";
    echo "   • Go to: https://developer.spotify.com/dashboard\n";
    echo "   • Edit your Music Locker app\n";
    echo "   • Add this Redirect URI: $redirectUri\n\n";
    
    echo "2. Start your local development server:\n";
    echo "   • XAMPP: Start Apache & MySQL\n";
    echo "   • Or run: php -S 127.0.0.1:8888 -t public\n\n";
    
    echo "3. Access your application:\n";
    echo "   • Local: http://musiclocker.local\n";
    echo "   • Or: http://127.0.0.1:8888\n";
    echo "   • Ngrok: $ngrokUrl\n\n";
    
    echo "4. Test Spotify OAuth:\n";
    echo "   • Register/login to your Music Locker account\n";
    echo "   • Try connecting to Spotify from the dashboard\n\n";
    
    echo "⚠️  Important Notes:\n";
    echo "   • This Ngrok URL is temporary and changes each restart\n";
    echo "   • Rerun this script whenever you restart Ngrok\n";
    echo "   • Always update Spotify app settings with new redirect URI\n";
    echo "   • APP_URL updated to use HTTPS for proper asset loading\n";
    
    echo "\n" . str_repeat("=", 60) . "\n";
}

// Main script execution
if ($argc < 2) {
    echo "❌ Usage: php scripts/ngrok-setup.php <ngrok-url>\n";
    echo "Example: php scripts/ngrok-setup.php https://abc123.ngrok.io\n";
    exit(1);
}

$ngrokUrl = trim($argv[1]);

// Validate Ngrok URL (support both .ngrok.io and .ngrok-free.app formats)
if (!filter_var($ngrokUrl, FILTER_VALIDATE_URL) || 
    !preg_match('/^https:\/\/[a-zA-Z0-9\-]+\.ngrok(\-free\.app|\.io)$/', $ngrokUrl)) {
    echo "❌ Error: Invalid Ngrok URL format\n";
    echo "Expected formats: https://abc123.ngrok.io or https://abc123.ngrok-free.app\n";
    exit(1);
}

echo "🔧 Setting up Ngrok integration for Music Locker...\n";
echo "Ngrok URL: $ngrokUrl\n";

if (updateEnvFile($ngrokUrl)) {
    $redirectUri = rtrim($ngrokUrl, '/') . '/api/spotify/callback';
    displayInstructions($ngrokUrl, $redirectUri);
    echo "✅ Setup complete! Environment variables updated successfully.\n";
} else {
    echo "❌ Setup failed. Please check the errors above and try again.\n";
    exit(1);
}