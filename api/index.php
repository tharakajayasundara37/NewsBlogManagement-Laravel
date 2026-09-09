<?php
use Illuminate\Http\Request;
define('LARAVEL_START', microtime(true));
$storage = '/tmp/news-hub-storage';
foreach (['framework/cache', 'framework/sessions', 'framework/views', 'logs'] as $directory) { if (! is_dir($storage.'/'.$directory)) mkdir($storage.'/'.$directory, 0777, true); }

$runtimePaths = [
    'APP_MAINTENANCE_DRIVER' => 'file',
    'APP_CONFIG_CACHE' => '/tmp/news-hub-config.php',
    'APP_EVENTS_CACHE' => '/tmp/news-hub-events.php',
    'APP_PACKAGES_CACHE' => '/tmp/news-hub-packages.php',
    'APP_ROUTES_CACHE' => '/tmp/news-hub-routes.php',
    'APP_SERVICES_CACHE' => '/tmp/news-hub-services.php',
    'VIEW_COMPILED_PATH' => $storage.'/framework/views',
    'BROADCAST_CONNECTION' => 'log',
    'FILESYSTEM_DISK' => 'local',
    'QUEUE_CONNECTION' => 'sync',
    'MAIL_MAILER' => 'log',
];

foreach ($runtimePaths as $name => $value) {
    putenv($name.'='.$value);
    $_ENV[$name] = $value;
    $_SERVER[$name] = $value;
}

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $app->useStoragePath($storage);
    $app->handleRequest(Request::capture());
} catch (Throwable $exception) {
    error_log('NewsHub bootstrap failure: '.$exception);
    http_response_code(500);
    echo 'NewsHub is temporarily unavailable.';
}
