<?php

declare(strict_types=1);

namespace Darflen\Framework\Database;

use Darflen\Framework\Config\Config;
use PDO;
use PDOStatement;

class DB
{
    private PDO $pdo;

    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getInstance(): PDO
    {
        if (isset($this->pdo)) {
            return $this->pdo;
        }
        $this->pdo = new PDO(
            'mysql:host=' . $this->config->get('database.mariadb.host')
                . ';dbname=' . $this->config->get('database.mariadb.database')
                . ';port=' . $this->config->get('database.mariadb.port'),
            $this->config->get('database.mariadb.username'),
            $this->config->get('database.mariadb.password'),
            $this->config->get('database.mariadb.options') ?? [],
        );
        return $this->pdo;
    }

    public function rawQuery(string $query): PDOStatement
    {
        $query = $this->getInstance()->query($query);
        return $query;
    }

    public function preparedQuery(string $query, array $parameters = []): PDOStatement
    {
        $query = $this->getInstance()->prepare($query);
        $query->execute($parameters);
        return $query;
    }

    public function transaction(callable $callback): mixed
    {
        $query = $this->getInstance();
        $query->beginTransaction();
        try {
            $result = $callback($this);
            $query->commit();
            return $result;
        } catch (\Throwable $throwable) {
            $query->rollBack();
            throw $throwable;
        }
    }
}
