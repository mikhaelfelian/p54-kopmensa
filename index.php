<?php
date_default_timezone_set('Asia/Jakarta');
/**
 * Front Controller
 * Redirect all requests to public/index.php
 */

// Security headers
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

// Check if request is for a static file
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$publicPath = __DIR__ . '/public' . $uri;

// Let CodeIgniter handle the environment
if (file_exists(__DIR__ . '/app/Config/Boot/development.php') || file_exists(__DIR__ . '/app/Config/Boot/testing.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

if (file_exists($publicPath) && is_file($publicPath)) {
    // Serve static files directly
    return false;
} else {
    // Forward to public/index.php
    require_once __DIR__ . '/public/index.php';
}