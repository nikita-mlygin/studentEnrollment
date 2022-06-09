<?php

namespace App\Base\Sql;

require_once __DIR__ . '/../base/ISqlQuery.php';
require_once __DIR__ . '/../../join/joinSql.php';
require_once __DIR__ . '/../../operand/primitive/primitiveTypeOperand.php';
require_once __DIR__ . '/../../operand/table/table.php';
require_once __DIR__ . '/../../operand/base/baseOperand.php';
require_once __DIR__ . '/../../operand/column/function.php';
require_once __DIR__ . '/../../operand/column/allColumn.php';

class BaseSelectQuery extends BaseOperand implements ISqlQuery
{
    public ?TableOperand $table;
    /**
     * @property Join[] $joins
     */
    public array $joins = [];
    public ?BaseLogicalCondition $where = null;
    public array $columns = [];
    public ?BaseOperand $group = null;
    public array $order = [[]];

    public function __construct(?TableOperand $table = null, array $joins = [], ?BaseLogicalCondition $where = null, array $columns = [])
    {
        $this->table = $table;
        $this->joins = $joins;
        $this->where = $where;
        $this->columns = $columns;
    }

    public function render(): string
    {
        return 'select ' . $this->columnsRender() . '  from ' . $this->table->render() . ' ' . $this->joinRender() .
            ($this->where === null
            ? ''
            : ' where ' . $this->where->render()
            ) . ($this->group === null ? '' : ' group by ' . $this->group->render())
            . ($this->order == [[]] ? '' : 'order by ' . $this->renderOrder());
    }

    private function renderOrder(): string
    {
        $result = '';

        foreach ($this->order as $value) {
            $result .= $value[0]->render();

            if (isset($value[1])) {
                $result .= ' ' . $value[1];
            }

            $result .= ', ';
        }

        return substr($result, 0, -2);
    }

    private function columnsRender(): string
    {
        if ($this->columns === null || count($this->columns) == 0) {
            return '*';
        }

        $returnValue = '';

        foreach ($this->columns as $item) {
            if ($item instanceof BaseOperand) {
                $returnValue .= $item->render() . ', ';
            }
            else {
                throw new \Error('Operand must be column');
            }
        }

        return substr($returnValue, 0, -2);
    }

    private function joinRender(): string
    {
        if ($this->joins === null || count($this->joins) == 0) {
            return '';
        }
        $returnValue = '';

        foreach ($this->joins as $item) {
            if ($item instanceof Join) {
                $returnValue .= $item->render() . ' ';
            }
            else {
                throw new \Error('Operand must be table');
            }
        }

        return substr($returnValue, 0, -1);
    }

    static function create(): BaseSelectQueryBuilder
    {
        return new BaseSelectQueryBuilder(new static ());
    }
}

interface IConditionBuilder
{
    public function start(string|BaseOperand $left, string|BaseOperand $right, string $operator);
    public function startN();
    public function and ();
    public function or ();
    public function not();
    public function end();
}

class BaseConditionBuilder implements IConditionBuilder
{
    private array $previousCondition = [];

    private array $select = ['start', null];


    public function start(string|BaseOperand $left, string|int|BaseOperand $right, string $operator): BaseConditionBuilder
    {
        $this->select[1] = new BaseNOperandLogicalCondition(
            new BaseTwoOperandLogicalCondition
            (
            gettype($left) == 'string' ? new ColumnOperand($left) : $left,
            gettype($right) == 'string' || gettype($right) == 'integer' ? new PrimitiveTypeOperand($right) : $right,
            $operator
            )
            );

        return $this;
    }

    public function startN(): BaseConditionBuilder
    {
        array_push($this->previousCondition, $this->select);
        $this->select = ['start', null];

        return $this;
    }

    public function and (): BaseConditionBuilder
    {
        $argc = \func_num_args();

        if ($argc == 0) {
            $this->addConditionBlock('and');
        }
        else if ($argc == 3) {
            $this->addConditionToSelect(\func_get_args(), 'and');
        }

        return $this;
    }

    public function or ()
    {
        $argc = \func_num_args();

        if ($argc == 0) {
            $this->addConditionBlock('or');
        }
        else if ($argc == 3) {
            $this->addConditionToSelect(\func_get_args(), 'or');
        }

        return $this;
    }

    private function addConditionBlock(string $operator)
    {
        array_push($this->previousCondition, $this->select);
        $this->select = [$operator, null];
    }

    private function addConditionToSelect(array $args, string $operator)
    {
        $this->select[1]->add(
            new BaseTwoOperandLogicalCondition(
            new ColumnOperand($args[0]),
            new PrimitiveTypeOperand($args[1]),
            $args[2]
            ),
            $operator
        );
    }

    public function end(): BaseConditionBuilder
    {
        if ($this->select[0] == 'start') {
            $this->previousCondition[count($this->previousCondition) - 1][1] = $this->select[1];
        }
        else if ($this->select[0] != 'not') {
            $this->previousCondition[count($this->previousCondition) - 1][1]->add(
                $this->select[1],
                $this->select[0]
            );
        }
        else {
            $this->previousCondition[count($this->previousCondition) - 1][1] = new BaseNOperandLogicalCondition(
                new BaseOneOperandLogicalCondition($this->select[1], 'not')
                );
        }

        $this->select = end($this->previousCondition);
        array_pop($this->previousCondition);

        return $this;
    }

    public function not(): BaseConditionBuilder
    {
        $this->addConditionBlock('not');

        return $this;
    }

    function complete(): BaseLogicalCondition
    {
        return $this->select[1];
    }
}

abstract class BaseSelectWhereConditionBuilder extends BaseSelectQueryBuilder implements IConditionBuilder
{
    private BaseConditionBuilder $selectQueryBuilder;

