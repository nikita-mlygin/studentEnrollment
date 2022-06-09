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
        $this->scores[$disciplineName] = $score;

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
    protected array $columns;
    protected BaseSelectQuery $baseQueryObject;
    protected BaseSelectQueryBuilder $baseQueryBuilder;

    protected function resetBaseQuery()
    {
        $this->baseQueryBuilder->resetObject(clone $this->baseQueryObject);
    }

    protected function getColumn(string $columnName): ColumnOperand
    {
        if (isset($this->columns[$columnName])) {
            return $this->columns[$columnName];
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
            'studentId' => new ColumnOperand('ref_student_id'),
            'disciplineName' => new ColumnOperand('student_score_discipline_name'),
            'score' => new ColumnOperand('student_score')
        ];

        $this->baseQueryObject = new BaseSelectQuery($this->table);
        $this->baseQueryBuilder = new BaseSelectQueryBuilder(clone $this->baseQueryObject);

        $disciplineGroups = [ // TODO Перенести куда-нибудь
            'base' => ['Математика', 'Русский'],
            'technical' => ['Физика', 'Информатика'],
            'bio' => ['Химия', 'Биология'],
            'lala' => ['Обществознание', 'История']
        ];
    }

    private function getAverageGradeQueryBuilder(): BaseSelectQueryBuilder
    {
        return $this->baseQueryBuilder
            ->addSelectColumn(new FunctionOperand('avg', [$this->columns['score']], 'average'))
            ->group($this->columns['studentId']);
    }

    public function setAverageGradeQuery(): StudentScoreQueryBuilder
    {
        $this->getAverageGradeQueryBuilder()->getObj();

        return $this;
    }

    private function getGroupOverageScoreQueryBuilder(array $disciplineList): BaseSelectQueryBuilder
    {
        return $this->getAverageGradeQueryBuilder()
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

    public function setGroupAverageScoreQuery(array $disciplineList): StudentScoreQueryBuilder
    {
        $this->getGroupOverageScoreQueryBuilder($disciplineList);

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
            'id' => new ColumnOperand('speciality_id'),
            'name' => new ColumnOperand('speciality_name'),
            'description' => new ColumnOperand('speciality_description'),
            'direction' => new ColumnOperand('speciality_direction'),
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
            'studentId' => new ColumnOperand('ref_student_speciality_student_id'),
            'specialityId' => new ColumnOperand('ref_student_speciality_speciality_id'),
            'priority' => new ColumnOperand('student_speciality_priority'),
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
            'id' => new ColumnOperand('student_id'),
            'firstName' => new ColumnOperand('student_first_name'),
            'lastName' => new ColumnOperand('student_last_name'),
            'patronymic' => new ColumnOperand('student_patronymic'),
            'certificate' => new ColumnOperand('student_certificate'),
            'groupId' => new ColumnOperand('ref_student_group_id'),
        ];

        $this->baseQueryObject = new BaseSelectQuery($this->table);
        $this->baseQueryBuilder = new BaseSelectQueryBuilder(clone $this->baseQueryObject);
    }

    public function joinOverage(?StudentScoreQueryBuilder $builder = null): StudentQueryBuilderTable
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

    public function getById(int $id): StudentQueryBuilderTable
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

        $this->getById($id);

        $builder->joinThisTable($this->baseQueryBuilder, 'studentId', $this->columns['id']);

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

echo $studentQuery
    ->getScoresById(1, ['disciplineName', 'score'])
    ->getQuery()
    ->render();