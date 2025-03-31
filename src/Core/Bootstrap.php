<?php

namespace App\Core;

class Bootstrap {
    public static function start(): void {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 3600,   // Durée de la session en secondes (1 heure)
                'path' => '/',
                'domain' => '',       // Domaine vide pour autoriser localhost
                'secure' => false,    // Mettre true si HTTPS
                'httponly' => true,   // Sécuriser l'accès aux cookies par HTTP seulement
                'samesite' => 'Lax'   // Empêcher les fuites de session via les liens externes
            ]);
            session_start();
        }
    }
}
