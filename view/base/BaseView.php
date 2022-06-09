<?php

namespace App\Base\View;

use App\Base\Model\IModel;


require_once modelDir . '/base/IModel.php';
require_once __DIR__ . '/IView.php';

abstract class BaseView implements IView
{
    protected IModel $model; 
    protected array $viewFiles;
    protected string $path;

    public function __construct(string $path, array $viewFiles)
    {
        $this->viewFiles = $viewFiles;
        $this->path = $path;
    }

    protected function includeViewFile(string $viewName)
    {
        require "$this->path/" . $this->viewFiles[$viewName];
    }

    abstract public function viewDefault(): void;
}
