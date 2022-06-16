<?php

namespace App\Entities;

require_once moduleDir . '/sql/MySql.php';


use stdClass;
use App\Base\Sql\BaseSelectQuery;
use App\Base\Sql\TableOperand;
use App\Base\Sql\BaseSelectQueryBuilder;
use App\Base\Sql\BaseSelectQueryJoinBuilder;
use App\Base\Sql\BaseConditionBuilder;
use App\Base\Sql\IConditionBuilder;
use App\Base\Sql\BaseOperand;
use App\Base\Sql\ColumnOperand;
use App\Base\Sql\Join;
use App\Base\Sql\BaseTwoOperandLogicalCondition;
use App\Base\Sql\FunctionOperand;
use App\Base\Sql\AllColumn;
use App\Base\Sql\BaseLogicalCondition;
use App\Base\Sql\PrimitiveTypeOperand;
use App\Base\Sql\AliasesBaseOperand;

/**
 * Summary of Student
 * @property array<string,int> $scores
 */
class Student
{
    public string $firstName;
    public string $lastName;
    public string $patronymic;

    public float $averageGrade;

    public array $scores = [
        'maths' => 0,
        'physics' => 0,
        'informatics' => 0,
        'biology' => 0,
        'chemistry' => 0,
    ];
    public bool $certificateIsBrought;

    public array $specialty = [];

    public array $contacts;

    public static function create(): StudentBuilder
    {
        return new StudentBuilder(new Student);
    }
}

class StudentBuilder
{
    private Student $buildObj;

    public function __construct(Student $buildObj)
    {
        $this->buildObj = $buildObj;
    }

    public function firstName(string $firstName): StudentBuilder
    {
        $this->buildObj->firstName = $firstName;

        return $this;
    }

    public function lastName(string $lastName): StudentBuilder
    {
        $this->buildObj->lastName = $lastName;

        return $this;
    }

    public function scores(string $disciplineName, int $score): StudentBuilder
    {
        $this->buildObj->scores[$disciplineName] = $score;

        return $this;
    }

    public function getObj(): Student
    {
        return $this->buildObj;
    }

    public function getObjByArray(stdClass $inArray): Student
    {
        if (isset($inArray->firstName)) {
            $this->buildObj->firstName = $inArray->firstName;
        }
        else {
            throw new \Error('firstName not found');
        }

        if (isset($inArray->lastName)) {
            $this->buildObj->lastName = $inArray->lastName;
        }
        else {
            throw new \Error('lastName not found');
        }

        return $this->buildObj;
    }
}

class BaseQueryBuilderForTable
{
    protected TableOperand $table;
    /**
     * Summary of 
     * @var array{string:BaseOperand}
     */
    protected array $columns;
    protected BaseSelectQuery $baseQueryObject;
    protected BaseSelectQueryBuilder $baseQueryBuilder;

    protected function resetBaseQuery()
    {
        $this->baseQueryBuilder->resetObject(clone $this->baseQueryObject);
    }

    protected function getColumn(string $columnName, ?string $alias = null): ColumnOperand
    {
        if (isset($this->columns[$columnName])) {
            if (\is_null($alias)) {
                return $this->columns[$columnName];
            }
            else if ($this->columns[$columnName] instanceof AliasesBaseOperand) {
                return (clone $this->columns[$columnName])->setAliases($alias);
            }
            else {
                throw new \Error("Can't use aliases on non-aliases object");
            }
        }

        throw new \Error('Column not found');
    }

    public function setColumns(array $columns): BaseQueryBuilderForTable
    {
        foreach ($columns as $value) {
            if (isset($this->columns[$value])) {
                $this->baseQueryBuilder->addSelectColumn($this->columns[$value]);
            }
        }

        return $this;
    }

