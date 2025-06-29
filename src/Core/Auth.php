<?php

namespace App\Core;

class Auth {
    // Et une méthode check() qui valide si l'utilisateur est connecté
    public static function check(): bool {
        return isset($_SESSION['user']);
    }

    public static function requireAuth(): void {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }
    }

    public static function requireRole(array $roles): void {
        if (!self::check() || !isset($_SESSION['user']['role']) || !in_array($_SESSION['user']['role'], $roles)) {
            header('Location: /');
            exit;
        }
    }

}
