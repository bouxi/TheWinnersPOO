<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Views\View;

class ProfileController {

    public function index(): void {
        // Vérification de l'authentification
        Auth::requireAuth();

        // Récupération des informations de l'utilisateur depuis la session
        $username = $_SESSION['user'];

        // Passer les informations à la vue
        $view = new View();
        $view->render('profile.html.twig', ['username' => $username]);
    }
}
