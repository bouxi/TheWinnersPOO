<?php

namespace App\Controllers;

use App\Models\UserRepository;
use App\Models\User;
use App\Views\View;
use App\Core\Security;
use App\Core\Utils;
use App\Services\UploadService;

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
            $_SESSION['user'] = [
                'id'       => $user->getId(),
                'username' => $user->getUsername(),
                'role'     => $user->getRole()
            ];

            Utils::redirect('/profile');
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
        Utils::redirect('/login');
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
        $birthdate = $_POST['birthdate'] ?? null;

        $view = new View();

        if (empty($username) || empty($email) || empty($password) || empty($passwordConfirm)) {
            $view->render('register.html.twig', ['error' => 'Tous les champs obligatoires doivent être remplis.']);
            return;
        }

        if ($password !== $passwordConfirm) {
            $view->render('register.html.twig', ['error' => 'Les mots de passe ne correspondent pas.']);
            return;
        }

        $userRepo = new UserRepository();

        if ($userRepo->findByUsername($username) || $userRepo->findByEmail($email)) {
            $view->render('register.html.twig', ['error' => 'Nom d\'utilisateur ou email déjà utilisé.']);
            return;
        }

        $avatarFilename = UploadService::uploadImage(
            $_FILES['avatar'],
            __DIR__ . '/../../public/uploads/avatars/'
        );

        if ($_FILES['avatar']['name'] && !$avatarFilename) {
            $view->render('register.html.twig', ['error' => 'Fichier invalide (format ou taille max 2 Mo)']);
            return;
        }

        $user = new User($username, $email, $password, 'member');
        $user->setBirthdate($birthdate ?: null);
        $user->setAvatar($avatarFilename);

        $userRepo->save($user);

        Utils::redirectSuccess('/login', 'Inscription réussie ! Vous pouvez vous connecter.');
    }
}
