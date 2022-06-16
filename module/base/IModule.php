<?php

namespace App\Base\Module;

interface IModule
{
    static public function getInstance(): IModule;

    public function init(): void;

    public function __construct();
}