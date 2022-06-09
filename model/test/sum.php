<?php
namespace App\Model;

use App\Base\Model\IModel;

require_once __DIR__.'/../base/IModel.php';

class Sum implements IModel
{
    public float $a, $b;

    public function __construct(float $a, $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    public function run()
    {

    }

    public function getResult(): float
    {
        return $this->a + $this->b;
    }
}