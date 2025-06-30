<?php

namespace App\Controllers;

use App\Views\View;

class NotAvailableController
{
    // 🔧 Affiche la page "fonctionnalité non disponible"
    public function index(): void
    {
        $view = new View();
        $view->render('pasdispo.html.twig');
    }
}
