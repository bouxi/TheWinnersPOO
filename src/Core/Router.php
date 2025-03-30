<?php

namespace App\Core;

use App\Controllers\HomeController;

class Router {
    public function handleRequest(): void {
        $path = $_SERVER['REQUEST_URI'] ?? '/';

        switch (true) {
            case $path === '/':
                $controller = new HomeController();
                $controller->index();
                break;

            case $path === '/login':
                $authController = new \App\Controllers\AuthController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $authController->login();
                } else {
                    $authController->loginForm();
                }
                break;

            case $path === '/logout':
                $authController = new \App\Controllers\AuthController();
                $authController->logout();
                break;

            case $path === '/profile':
                $profileController = new \App\Controllers\ProfileController();
                $profileController->index();
                break;

            case $path === '/profile/update':
                $profileController = new \App\Controllers\ProfileController();
                $profileController->update();
                break;

            case $path === '/admin':
                Auth::requireRole(['admin', 'guild_master']);
                $adminController = new \App\Controllers\AdminController();
                $adminController->dashboard();
                break;

            case $path === '/officer':
                Auth::requireRole(['officer', 'guild_master']);
                $officerController = new \App\Controllers\OfficerController();
                $officerController->dashboard();
                break;

            case $path === '/veteran':
                $veteranController = new \App\Controllers\VeteranController();
                $veteranController->dashboard();
                break;

            case $path === '/member':
                $memberController = new \App\Controllers\MemberController();
                $memberController->dashboard();
                break;

            case $path === '/recruit':
                $recruitController = new \App\Controllers\RecruitController();
                $recruitController->dashboard();
                break;

            case $path === '/applicant':
                $applicantController = new \App\Controllers\ApplicantController();
                $applicantController->dashboard();
                break;

            case $path === '/admin/users':
                $adminUserController = new \App\Controllers\AdminUserController();
                $adminUserController->index();
                break;

            case $path === '/admin/users/create':
                $adminUserController = new \App\Controllers\AdminUserController();
                $adminUserController->create();
                break;

            case $path === '/admin/users/store':
                $adminUserController = new \App\Controllers\AdminUserController();
                $adminUserController->store();
                break;

            case preg_match('/\/admin\/users\/delete\/(\d+)/', $path, $matches):
                $adminUserController = new \App\Controllers\AdminUserController();
                $adminUserController->delete((int)$matches[1]);
                break;

            default:
                echo "404 - Page non trouvée";
                break;
        }
    }
}
