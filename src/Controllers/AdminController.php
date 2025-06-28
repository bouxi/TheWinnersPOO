<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Repositories\UserRepository;
use App\Views\View;

class AdminController {
    public function dashboard(): void {
        Auth::requireRole(['admin', 'guild_master']);

        $userRepo = new UserRepository();
        $totalUsers = $userRepo->getTotalUsers();
        $rolesDistribution = $userRepo->getUsersByRole();

        $view = new View();
        $view->render('admin/dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'rolesDistribution' => $rolesDistribution
        ]);
    }
}
