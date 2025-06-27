<?php

namespace App\Core;

class App
{
    private static array $config = [];

    /**
     * Charge la configuration depuis config.php (une seule fois)
     */
    public static function loadConfig(): void
    {
        if (!self::$config) {
            self::$config = require __DIR__ . '/../config.php';
        }
    }

    /**
     * Récupère une clé de configuration depuis config.php
     */
    public static function get(string $key): mixed
    {
        self::loadConfig();
        return self::$config[$key] ?? null;
    }

    /**
     * Récupère le nom de l'application défini dans config.php
     */
    public static function getAppName(): string
    {
        return self::get('APP_NAME') ?? 'MonApp';
    }

    /**
     * Détecte si l'application est en mode développement
     * (en lisant APP_ENV dans config.php)
     */
    public static function isDevMode(): bool
    {
        return self::get('APP_ENV') === 'dev';
    }

    /**
     * Récupère le chemin de base dynamique de l'application
     * Exemple : "/thewinners/public" ou "/"
     */
    public static function getBasePath(): string
    {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = str_replace('/index.php', '', $scriptName);
        return rtrim($basePath, '/');
    }

    /**
     * Retourne la configuration PDO prête à être utilisée
     */
    public static function getDbDsn(): string
    {
        $db = self::get('DB');
        return sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $db['host'],
            $db['port'],
            $db['dbname'],
            $db['charset']
        );
    }

    /**
     * Retourne le nom d’utilisateur DB
     */
    public static function getDbUser(): string
    {
        return self::get('DB')['user'];
    }

    /**
     * Retourne le mot de passe DB
     */
    public static function getDbPassword(): string
    {
        return self::get('DB')['password'];
    }

}
