<?php

namespace App\Core;

class Controller {
    protected function requireAuth(): void {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }
    }

    protected function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
