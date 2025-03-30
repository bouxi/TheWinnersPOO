<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Views\View;

class AdminController {
    public function dashboard(): void {
        Auth::requireRole(['admin', 'guild_master']);
        $view = new View();
        $view->render('admin.html.twig');
    }
}
