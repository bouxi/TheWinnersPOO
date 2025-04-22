<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Views\View;

class VeteranController {
    public function dashboard(): void {
        Auth::requireRole(['veteran', 'guild_master']);
        $view = new View();
        $view->render('veteran.html.twig');
    }
}