    public function joinThisTable(BaseSelectQueryBuilder $builder, string|ColumnOperand $referenceColumnName, ColumnOperand $targetColumn)
    {
        $result = $this->baseQueryBuilder->getObj();

        if (\gettype($referenceColumnName) == 'string') {
            $referenceColumnName = $this->getColumn($referenceColumnName);
        }

        $builder
            ->join($this->table)
            ->on($referenceColumnName, $targetColumn, '=')
            ->complete()
            ->addSelectColumns($result->columns);

        if (!\is_null($result->joins)) {
            foreach ($result->joins as $item) {
                $builder->join($item);
            }
        }

        if (!\is_null($result->where)) {
            $builder
                ->addWhereAnd($result->where);
        }

        if (!\is_null($result->group)) {
            $builder
                ->group($result->group);
        }

        if ($result->order != [[]]) {
            $builder->addOrder($result->order);
        }

        $this->resetBaseQuery();

        return $builder;
    }

    public function getQuery(): BaseSelectQuery
    {
        $result = $this->baseQueryBuilder->getObj();

        $this->resetBaseQuery();

        return $result;
    }
}

class StudentScoreQueryBuilder extends BaseQueryBuilderForTable
{
    public TableOperand $table;

    public array $columns;

    public BaseSelectQuery $baseQueryObject;
    public BaseSelectQueryBuilder $baseQueryBuilder;

    public array $disciplineGroups;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->table = new TableOperand('student_score');

        $this->columns = [
            'studentId' => new ColumnOperand('ref_student_id', $this->table),
            'disciplineName' => new ColumnOperand('student_score_discipline_name', $this->table),
            'score' => new ColumnOperand('student_score', $this->table),
        ];

        $this->baseQueryObject = new BaseSelectQuery($this->table);
        $this->baseQueryBuilder = new BaseSelectQueryBuilder(clone $this->baseQueryObject);
    }

    private function getAverageGradeQueryBuilder(string $alias = 'average'): BaseSelectQueryBuilder
    {
        return $this->baseQueryBuilder
            ->addSelectColumn(new FunctionOperand('avg', [$this->columns['score']], $alias))
            ->group($this->columns['studentId']);
    }

    public function setAverageGradeQuery(): StudentScoreQueryBuilder
    {
        $this->getAverageGradeQueryBuilder()->getObj();

        return $this;
    }

    private function getGroupOverageScoreQueryBuilder(array $disciplineList, string $alias): BaseSelectQueryBuilder
    {
        return $this->getAverageGradeQueryBuilder($alias)
            ->where()->start($this->columns['disciplineName'], new PrimitiveTypeOperand($this->getDisciplineListStr($disciplineList), false), 'in')->complete();
    }

    private function getDisciplineListStr(array $disciplineList): string
    {
        $result = '(';

        foreach ($disciplineList as $item) {
            $result .= "'$item', ";
        }

        return substr($result, 0, -2) . ')';
    }

    public function setGroupAverageScoreQuery(array $disciplineList, string $alias = 'average_group'): StudentScoreQueryBuilder
    {
        $this->getGroupOverageScoreQueryBuilder($disciplineList, $alias);

        return $this;
    }

    /**
     * Sorting by average grade and average group grade
     * @param string[] $disciplines
     * @return StudentScoreQueryBuilder
     */
    public function setAverageAndGroup(array $disciplines): StudentScoreQueryBuilder
    {
        $scoreBaseTable = (clone $this->getColumn('score'))->setTable($this->table);
        $baseStudentId = (clone $this->getColumn('studentId'))->setTable($this->table);
        $selectStudentId = (clone $this->getColumn('studentId'))->setTable('avg_select');
        $avgScoreSelect = new ColumnOperand('average_group', 'avg_select');
        $avgScore = new ColumnOperand('average');

        $selectQuery = ($this->setGroupAverageScoreQuery($disciplines)->setColumns(['studentId'])->getQuery()->setAliases('avg_select'));

        $this->baseQueryBuilder->select([
            new FunctionOperand('avg', [$scoreBaseTable], 'average'),
            $avgScoreSelect
        ])
            ->from($this->table)
            ->join($selectQuery)
            ->on($baseStudentId, $selectStudentId, '=')
            ->complete()
            ->group($baseStudentId)
            ->addOrder([[$avgScore, 'desc'], [$avgScoreSelect, 'desc']]);

        return $this;
    }

    public function getQuery(): BaseSelectQuery
    {
        $result = $this->baseQueryBuilder->getObj();

        $this->resetBaseQuery();

        return $result;
    }

    public function joinThisTableToStudent(BaseSelectQueryBuilder $builder, ColumnOperand $studentId): BaseSelectQueryBuilder
    {
        return $this->joinThisTable($builder, 'studentId', $studentId);
    }

    public function sort(string $type = 'desc'): StudentScoreQueryBuilder
    {
        $this->baseQueryBuilder->addOrder('average', $type);

        return $this;
    }
}

