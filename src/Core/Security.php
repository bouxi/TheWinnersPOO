<?php

namespace App\Core;

use App\Models\User;
use App\Models\UserRepository;

class Security
{
    public static function isAuthenticated(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function requireAuth(): void
    {
        if (!self::isAuthenticated()) {
            header('Location: /login');
            exit;
        }
    }

    public static function getCurrentUser(): ?User
    {
        if (!self::isAuthenticated()) {
            return null;
        }

        // Récupère uniquement le username de la session
        $username = $_SESSION['user']['username'] ?? null;

        if ($username === null) {
            return null;
        }

        // Recharge l'objet User depuis la base
        $userRepo = new UserRepository();
        return $userRepo->findByUsername($username);
    }

    public static function getCurrentUserId(): ?int
    {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function getCurrentUsername(): ?string
    {
        return $_SESSION['user']['username'] ?? null;
    }

    public static function getCurrentUserRole(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }
}
