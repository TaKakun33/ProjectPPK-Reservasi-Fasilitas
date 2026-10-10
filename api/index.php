<?php
foreach (['/tmp/storage/framework/views','/tmp/storage/framework/cache','/tmp/storage/framework/sessions','/tmp/storage/logs','/tmp/bootstrap/cache'] as $d) {
    if (!is_dir($d)) mkdir($d, 0777, true);
}
$_ENV['VIEW_COMPILED_PATH'] = $_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');

require __DIR__ . '/../public/index.php';