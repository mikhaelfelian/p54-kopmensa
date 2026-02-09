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
    $mimeType = mime_content_type($publicPath);
    if ($mimeType === false) {
        // Fallback MIME type detection based on extension
        $extension = strtolower(pathinfo($publicPath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'pdf' => 'application/pdf',
            'xml' => 'application/xml',
            'txt' => 'text/plain',
            'html' => 'text/html',
        ];
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
    }
    
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . filesize($publicPath));
    readfile($publicPath);
    exit();
} else {
    // Forward to public/index.php
    require_once __DIR__ . '/public/index.php';
}