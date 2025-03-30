<?php

namespace App\Controllers;

use App\Views\View;

class HomeController {
    public function index(): void {
        $view = new View();
        // Vérifie si l'utilisateur est connecté
        $username = $_SESSION['user'] ?? null;
        $view->render('home.html.twig', ['username' => $username]);
    }
}