    public function __construct(BaseSelectQuery $object)
    {
        parent::__construct($object);

        $this->selectQueryBuilder = new BaseConditionBuilder();
    }

    abstract public function complete(): BaseSelectQueryBuilder;

    protected function getRes(): BaseLogicalCondition
    {
        return $this->selectQueryBuilder->complete();
    }

    function start(string|BaseOperand $left, string|BaseOperand $right, string $operator)
    {
        $this->selectQueryBuilder->start($left, $right, $operator);

        return $this;
    }

    function startN()
    {
        $this->selectQueryBuilder->startN;

        return $this;
    }

    function and ()
    {
        switch (\func_num_args()) {
            case 0:
                $this->selectQueryBuilder->and();
                break;
            case 3:
                $args = \func_get_args();
                $this->selectQueryBuilder->and($args[0], $args[1], $args[2]);
                break;
            default:
                # code...
                break;
        }

        return $this;
    }

    function or ()
    {
        switch (\func_num_args()) {
            case 0:
                $this->selectQueryBuilder->or();
                break;
            case 3:
                $args = \func_get_args();
                $this->selectQueryBuilder->or($args[0], $args[1], $args[2]);
                break;
            default:
                # code...
                break;
        }

        return $this;
    }

    function not()
    {
        $this->selectQueryBuilder->not();

        return $this;
    }

    function end()
    {
        $this->selectQueryBuilder->end();

        return $this;
    }
}

class BaseSelectQueryBuilder
{
    protected BaseSelectQuery $object;

    public function __construct(BaseSelectQuery $query)
    {
        $this->object = $query;
    }

    public function addWhereAnd(BaseLogicalCondition $condition): BaseSelectQueryBuilder
    {
        if (\is_null($this->object->where)) {
            $this->object->where = $condition;
        }
        else {
            $this->object->where = new BaseTwoOperandLogicalCondition($this->object->where, $condition, 'and');
        }


        return $this;
    }

    private function bindAddOrderArray(array $column)
    {
        if (isset($column[0][0])) {
            if ($this->object->order[0] == [] || empty($this->object->order)) {
                $this->object->order = $column;
            }
            else {
                $this->object->order = array_merge($this->object->order, $column);
            }
        }
        else {
            if ($this->object->order == [] || empty($this->object->order)) {
                $this->object->order[0] = $column;
            }
            else {
                array_push($this->object->order, $column);
            }
        }
    }

    public function addOrder(string|ColumnOperand|array $column, ?string $type = null): BaseSelectQueryBuilder
    {
        if (gettype($column) == 'array') {
            $this->bindAddOrderArray($column);
        }
        else {
            if (\gettype($column) == 'string') {
                $column = new ColumnOperand($column);
            }

            $result = [$column];

            if (!is_null($type)) {
                array_push($result, $type);
            }

            array_push($this->object->order, $result);
        }


        return $this;
    }

    public function setOrder(array $order): BaseSelectQueryBuilder
    {
        $this->object->order = $order;

        return $this;
    }

    public function addSelectColumns(array $columns): BaseSelectQueryBuilder
    {
        foreach ($columns as $item) {
            $this->addSelectColumn($item);
        }

        return $this;
    }

    public function resetObject(BaseSelectQuery $object)
    {
        $this->object = $object;
    }

    public function select(?array $columns = null): BaseSelectQueryBuilder
    {
        if ($columns === null) {
            $this->object->columns = [new AllColumn()];
        }
        else {
            $this->object->columns = $columns;
        }

        return $this;
    }

    public function addSelectColumn(BaseOperand $column): BaseSelectQueryBuilder
    {
        array_push($this->object->columns, $column);

        return $this;
    }

    public function from(string $tableName, ?string $databaseName = null): BaseSelectQueryBuilder
    {
        $this->object->table = new TableOperand($tableName, $databaseName = null);

        return $this;
    }

    public function where(): BaseSelectQueryWhereBuilder
    {
        return new BaseSelectQueryWhereBuilder($this->object);
    }

    public function join(): BaseSelectQueryJoinBuilder
    {
        $args = \func_get_args();

        switch (\func_num_args()) {
            case 1:
                array_push($this->object->joins, new Join
                    (
                    gettype($args[0]) == 'string' ? new TableOperand($args[0]) : $args[0]
                    )
                );
                break;
            case 2:
                array_push($this->object->joins, new Join($args[0], $args[1]));
                break;

            default:
                throw new \Error('Неверное количество параметров');
        }

        if ($this instanceof BaseSelectQueryJoinBuilder) {
            return $this;
        }
        else {
            return new BaseSelectQueryJoinBuilder($this->object);
        }
    }

    public function getObj()
    {
        return $this->object;
    }

    public function group(string|BaseOperand $group): BaseSelectQueryBuilder
    {
        $this->object->group = (\gettype($group) == 'string' ? new ColumnOperand($group) : $group);

        return $this;
    }
}

class BaseSelectQueryWhereBuilder extends BaseSelectWhereConditionBuilder
{
    public function complete(): BaseSelectQueryBuilder
    {
        $this->object->where = $this->getRes();

        return $this;
    }
}

class BaseSelectQueryJoinBuilder extends BaseSelectWhereConditionBuilder
{
    public function on(): BaseSelectQueryJoinBuilder
    {
        switch (\func_num_args()) {
            case 0:
                break;
            case 3:
                $args = \func_get_args();
                $this->start($args[0], $args[1], $args[2]);
                break;
            default:
                # code...
                break;
        }

        return $this;
    }

    public function complete(): BaseSelectQueryBuilder
    {
        $this->object->joins[count($this->object->joins) - 1]->bindOn($this->getRes());

        return $this;
    }
}