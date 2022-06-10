<?php

namespace App\Base\Sql;
use DateTime;

require_once __DIR__ . "/../base/baseOperand.php";

class PrimitiveTypeOperand extends BaseOperand
{
    private mixed $operand;
    private bool $needQuotes;

    public function __construct(int|string $operand, ?bool $needQuotes = null)
    {
        $this->operand = $operand;
        $this->needQuotes = $needQuotes === null ? gettype($operand) != 'integer' : $needQuotes;
    }

    public function render(): string
    {
        return $this->needQuotes ? "'$this->operand'" : (string)$this->operand;
    }
}