class SpecialityQueryBuilderTable extends BaseQueryBuilderForTable
{
    public function __construct()
    {
        $this->table = new TableOperand('speciality');

        $this->columns =
        [
            'id' => new ColumnOperand('speciality_id', $this->table),
            'name' => new ColumnOperand('speciality_name', $this->table),
            'description' => new ColumnOperand('speciality_description', $this->table),
            'direction' => new ColumnOperand('speciality_direction', $this->table),
        ];


        $this->baseQueryObject = new BaseSelectQuery($this->table);
        $this->baseQueryBuilder = new BaseSelectQueryBuilder(clone $this->baseQueryObject);
    }

    public function joinThisTableToStudentSpeciality(BaseSelectQueryBuilder $builder, ColumnOperand $specialityRefId)
    {
        return $this->joinThisTable($builder, $this->columns['id'], $specialityRefId);
    }
}

class StudentSpecialityQueryBuilderTable extends BaseQueryBuilderForTable
{
    public function __construct()
    {
        $this->table = new TableOperand('student_speciality');

        $this->columns =
        [
            'studentId' => new ColumnOperand('ref_student_speciality_student_id', $this->table),
            'specialityId' => new ColumnOperand('ref_student_speciality_speciality_id', $this->table),
            'priority' => new ColumnOperand('student_speciality_priority', $this->table),
        ];


        $this->baseQueryObject = new BaseSelectQuery($this->table);
        $this->baseQueryBuilder = new BaseSelectQueryBuilder(clone $this->baseQueryObject);
    }

    public function priorityFilter(int $priority): StudentSpecialityQueryBuilderTable
    {
        $this->baseQueryBuilder->where()->start($this->columns['priority'], new PrimitiveTypeOperand($priority), '=')->complete();

        return $this;
    }

    public function specialityFilter(int $specialityId): StudentSpecialityQueryBuilderTable
    {
        $this->baseQueryBuilder
            ->addWhereAnd(
            new BaseTwoOperandLogicalCondition(
            $this->columns['specialityId'], new PrimitiveTypeOperand($specialityId), '=')
        );

        return $this;
    }

    public function joinToThisSpeciality(SpecialityQueryBuilderTable $builder): StudentSpecialityQueryBuilderTable
    {
        $builder->joinThisTableToStudentSpeciality($this->baseQueryBuilder, $this->columns['specialityId']);

        return $this;
    }

    public function joinToThisStudent(StudentQueryBuilderTable $builder): StudentSpecialityQueryBuilderTable
    {
        $builder->joinThisTable($this->baseQueryBuilder, 'id', $this->columns['studentId']);

        return $this;
    }

    public function joinStudentScore(StudentScoreQueryBuilder $builder): StudentSpecialityQueryBuilderTable
    {
        $builder->joinThisTable($this->baseQueryBuilder, 'studentId', $this->columns['studentId']);

        return $this;
    }

    public function joinStudentAverageScore(array $columns = [], ?StudentScoreQueryBuilder $builder = null): StudentSpecialityQueryBuilderTable
    {
        if (\is_null($builder)) {
            $builder = new StudentScoreQueryBuilder();
        }

        if ($columns == []) {
            $builder->setColumns($columns);
        }

        $builder->setAverageGradeQuery()->sort();

        $this->joinStudentScore($builder);

        return $this;
    }

    public function joinStudentAverageGroupScore(array $disciplineGroup, array $columns = [], ?StudentScoreQueryBuilder $builder = null): StudentSpecialityQueryBuilderTable
    {
        if (\is_null($builder)) {
            $builder = new StudentScoreQueryBuilder();
        }

        if ($columns == []) {
            $builder->setColumns($columns);
        }

        $builder->setGroupAverageScoreQuery($disciplineGroup);

        $this->joinStudentScore($builder);
        return $this;
    }

