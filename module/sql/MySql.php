<?php

namespace App\Module;
use App\Base\Module\BaseModule;
use App\Base\Sql\BaseSelectQuery;
use App\Base\Sql\TableOperand;
use App\Base\Sql\BaseTwoOperandLogicalCondition;
use App\Base\Sql\ColumnOperand;
use App\Base\Sql\PrimitiveTypeOperand;
use App\Base\Sql\Join;
use App\Base\Sql\BaseSelectQueryBuilder;

require_once moduleDir . '/base/BaseModule.php';
require_once moduleDir . '/sql/base/query/select/BaseSelectQuery.php';
require_once moduleDir . '/sql/base/query/insert/BaseInsertQuery.php';


class MySqlModule extends BaseModule
{
    private string $username;
    private string $password;
    private string $serverName;
    private string $port;
    private string $dbname;

    private \PDO $connection;

    public function __construct()
    {
    // Need for implementation
    }

    public function init(): void
    {
        $settings = include configDir . '/database.php';

        $this->username = $settings['username'];
        $this->password = $settings['password'];
        $this->serverName = $settings['server'];
        $this->port = $settings['port'];
        $this->dbname = $settings['dbname'];

        $this->connection = new \PDO(
            "mysql:host=$this->serverName;port=$this->port;dbname=$this->dbname",
            $this->username,
            $this->password
            );

        if (!$this->connection) {
            throw new \Error("Database isn't working");
        }
    }

    function execQuery(BaseSelectQuery $query)
    {
        $this->connection->exec($query->render());
    }

    function openQuery(BaseSelectQuery $query)
    {
        return $this->connection->query($query->render());
    }
}
