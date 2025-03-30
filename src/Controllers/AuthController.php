<?php

namespace App\Controllers;

use App\Models\UserRepository;
use App\Models\User;
use App\Views\View;

class AuthController {

    public function loginForm(): void {
        $view = new View();
        $view->render('login.html.twig');
    }

    public function login(): void {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $userRepo = new UserRepository();
        $user = $userRepo->findByUsername($username);

        if ($user && $user->verifyPassword($password)) {
            //session_start();
            $_SESSION['user'] = $user->getUsername();
            $_SESSION['role'] = $user->getRole();
            // Redirection vers le profil
            header('Location: /profile');
            exit;
        } else {
            echo "Identifiants incorrects.";
        }
    }


    public function logout(): void {
        session_start();
        session_destroy();
        header('Location: /login');
        exit;
    }
}
