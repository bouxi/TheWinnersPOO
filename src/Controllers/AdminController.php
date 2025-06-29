<?php

namespace App\Controllers;

use App\Core\Security;
use App\Repositories\UserRepository;
use App\Repositories\MessageRepository;
use App\Views\View;
use App\Core\Env;

class AdminController {

    public function dashboard(): void {
        Security::requireRole(['admin', 'guild_master']);

        $userRepo = new UserRepository();
        $messageRepo = new MessageRepository();

        $totalUsers = $userRepo->getTotalUsers();
        $rolesDistribution = $userRepo->getUsersByRole();
        $latestUsers = $userRepo->getLatestUsers(5);
        $unreadMessages = $messageRepo->countAllUnreadMessages();

        (new View())->render('admin/dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'rolesDistribution' => $rolesDistribution,
            'latestUsers' => $latestUsers,
            'unreadMessages' => $unreadMessages,
            'app_env' => Env::get('APP_ENV', 'unknown')
        ]);
    }
}
