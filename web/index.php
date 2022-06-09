<?php

declare(strict_types = 1);

namespace App;

require_once __DIR__ . '/../app/app.php';
require_once __DIR__ . '/../config/const.php';
$config = include __DIR__.'/../config/config.php';


$app = App::getApp();

$app->start($config);