<?php

namespace App\Controllers;

use App\Core\Twig;

class HomeController {
    public function index(): void {
        // Vérifie que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifie si un utilisateur est connecté
        $isAuthenticated = isset($_SESSION['user']);
        $user = $isAuthenticated ? $_SESSION['user'] : null;

        // Si connecté, récupère son nom d'utilisateur via la méthode getUsername()
        $username = $isAuthenticated ? $user->getUsername() : 'Invité';

        // Rendu de la vue avec les données nécessaires
        $twig = Twig::getInstance();
        echo $twig->render('home.html.twig', [
            'isAuthenticated' => $isAuthenticated,
            'user' => $user,
            'username' => $username, // optionnel si utilisé dans le template
        ]);
    }
}
