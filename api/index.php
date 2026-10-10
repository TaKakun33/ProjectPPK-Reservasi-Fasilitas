<?php
// Folder yang bisa ditulis di Vercel hanya /tmp
$dirs = [
    '/tmp/storage/app/private',
    '/tmp/storage/app/public',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];
foreach ($dirs as $d) {
    if (!is_dir($d)) {
        @mkdir($d, 0777, true);
    }
}

$env = [
    'LARAVEL_STORAGE_PATH'  => '/tmp/storage',
    'VIEW_COMPILED_PATH'    => '/tmp/storage/framework/views',
    'APP_SERVICES_CACHE'    => '/tmp/bootstrap/cache/services.php',
    'APP_PACKAGES_CACHE'    => '/tmp/bootstrap/cache/packages.php',
    'APP_CONFIG_CACHE'      => '/tmp/bootstrap/cache/config.php',
    'APP_ROUTES_CACHE'      => '/tmp/bootstrap/cache/routes.php',
    'APP_EVENTS_CACHE'      => '/tmp/bootstrap/cache/events.php',
];
foreach ($env as $k => $v) {
    putenv("$k=$v");
    $_ENV[$k] = $v;
    $_SERVER[$k] = $v;
}

// Sementara untuk debugging: tampilkan error di halaman
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', 'php://stderr');
error_reporting(E_ALL);

try {
    require __DIR__ . '/../public/index.php';
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo get_class($e) . ': ' . $e->getMessage() . "\n"
        . $e->getFile() . ':' . $e->getLine() . "\n\n"
        . $e->getTraceAsString();
}