<?php

namespace App\Base\Sql;

require_once __DIR__ . '/../operand/condition/logicalCondition/logicalCondition.php';
require_once __DIR__ . "/../operand/table/table.php";

class Join
{
    public ?string $joinType = null;
    public TableOperand|BaseSelectQuery $joinTable;
    public ?BaseLogicalCondition $joinCondition = null;

    public function __construct()
    {
        $paramCount = \func_num_args();
        $paramArray = \func_get_args();

        switch ($paramCount) {
            case 2:
                if (gettype($paramArray[0]) == 'string' && $paramArray[1] instanceof TableOperand) {
                    $this->joinType = $paramArray[0];
                    $this->joinTable = $paramArray[1];
                }
                else if (($paramArray[0] instanceof TableOperand || $paramArray[0] instanceof BaseSelectQuery) && $paramArray[1] instanceof BaseLogicalCondition) {
                    $this->joinTable = $paramArray[0];
                    $this->joinCondition = $paramArray[1];
                }
                break;
            case 3:
                if (gettype($paramArray[0]) == 'string' && $paramArray[1] instanceof TableOperand && $paramArray[2] instanceof BaseLogicalCondition) {
                    $this->joinType = $paramArray[0];
                    $this->joinTable = $paramArray[1];
                    $this->joinCondition = $paramArray[2];
                }
                break;
            case 1:
                if ($paramArray[0] instanceof TableOperand || $paramArray[0] instanceof BaseSelectQuery) {
                    $this->joinTable = $paramArray[0];
                }
                break;
            default:
                throw new \Error('Params count must be 3');
        }
    }

    public function bindOn(BaseLogicalCondition $joinCondition)
    {
        $this->joinCondition = $joinCondition;
    }

    public function render(): string
    {
        return
            ($this->joinType === null ? '' : ($this->joinType . ' '))
            . 'join ' . $this->joinTable->render()
            . ($this->joinCondition !== null
            ? (' on ' . $this->joinCondition->render())
            : '');
    }
}