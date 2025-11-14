<?php
/**
 * Debug script to check server variables
 */
require_once __DIR__ . '/../config/config.php';

header('Content-Type: text/plain');

echo "=== SERVER VARIABLES ===\n\n";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";
echo "HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "\n";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "\n=== CALCULATED VALUES ===\n\n";

$requestUri = $_SERVER['REQUEST_URI'];
$requestUri = strtok($requestUri, '?');
echo "Clean REQUEST_URI: $requestUri\n";

$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = str_replace('/index.php', '', $scriptName);
echo "SCRIPT_NAME: $scriptName\n";
echo "BASE_PATH: $basePath\n";

$requestPath = str_replace($basePath, '', $requestUri);
echo "REQUEST_PATH (after removing base): $requestPath\n";

echo "\n=== BASE_URL ===\n\n";
echo "BASE_URL: " . BASE_URL . "\n";
