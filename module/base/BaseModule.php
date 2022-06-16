<?php

namespace App\Base\Module;

require_once __DIR__ . '/IModule.php';

abstract class BaseModule implements IModule
{
    static protected ?BaseModule $instance = null;

    public static function getInstance(): BaseModule
    {
        if (static::$instance == null) {
            static::$instance = new static ();
        }

        return static::$instance;
    }

    abstract public function init(): void;
}