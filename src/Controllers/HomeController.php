<?php

namespace App\Controllers;

use App\Core\Twig;

class HomeController {
    public function index(): void {
        // Vérifier si la session est bien démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier si l'utilisateur est connecté
        //$username = $_SESSION['user'] ?? 'Invité';

        $isAuthenticated = isset($_SESSION['user']);
        $username = $isAuthenticated ? $_SESSION['user']['username'] : null;

        // Obtenir l'instance Twig et rendre la page d'accueil
        $twig = Twig::getInstance();
        echo $twig->render('home.html.twig', [
            'isAuthenticated' => $isAuthenticated,
            'username' => $username
        ]);
    }
}
