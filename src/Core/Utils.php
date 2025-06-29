<?php

namespace App\Core;

class Utils
{
    /**
     * Redirige vers une URL interne (ex: 'profile' ou '/profile')
     */
    public static function redirect(string $relativePath): void
    {
        // Force le slash en début
        if (substr($relativePath, 0, 1) !== '/') {
            $relativePath = '/' . $relativePath;
        }

        // Utilise le basePath pour compatibilité avec /public ou sous-dossiers
        $url = App::getBasePath() . $relativePath;

        header("Location: $url");
        exit;
    }

    /**
     * Redirige vers une URL en ajoutant un message flash "success"
     */
    public static function redirectSuccess(string $url, string $message): void
    {
        // Force le slash devant l’URL
        if (substr($url, 0, 1) !== '/') {
            $url = '/' . $url;
        }

        // Préfixe avec base path si nécessaire
        $finalUrl = App::getBasePath() . $url . '?success=' . urlencode($message);

        header("Location: $finalUrl");
        exit;
    }

    /**
     * Redirige vers une URL en ajoutant un message flash "error"
     */
    public static function redirectError(string $url, string $message): void
    {
        if (substr($url, 0, 1) !== '/') {
            $url = '/' . $url;
        }

        $finalUrl = App::getBasePath() . $url . '?error=' . urlencode($message);

        header("Location: $finalUrl");
        exit;
    }

    public static function flashSuccess(string $message): void {
        $_SESSION['flash_success'] = $message;
    }

    public static function flashError(string $message): void {
        $_SESSION['flash_error'] = $message;
    }

    public static function getFlashSuccess(): ?string {
        if (!isset($_SESSION['flash_success'])) return null;
        $msg = $_SESSION['flash_success'];
        unset($_SESSION['flash_success']);
        return $msg;
    }

    public static function getFlashError(): ?string {
        if (!isset($_SESSION['flash_error'])) return null;
        $msg = $_SESSION['flash_error'];
        unset($_SESSION['flash_error']);
        return $msg;
    }
}
