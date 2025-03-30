<?php

namespace App\Controllers;

use App\Views\View;

class HomeController {
    public function index(): void {
        $view = new View();
        $view->render('home.html.twig', ['title' => 'Accueil']);
    }
}
