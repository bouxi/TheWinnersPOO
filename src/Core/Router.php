<?php

namespace App\Core;

use App\Controllers\HomeController;

class Router {
    public function handleRequest() {
        $path = $_SERVER['REQUEST_URI'];

        switch ($path) {
            case '/':
                $controller = new HomeController();
                $controller->index();
                break;

            case '/login':
                $authController = new \App\Controllers\AuthController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $authController->login();
                } else {
                    $authController->loginForm();
                }
                break;

            case '/logout':
                $authController = new \App\Controllers\AuthController();
                $authController->logout();
                break;
            case '/profile':
                $profileController = new \App\Controllers\ProfileController();
                $profileController->index();
                break;

            default:
                echo "404 - Page non trouvée";
                break;
        }
    }
}
