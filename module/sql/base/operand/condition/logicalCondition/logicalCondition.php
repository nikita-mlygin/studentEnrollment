<?php
namespace App\Base\Sql;

require_once __DIR__ . '/../../base/baseOperand.php';
require_once __DIR__ . '/../../column/columnOperand.php';

abstract class BaseLogicalCondition extends BaseOperand
{
    public function __construct(string $operandType)
    {
       $this->operatorType = $operandType;
    }

    protected string $operatorType;
}

class BaseTwoOperandLogicalCondition extends BaseLogicalCondition
{
    private BaseOperand $left;
    private BaseOperand $right;

    public function __construct(BaseOperand $left, BaseOperand $right, string $operatorType)
    {
        $this->left = $left;
        $this->right = $right;

        parent::__construct($operatorType);
    }

    public function render(): string
    {
        return ($this->left instanceof ColumnOperand
            ? $this->left->render()
            : ('(' . $this->left->render() . ') '))
            . " $this->operatorType " 
            . (
                $this->right instanceof PrimitiveTypeOperand
                    ? $this->right->render()
                    : '(' . $this->right->render() . ')'
                );
    }
}

class BaseOneOperandLogicalCondition extends BaseLogicalCondition
{
    private BaseOperand $operand;

    public function __construct(BaseOperand $operand, string $operatorType)
    {
        $this->operand = $operand;

        parent::__construct($operatorType);
    }

    public function render(): string
    {
        return "$this->operatorType(" . $this->operand->render() . ')';
    }
}

class BaseNOperandLogicalCondition extends BaseLogicalCondition
{
    private array $logicalOperands;

    /**
     * Class constructor.
     */
    public function __construct(BaseLogicalCondition $logicalOperand)
    {
        $this->logicalOperands = [['', $logicalOperand]];
    }

    public function add(BaseLogicalCondition $logicalOperand, string $operand)
    {
        array_push($this->logicalOperands, [$operand, $logicalOperand]);
    }

    public function render(): string
    {
        $result = '';

        foreach ($this->logicalOperands as $value) {
            $result .= ($value[0] == '' ? '(' : ' ' . $value[0] . ' (')
                . $value[1]->render() . ')';
        }

        return $result;
    }
}