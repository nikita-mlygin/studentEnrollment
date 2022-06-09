<?php

namespace App\Base\Sql;

require_once __DIR__ . '/ISqlQuery.php';

interface ISqlQueryRender
{
    function render(ISqlQuery|BaseOperand $query): string;
}