    public function joinStudent(array $columns = [], ?StudentQueryBuilderTable $builder = null): StudentSpecialityQueryBuilderTable
    {
        if (\is_null($builder)) {
            $builder = new StudentQueryBuilderTable();
        }

        if ($columns != []) {
            $builder->setColumns($columns);
        }

        $this->joinToThisStudent($builder);
        return $this;
    }
}

class StudentQueryBuilderTable extends BaseQueryBuilderForTable
{
    public function __construct()
    {
        $this->table = new TableOperand('student');
        $this->columns =
        [
            'id' => new ColumnOperand('student_id', $this->table),
            'firstName' => new ColumnOperand('student_first_name', $this->table),
            'lastName' => new ColumnOperand('student_last_name', $this->table),
            'patronymic' => new ColumnOperand('student_patronymic', $this->table),
            'certificate' => new ColumnOperand('student_certificate', $this->table),
            'groupId' => new ColumnOperand('ref_student_group_id', $this->table),
        ];

        $this->baseQueryObject = new BaseSelectQuery($this->table);
        $this->baseQueryBuilder = new BaseSelectQueryBuilder(clone $this->baseQueryObject);
    }

    public function joinScores(StudentScoreQueryBuilder $builder): StudentQueryBuilderTable
    {
        $builder->joinThisTable($this->baseQueryBuilder, 'studentId', $this->columns['id']);

        return $this;
    }

    public function joinAverage(?StudentScoreQueryBuilder $builder = null): StudentQueryBuilderTable
    {
        if (is_null($builder)) {
            $builder = new StudentScoreQueryBuilder();
        }

        $builder->setAverageGradeQuery()->sort();

        $builder->joinThisTable($this->baseQueryBuilder, 'studentId', $this->columns['id']);

        return $this;
    }

    public function joinAverageGroup(array $disciplineGroup, ?StudentScoreQueryBuilder $builder): StudentQueryBuilderTable
    {
        if (is_null($builder)) {
            $builder = new StudentScoreQueryBuilder();
        }

        $builder->setGroupAverageScoreQuery($disciplineGroup)->sort();

        return $this;
    }

    public function filterById(int|array $id): StudentQueryBuilderTable
    {
        $this->baseQueryBuilder->addWhereAnd(new BaseTwoOperandLogicalCondition($this->columns['id'], new PrimitiveTypeOperand($id), '='));

        return $this;
    }

    public function getScoresById(int $id, array $columns = [], ?StudentScoreQueryBuilder $builder = null): StudentQueryBuilderTable
    {
        if (is_null($builder)) {
            $builder = new StudentScoreQueryBuilder();
        }

        if ($columns != []) {
            $builder->setColumns($columns);
        }

        $this->filterById($id);

        $builder->joinThisTable($this->baseQueryBuilder, 'studentId', $this->columns['id']);

        return $this;
    }

    /**
     * Summary of getStudentsInSpeciality
     * @param int $specialityId
     * @param array{joinAverageScore:bool,scoreBuilder:StudentScoreQueryBuilder,disciplineGroup:array<string>} $settings
     * @return StudentQueryBuilderTable
     */
    public function getStudentsInSpeciality(int $specialityId, array $settings): StudentQueryBuilderTable
    {
        $specialityId; // TODO

        if (isset($settings['joinAverageScore'])) {
            if (!isset($settings['averageScoreBuilder'])) {
                $builder = new StudentScoreQueryBuilder();
            }
            else {
                $builder = $settings['averageScoreBuilder'];
            }

            if (isset($settings['averageScoreBuilder'])) {
                $builder->setGroupAverageScoreQuery($settings['scoreBuilder']);
            }
            else {
                $builder->setAverageGradeQuery();
            }
        }

        return $this;
    }
}


$studentScoreQuery = new StudentScoreQueryBuilder;
$studentScoreQuery->setAverageGradeQuery();

$query1 = new SpecialityQueryBuilderTable();
$query1->setColumns(['name']);

$query = new StudentSpecialityQueryBuilderTable();

$studentQuery = new StudentQueryBuilderTable;
$studentQuery->setColumns(['firstName', 'lastName']);

$query = (new StudentScoreQueryBuilder())
    ->setAverageAndGroup(['Математика', 'Русский']);

// echo $studentQuery->joinScores($query)->getQuery()->render();