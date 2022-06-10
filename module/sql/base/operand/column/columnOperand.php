<?php

namespace App\Base\Sql;

require_once __DIR__ . '/../base/baseOperand.php';

class ColumnOperand extends AliasesBaseOperand
{
    protected string $columnName;
    private null|TableOperand|string $tableName;
    protected ?string $alias;

    public function __construct(string $columnName, null|TableOperand|string $table = null, ?string $alias = null)
    {
        $this->columnName = $columnName;
        $this->tableName = $table;
        $this->alias = $alias;
    }

    public function setTable(TableOperand|string $operand): ColumnOperand
    {
        $this->tableName = $operand;

        return $this;
    }

    protected function operandRender(): string
    {
        if ($this->tableName === null) {
            return '`' . $this->columnName . '`';
        }
        else {
            if (gettype($this->tableName) == 'string') {
                return '`' . $this->tableName . '`.`' . $this->columnName . '`';
            }
            else {
                return '`' . $this->tableName->render() . '`.`' . $this->columnName . '`';
            }
        }
    }
}