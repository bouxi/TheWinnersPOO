<?php

namespace App\Core;

use App\Models\User;
use App\Repositories\UserRepository;

class Security
{
    /**
     * Vérifie si un utilisateur est connecté
     */
    public static function isAuthenticated(): bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * Redirige vers /login si l'utilisateur n’est pas connecté
     */
    public static function requireAuth(): void
    {
        if (!self::isAuthenticated()) {
            Utils::redirect('/login');
        }
    }

    /**
     * Récupère l'utilisateur actuellement connecté (depuis la base)
     */
    public static function getCurrentUser(): ?User
    {
        // Vérifie que l'utilisateur est en session
        if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
            return null;
        }

        // Récupère l'ID utilisateur depuis la session
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            return null;
        }

        // Recharge l'utilisateur complet depuis la base
        $repo = new UserRepository();
        return $repo->findById($userId); // retourne un User ou null
    }

    /**
     * Déconnecte proprement l'utilisateur
     */
    public static function logout(): void
    {
        unset($_SESSION['user']);
        session_destroy();
    }
}
