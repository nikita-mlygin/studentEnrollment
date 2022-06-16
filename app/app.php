<?php

namespace App;

require_once __DIR__ . '/../controller/base/IController.php';

use App\Base\Controller\IController;
use App\Base\Model\IModel;
use App\Base\Module\IModule;

final class App
{
    /**
     * @var string[] $requestUrl
     */
    private array $requestUrl = [];
    /**
     * @var array<string,string> $controllersFiles
     */
    private array $controllersFiles = [];

    private IController $controller;
    /**
     * @var array<string,array{initF:callable-string,file:string,included:bool}> $moduleList
     */
    private array $moduleList = [];

    static private ?App $instance = null;

    static public function getApp(): App
    {
        if (static::$instance == null) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    public function __construct()
    {
        $this->moduleList = require_once moduleDir . '/moduleList.php';

        if (isset($_SERVER['REQUEST_URI'])) {
            $this->requestUrl = explode('/', explode('?', $_SERVER['REQUEST_URI'])[0]);
            $this->controllersFiles = require_once __DIR__ . '/../controller/main.php';
        }
    }

    function start(array $config): void
    {
        $config; // TODO

        if (isset($this->controllersFiles[$this->requestUrl[1]])) {
            $this->controller = require_once __DIR__ . '/../controller/' . $this->controllersFiles[$this->requestUrl[1]];
        }

        if (isset($this->requestUrl[2])) {
            $this->controller->runAction($this->requestUrl[2]);
        }
        else {
            $this->controller->runDefault();
        }
    }

    public function getModule(string $moduleName): IModule
    {
        if (isset($this->moduleList[$moduleName])) {
            if (!isset($this->moduleList[$moduleName]['included'])) {
                require_once $this->moduleList[$moduleName]['file'];

                $this->moduleList[$moduleName]['initF']()->init();
            }

            return $this->moduleList[$moduleName]['initF']();
        }
        else {
            print_r($this->moduleList);
            throw new \Error("Isn't have module with name: $moduleName\r\n");
        }
    }
}