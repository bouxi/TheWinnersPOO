<?php

namespace App\Controllers;

use App\Core\Security;
use App\Views\View;

class NotAvailableController
{
    // 🔧 Affiche la page "fonctionnalité non disponible"
    public function index(): void
    {
        Security::requireAuth();
        $user = Security::getCurrentUser();

        $view = new View();
        $view->render('tips/pasdispo.html.twig', [
            'user' => $user
        ]);
    }
}
