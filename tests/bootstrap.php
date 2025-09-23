<?php
/**
 * Test Bootstrap
 * 
 * Initialize testing environment
 */

// Define paths
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('SRC_PATH', ROOT_PATH . '/src');

// Include Composer autoloader
require_once ROOT_PATH . '/vendor/autoload.php';

// Load helper functions for testing
require_once SRC_PATH . '/Utils/HelperFunctions.php';
require_once SRC_PATH . '/Security/CsrfManager.php';
require_once SRC_PATH . '/Utils/UrlHelper.php';
require_once SRC_PATH . '/Utils/ConfigManager.php';

// Set testing environment
$_ENV['APP_ENV'] = 'testing';
$_ENV['DB_DATABASE'] = 'music_locker_test';

// Start session for testing
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}