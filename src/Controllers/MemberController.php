<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Views\View;

class MemberController {
    public function dashboard(): void {
        Auth::requireRole(['member', 'veteran', 'officer', 'guild_master']);
        $view = new View();
        $view->render('member.html.twig');
    }
}
