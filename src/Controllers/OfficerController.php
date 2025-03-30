<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Views\View;

class OfficerController {
    public function dashboard(): void {
        Auth::requireRole(['officer', 'guild_master']);
        $view = new View();
        $view->render('officer.html.twig');
    }
}
