<?php

namespace App\Controllers;

use App\Models\UserRepository;
use App\Models\User;
use App\Views\View;
use App\Core\Auth;

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
        //session_start();
        session_destroy();
        header('Location: /login');
        exit;
    }

    public function registerForm(): void {
        $view = new View();
        $view->render('register.html.twig');
    }

    public function register(): void {
        // Démarrer le tampon de sortie pour éviter les erreurs d'en-tête
        ob_start();

        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $passwordConfirm = $_POST['password_confirm'];

        // Vérification des champs
        if (empty($username) || empty($email) || empty($password) || empty($passwordConfirm)) {
            $view = new View();
            $view->render('register.html.twig', ['error' => 'Tous les champs sont requis.']);
            ob_end_flush(); // Libérer le tampon avant de quitter
            return;
        }

        // Vérification des mots de passe
        if ($password !== $passwordConfirm) {
            $view = new View();
            $view->render('register.html.twig', ['error' => 'Les mots de passe ne correspondent pas.']);
            ob_end_flush(); // Libérer le tampon avant de quitter
            return;
        }

        $userRepo = new UserRepository();

        // Vérifier si l'utilisateur ou l'email existe déjà
        if ($userRepo->findByUsername($username) || $userRepo->findByEmail($email)) {
            $view = new View();
            $view->render('register.html.twig', ['error' => 'Nom d\'utilisateur ou email déjà utilisé.']);
            ob_end_flush(); // Libérer le tampon avant de quitter
            return;
        }

        // Créer l'utilisateur
        $user = new User($username, $email, $password, 'member');
        $userRepo->save($user);

        // Redirection vers la page de connexion
        header('Location: /login');
        ob_end_flush(); // Libérer le tampon après la redirection
        exit;
    }
}
