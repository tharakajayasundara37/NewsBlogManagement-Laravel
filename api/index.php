<?php
use Illuminate\Http\Request;
define('LARAVEL_START', microtime(true));
$storage = '/tmp/news-hub-storage';
foreach (['framework/cache', 'framework/sessions', 'framework/views', 'logs'] as $directory) { if (! is_dir($storage.'/'.$directory)) mkdir($storage.'/'.$directory, 0777, true); }
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->useStoragePath($storage);
$app->handleRequest(Request::capture());
