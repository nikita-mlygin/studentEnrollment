<?php
use App\Base\Controller\BaseController;
use App\Model\Sum;

require_once __DIR__.'/../base/BaseController.php';

class Test extends BaseController
{
    public function __construct()
    {
        parent::__construct(['default' => 'test/test.php', 'sum' => 'test/sum.php']);
    }

    public function runAction(string $actionName)
    {
        parent::includeModelFile($actionName);
        $view = new SumView();

        if($actionName == 'sum')
        {
            $this->model = new Sum($_GET['a'], $_GET['b']);
        }

        require_once viewDir . '/test/sum.php';

        $view->viewSum($this->model);
    }

    public function runDefault()
    {
        echo "Default request without actions";
    }
}

$test = new Test();

return $test;
