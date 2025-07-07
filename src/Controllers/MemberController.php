<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Security;
use App\Views\View;

class MemberController {
    public function index(): void {
        Auth::requireRole(['member', 'veteran', 'officer', 'guild_master']);
        $user = Security::getCurrentUser();

        $view = new View();
        $view->render('guild/members.html.twig', [
            'user' => $user,
        ]);
    }
}
