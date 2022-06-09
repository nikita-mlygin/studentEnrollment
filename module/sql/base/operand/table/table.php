<?php

namespace App\Base\Sql;

require_once __DIR__ . '/../base/baseOperand.php';

class TableOperand extends BaseOperand
{
    private string $tableName;
    private ?string $databaseName;

    public function __construct(string $tableName, ?string $databaseName = null)
    {
        $this->tableName = $tableName;
        $this->databaseName = $databaseName;
    }

    public function render(): string
    {
        return $this->databaseName === null?$this->tableName:"$this->databaseName.$this->tableName";
    }
}