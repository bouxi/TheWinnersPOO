<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Views\View;

class ApplicantController {
    public function dashboard(): void {
        Auth::requireRole(['applicant']);
        $view = new View();
        $view->render('applicant.html.twig');
    }
}
