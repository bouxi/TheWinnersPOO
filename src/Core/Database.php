<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    /**
     * Retourne une instance PDO connectée (singleton)
     */
    public static function getConnection(): PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        try {
            // Charge la configuration de l'environnement
            Env::load();

            $host = Env::get('DB_HOST', '127.0.0.1');
            $port = Env::get('DB_PORT', '3306');
            $name = Env::get('DB_NAME', 'thewinners');
            $user = Env::get('DB_USER', 'root');
            $pass = Env::get('DB_PASS', '');

            $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";

            self::$connection = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);

        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur de connexion à la base de données : ' . $e->getMessage());
        }

        return self::$connection;
    }

    /**
     * Réinitialise complètement la connexion PDO.
     */
    public static function reset(): void
    {
        self::$connection = null;
    }

    /**
     * Retourne true si on est en environnement de développement
     */
    public static function isDev(): bool
    {
        return Env::get('APP_ENV', 'prod') === 'dev';
    }

    /**
     * Retourne true si on est en environnement de production
     */
    public static function isProd(): bool
    {
        return Env::get('APP_ENV', 'prod') === 'prod';
    }
}
