<?php
/**
 * Router for PHP Built-in Server
 * This simulates the .htaccess rewrite behavior
 * Use with: php -S localhost:8080 router.php
 */

// Get the requested URI
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If it's a real file, serve it
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Serve the file as-is
}

// Otherwise, route to index.php
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
