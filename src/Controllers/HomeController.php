<?php

namespace App\Controllers;

use App\Core\Security;
use App\Views\View;

class HomeController
{
    public function index(): void
    {
        // Récupère l'utilisateur en tant qu'objet via Security
        $user = Security::getCurrentUser();
        $isAuthenticated = $user !== null;

        $view = new View();
        $view->render('home.html.twig', [
            'isAuthenticated' => $isAuthenticated,
            'user' => $user,
        ]);
    }
}

