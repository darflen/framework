<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Database;

use Darflen\Framework\Config\Config;
use Darflen\Framework\Database\DB;
use Override;
use Pdo;
use PHPUnit\Framework\TestCase;

class DBFeatureTest extends TestCase
{
    private static Config $config;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $config = new Config();
        $config->loadEnv(dirname(__DIR__, 2));
        $config->loadConfigArray('database', [
            'mariadb' => [
                'host' => '127.0.0.1',
                'port' => '3306',
                'username' => 'root',
                'password' => env('DB_PASSWORD', ''),
                'options' => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_ORACLE_NULLS => PDO::NULL_TO_STRING,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ],
            ],
        ]);
        self::$config = $config;
    }

    public function testRawQuery(): void
    {
        $database = new DB(self::$config);
        $this->assertEquals(2, $database->rawQuery("SELECT 1 + 1 AS result")->fetch()["result"]);
        $this->assertEquals(5, $database->rawQuery("SELECT 3 + 2 AS result")->fetch()["result"]);
    }

    public function testPreparedQuery(): void
    {
        $database = new DB(self::$config);
        $this->assertEquals(10, $database->preparedQuery("SELECT 5 + ? AS result", [5])->fetch()["result"]);
        $this->assertEquals(30, $database->preparedQuery("SELECT 10 + ? AS result", [20])->fetch()["result"]);
    }

    public function testTransaction(): void
    {
        $database = new DB(self::$config);

        $result = $database->transaction(function (DB $database) {
            $answer = $database->preparedQuery("SELECT 5 + ? AS result", [5])->fetch()["result"] + $database->preparedQuery("SELECT 5 + ? AS result", [5])->fetch()["result"];
            return $answer;
        });

        $this->assertSame(20, (int) $result);
    }
}
