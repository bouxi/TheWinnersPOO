<?php

namespace App\Core;

class Auth {
    public static function check(): bool {
        //session_start();
        return isset($_SESSION['user']);
    }

    public static function requireAuth(): void {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }
    }
}
