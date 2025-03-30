<?php

namespace App\Core;

class Bootstrap {
    public static function start(): void {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
