<?php

declare(strict_types=1);

use App\App;
use App\Http\Request;

// Let PHP's built-in dev server serve real files under public/ directly.
if (PHP_SAPI === 'cli-server') {
    $file = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
    if ($file !== false && is_file($file) && str_starts_with($file, __DIR__ . DIRECTORY_SEPARATOR)) {
        return false;
    }
}

require dirname(__DIR__) . '/vendor/autoload.php';

try {
    $app = App::boot(dirname(__DIR__));
} catch (Throwable $e) {
    error_log('Application failed to boot: ' . $e);
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'The application is not configured correctly.';
    exit;
}

$app->handle(Request::fromGlobals($app->config->basePath()))->send();
