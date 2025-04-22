<?php

namespace App\Core;

class Utils
{
    /**
     * Redirige vers un chemin relatif, en tenant compte du dossier d'installation du projet.
     *
     * @param string $relativePath Chemin relatif à partir de la racine du projet (ex: "/profile")
     */
    public static function redirect(string $relativePath): void
    {
        // S'assurer que le chemin commence bien par un "/"
        if (substr($relativePath, 0, 1) !== '/') {
            $relativePath = '/' . $relativePath;
        }

        // Récupère le chemin de base (ex: /thewinners/public)
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

        // Construit l'URL finale
        $url = $base . $relativePath;

        header("Location: $url");
        exit;
    }

    /**
     * Redirige avec un message de succès via GET (ex: ?success=...)
     */
    public static function redirectSuccess(string $relativePath, string $message): void
    {
        self::redirect($relativePath . '?success=' . urlencode($message));
    }

    /**
     * Redirige avec un message d'erreur via GET (ex: ?error=...)
     */
    public static function redirectError(string $relativePath, string $message): void
    {
        self::redirect($relativePath . '?error=' . urlencode($message));
    }
}
