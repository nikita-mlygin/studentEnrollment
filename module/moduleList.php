<?php

use App\Module\MySqlModule;

$modules = 
[
    'sql' => [
        'initF' => 'App\Module\MySqlModule::getInstance',
        'file' => __DIR__ . '/sql/MySql.php',
    ]
];

return $modules;