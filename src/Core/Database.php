<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    /**
     * Retourne une instance PDO connectée, en singleton
     */
    public static function getConnection(): PDO
    {
        // Si déjà connectée, retourne l'existante
        if (self::$connection !== null) {
            return self::$connection;
        }

        try {
            // Utilise les méthodes de App pour charger la config depuis config.php
            $dsn = App::getDbDsn();         // mysql:host=...;port=...;dbname=...;charset=...
            $user = App::getDbUser();       // utilisateur DB
            $pass = App::getDbPassword();   // mot de passe DB

            // Création de l'objet PDO
            self::$connection = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);

        } catch (PDOException $e) {
            // En cas d'erreur, on jette une exception claire
            throw new \RuntimeException('Erreur de connexion à la base de données : ' . $e->getMessage());
        }

        return self::$connection;
    }

    /**
     * Réinitialise complètement la connexion PDO.
     * À utiliser avec précaution.
     */
    public static function reset(): void
    {
        self::$connection = null;
    }

}
