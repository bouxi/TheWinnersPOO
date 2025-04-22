<?php

namespace App\Core;

class Database {
    private static ?\PDO $connection = null;

    public static function getConnection(): \PDO {
        if (self::$connection === null) {
            $dotenv = parse_ini_file(__DIR__ . '/../../.env');

            // Récupération des informations de connexion
            $host = $dotenv['DB_HOST'] ?? 'localhost';
            $port = $dotenv['DB_PORT'] ?? '3306'; // Ajout du port par défaut 3306
            $dbname = $dotenv['DB_NAME'] ?? 'thewinners';
            $user = $dotenv['DB_USER'] ?? 'root';
            $pass = $dotenv['DB_PASS'] ?? '';

            try {
                // Utilisation du port dans la chaîne de connexion
                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8";
                self::$connection = new \PDO($dsn, $user, $pass);
                self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
