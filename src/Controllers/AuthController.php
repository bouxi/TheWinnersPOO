<?php

namespace App\Controllers;

use App\Models\UserRepository;
use App\Models\User;
use App\Views\View;
use App\Core\Security;

class AuthController
{
    public function loginForm(): void
    {
        $view = new View();
        $view->render('login.html.twig');
    }

    public function login(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $userRepo = new UserRepository();
        $user = $userRepo->findByUsername($username);

        if ($user && $user->verifyPassword($password)) {
            // Stocker uniquement les infos nécessaires dans la session
            $_SESSION['user'] = [
                'id'       => $user->getId(),
                'username' => $user->getUsername(),
                'role'     => $user->getRole()
            ];

            header('Location: /profile');
            exit;
        } else {
            $view = new View();
            $view->render('login.html.twig', [
                'error' => 'Identifiants incorrects.'
            ]);
        }
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /login');
        exit;
    }

    public function registerForm(): void
    {
        $view = new View();
        $view->render('register.html.twig');
    }

    public function register(): void
    {
        $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if (empty($username) || empty($email) || empty($password) || empty($passwordConfirm)) {
            $this->renderRegisterError('Tous les champs sont requis.');
            return;
        }

        if ($password !== $passwordConfirm) {
            $this->renderRegisterError('Les mots de passe ne correspondent pas.');
            return;
        }

        $userRepo = new UserRepository();

        if ($userRepo->findByUsername($username) || $userRepo->findByEmail($email)) {
            $this->renderRegisterError('Nom d\'utilisateur ou email déjà utilisé.');
            return;
        }

        $user = new User($username, $email, $password, 'member');
        $userRepo->save($user);

        header('Location: /login');
        exit;
    }

    private function renderRegisterError(string $message): void
    {
        $view = new View();
        $view->render('register.html.twig', [
            'error' => $message
        ]);
    }
}
