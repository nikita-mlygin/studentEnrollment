<?php

namespace App\Base\Sql;

require_once __DIR__ . '/columnOperand.php';

class AllColumn extends ColumnOperand
{
    public function __construct()
    {
        parent::__construct('*');
    }

    protected function operandRender(): string
    {
        return $this->columnName;
    }

    public function render(): string
    {
        return $this->columnName;
    }
}
