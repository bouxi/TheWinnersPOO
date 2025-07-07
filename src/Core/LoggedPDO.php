<?php
namespace App\Core;

use App\Debug\Debug;
use PDO;
use PDOStatement;

class LoggedPDO extends PDO
{
    public function query(string $statement, ?int $mode = PDO::ATTR_DEFAULT_FETCH_MODE, ...$fetch_mode_args): PDOStatement|false
    {
        Debug::addQuery($statement);
        return parent::query($statement, $mode, ...$fetch_mode_args);
    }

    public function prepare(string $statement, array $options = []): PDOStatement|false
    {
        Debug::addQuery($statement);
        return parent::prepare($statement, $options);
    }

    public function exec(string $statement): int|false
    {
        Debug::addQuery($statement);
        return parent::exec($statement);
    }
}
