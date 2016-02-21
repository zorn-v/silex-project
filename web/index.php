<?php

require_once __DIR__ . '/../config/load.php';

$app = new App\Application();

require_once APP_ROOT . '/config/db.php';

require_once APP_ROOT . '/config/services.php';
require_once APP_ROOT . '/config/routes.php';

$app->run();
