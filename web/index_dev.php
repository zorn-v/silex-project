<?php

//From symfony framework app_dev.php
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1')) || php_sapi_name() === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

require_once __DIR__ . '/../config/load.php';

$app = new App\Application();
$app['debug'] = true;

require_once APP_ROOT . '/config/db.php';

require_once APP_ROOT . '/config/services.php';
require_once APP_ROOT . '/config/routes.php';

$app->run();
