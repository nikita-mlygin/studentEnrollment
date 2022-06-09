<?php

namespace App\Base\Sql;

class FunctionOperand extends AliasesBaseOperand
{
    /**
     * @property BaseOperand[] $operands
     * @var BaseOperand[] $operands
     * @param BaseOperand[] $operands
     */
    private array $operands;
    private string $functionName;

    /**
     * Class constructor.
     */
    public function __construct(string $functionName, array $operands, ?string $alias = null)
    {
        $this->functionName = $functionName;
        $this->operands = $operands;
        $this->alias = $alias;
    }

    protected function operandRender(): string
    {
        return "$this->functionName (". $this->renderOperands() . ')';
    }

    private function renderOperands(): string
    {
        $result = '';


        foreach ($this->operands as $item) {
            $result .= $item->render() . ', ';
        }

        return substr($result, 0, -2);
    }
}
