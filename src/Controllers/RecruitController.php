<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Views\View;

class RecruitController {
    public function dashboard(): void {
        Auth::requireRole(['recruit']);
        $view = new View();
        $view->render('recruit.html.twig');
    }
}
