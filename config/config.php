<?php
/**
 * Configuration file for Sistema de Gestión para Comedores Industriales
 * 
 * This file contains database credentials and application settings
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'majorbot_comedores');
define('DB_USER', 'majorbot_comedores');
define('DB_PASS', 'Danjohn007!');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'Sistema de Gestión para Comedores Industriales');
define('APP_VERSION', '1.0.0');

// Timezone
date_default_timezone_set('America/Mexico_City');

// Auto-detect URL Base
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $path = str_replace('\\', '/', dirname($script));
    $baseUrl = $protocol . "://" . $host . $path;
    
    // Remove trailing slash if exists
    return rtrim($baseUrl, '/');
}

define('BASE_URL', getBaseUrl());

// Error Reporting - Set to 0 in production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');

return [
    'db' => [
        'host' => DB_HOST,
        'name' => DB_NAME,
        'user' => DB_USER,
        'pass' => DB_PASS,
        'charset' => DB_CHARSET
    ],
    'app' => [
        'name' => APP_NAME,
        'version' => APP_VERSION,
        'base_url' => BASE_URL
    ]
];
