<?php
use App\Base\View\BaseView;
use App\Base\Model\IModel;
use App\Model\Sum;

require_once viewDir . '/base/BaseView.php';

class SumView extends BaseView
{
    public float $sumResult;

    public function __construct()
    {
        parent::__construct(__DIR__, [
            'sum' => 'sumHtml.php',
            'default' => 'testHtml.php'
        ]);
    }

    public function viewDefault(): void
    {
        
    }

    private function viewSum($model)
    {
        if ($this->model instanceof Sum)
        {
            $this->sumResult = $model->getResult();
        }
        else
        {
            $data = [
                'result' => 0,
            ];

            throw new Exception('Model isnt Sum');
        }

        include $this->path . '/' . $this->viewFiles['sum'];
    }
}
