<?php

/**
 * Laravel Development Server Router
 *
 * This file is the entry point for PHP's built-in web server when running
 * `php artisan serve`. It automatically routes every request through the
 * public/ directory, so you do NOT need to run `php artisan serve` from
 * inside the public/ folder.
 *
 * Usage:
 *   php artisan serve
 *   php -S localhost:8000 server.php   ← manual alternative
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/'
);

// If a real static file exists inside public/, serve it directly.
$publicPath = __DIR__ . '/public' . $uri;

if ($uri !== '/' && file_exists($publicPath) && !is_dir($publicPath)) {
    return false; // Let the built-in server serve the file as-is
}

// Everything else → Laravel front controller
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/public/index.php';
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/public';

require __DIR__ . '/public/index.php';
