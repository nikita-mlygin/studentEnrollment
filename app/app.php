<?php

namespace App;

require_once __DIR__.'/../controller/base/IController.php';

use App\Base\Controller\IController;
use App\Base\Model\IModel;

class App
{
    private array $requetsAdress = [];
    private array $controllersFiles = [];
    private IController $controller;
    private string $dbname;

    private array $moduleList = [];

    static private ?App $instanse = null;

    static public function getApp(): App
    {
        if(static::$instanse == null)
        {
            static::$instanse = new static;
        }

        return static::$instanse;
    }

    public function __construct()
    {
        $this->moduleList = require_once moduleDir . '/moduleList.php';

        if(isset($_SERVER['REQUEST_URI']))
        {
            $this->requetsAdress = explode('/', explode('?', $_SERVER['REQUEST_URI'])[0]);
            $this->controllersFiles = require_once __DIR__.'/../controller/main.php';
        }
    }

    function start(array $config): void
    {
        if(isset($this->controllersFiles[$this->requetsAdress[1]]))
        {
            $this->controller = require_once __DIR__.'/../controller/'.$this->controllersFiles[$this->requetsAdress[1]];
        }

        if(isset($this->requetsAdress[2]))
            $this->controller->runAction($this->requetsAdress[2]);
        else
            $this->controller->runDefault();
    }

    public function getModule(string $moduleName)
    {
        if(isset($this->moduleList[$moduleName]))
        {
            if(!isset($this->moduleList[$moduleName]['included']))
            {
                require_once $this->moduleList[$moduleName]['file'];

                $this->moduleList[$moduleName]['initF']()->init();
            }

            return $this->moduleList[$moduleName]['initF']();
        } else
        {
            print_r($this->moduleList);
            throw new \Error("Isnt have module with name: $moduleName\r\n");
        }
    }
}