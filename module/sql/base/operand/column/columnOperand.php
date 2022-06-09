<?php

namespace App\Base\Sql;

require_once __DIR__ . '/../base/baseOperand.php';

class ColumnOperand extends AliasesBaseOperand
{
    protected string $columnName;
    private ?TableOperand $tableName;
    protected ?string $alias; 

    public function __construct(string $columnName, ?TableOperand $table = null, ?string $alias = null)
    {
        $this->columnName = $columnName;
        $this->tableName = $table;
        $this->alias = $alias;
    }

    protected function operandRender(): string
    {
        return $this->tableName === null?'`' . $this->columnName . '`' : '`' . $this->tableName->render() . '`.`' . $this->tableName . '`' . parent::render();
    }
}