<?php

declare(strict_types=1);

namespace Darflen\Framework\DB;

use PDO;
use PDOStatement;

class DB
{
    private PDO $connect;

    private function connect(): PDO
    {
        if (isset($this->connect)) {
            return $this->connect;
        }
        $this->connect = new PDO(
            'mysql:host=' . config('database.mariadb.host') .
                ';dbname=' . config('database.mariadb.database') .
                ';port=' . config('database.mariadb.port'),
            config('database.mariadb.username'),
            config('database.mariadb.password'),
            config('database.mariadb.options')
        );
        return $this->connect;
    }

    public function raw(string $query): PDOStatement
    {
        $query = $this->connect()->query($query);
        return $query;
    }

    public function query(string $query, array $parameters = []): PDOStatement
    {
        $query = $this->connect()->prepare($query);
        $query->execute($parameters);
        return $query;
    }
}
