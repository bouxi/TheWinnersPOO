<?php

namespace App\Core;

class Database {
    private static ?\PDO $connection = null;

    public static function getConnection(): \PDO {
        if (self::$connection === null) {
            $dotenv = parse_ini_file(__DIR__ . '/../../.env');
            $dsn = "mysql:host={$dotenv['DB_HOST']};dbname={$dotenv['DB_NAME']};charset=utf8";
            self::$connection = new \PDO($dsn, $dotenv['DB_USER'], $dotenv['DB_PASS']);
            self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return self::$connection;
    }
}
