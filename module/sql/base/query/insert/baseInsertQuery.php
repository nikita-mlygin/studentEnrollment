<?php

namespace App\Base\Sql;

require_once __DIR__ . '/../base/ISqlQuery.php';
require_once __DIR__ . '/../../join/joinSql.php';
require_once __DIR__ . '/../../operand/primitive/primitiveTypeOperand.php';
require_once __DIR__ . '/../../operand/table/table.php';
require_once __DIR__ . '/../../operand/base/baseOperand.php';

/**
 * @property ColumnOperand[] $columns
 * @property array<array<PrimitiveTypeOperand>> $values
*/
class BaseInsertQuery implements ISqlQuery
{
    public TableOperand $table;
    public array $columns = [];
    public array $values = [];

    public function render(): string
    {
        return 'insert into ' . $this->table->render() . ' (' . $this->renderColumns() . ') values ' . $this->renderValues();
    }

    private function renderColumns(): string
    {
        $result = '';

        foreach ($this->columns as $item) {
            $result .= $item->render() . ', ';
        }

        return substr($result, 0, -2);
    }

    private function renderValues(): string
    {
        $result = '';

        foreach ($this->values as $item) {
            $result .= '(';

            foreach ($item as $subItem) {
                $result .= $subItem->render() . ', ';
            }

            $result = substr($result, 0, -2) . '), ';
        }

        return substr($result, 0, -2);
    }

    static public function create()
    {
        return new BaseInsertQueryBuilder(new static);
    }
}

class BaseInsertQueryBuilder
{
    public function __construct(BaseInsertQuery $object)
    {
        $this->object = $object;
    }

    private BaseInsertQuery $object;

    public function insertInto(TableOperand|string $table): BaseInsertQueryBuilder
    {
        $this->object->table = gettype($table) == 'string' ? new TableOperand($table) : $table;

        return $this;
    }

    public function bindColumn(string|ColumnOperand $column): BaseInsertQueryBuilder
    {
        array_push($this->object->columns, gettype($column) == 'string' ? new ColumnOperand($column) : $column);
        
        return $this;
    }

    public function bindValues(array $values): BaseInsertQueryBuilder
    {
        $lastIndx = array_push($this->object->values, []) - 1;


        foreach ($values as $item) {
            array_push($this->object->values[$lastIndx], $item instanceof BaseOperand ? $item : new PrimitiveTypeOperand($item));
        }
        
        return $this;
    }

    public function getQuery(): BaseInsertQuery
    {
        return $this->object;
    }
}