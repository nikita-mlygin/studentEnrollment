<?php

namespace App\Base\Controller;


require_once __DIR__ . '/IController.php';
require_once __DIR__ . '/../../model/base/IModel.php';

abstract class BaseController implements IController
{
    public function __construct(array $modelFiles)
    {
        $this->modelFiles = $modelFiles;
    }

    protected string $path = modelDir;
    protected \App\Base\Model\IModel $model;
    protected array $modelFiles;

    abstract function runAction(string $actionName);
    abstract function runDefault();

    function includeModelFile(string $modelName = 'default')
    {
        if (isset($this->modelFiles[$modelName])) {
            require_once $this->path . '/' . $this->modelFiles[$modelName];
        }
        else {
            throw new \Error('Model not found: $this->path . ' / ' . $this->modelFiles[$modelName]');
        }
    }
}