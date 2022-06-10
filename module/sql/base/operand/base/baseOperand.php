<?php

namespace App\Base\Sql;

abstract class BaseOperand
{
    abstract public function render(): string;
}

abstract class AliasesBaseOperand extends BaseOperand
{
    protected ?string $alias;

    abstract protected function operandRender(): string;

    public function render(): string
    {
        try {
            return $this->operandRender() . (\is_null($this->alias) ? '' : " as `$this->alias`");
        }
        catch (\Throwable $th) {
            print_r($th);
            throw $th;
        }

    }

    public function setAliases(string $alias): AliasesBaseOperand
    {
        $this->alias = $alias;

        return $this;
    }
}