<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\UserRepository;
use App\Views\View;

class ProfileController {

    public function index(): void {
        Auth::requireAuth();
        $username = $_SESSION['user'];

        // Récupérer les informations complètes de l'utilisateur
        $userRepo = new UserRepository();
        $user = $userRepo->findByUsername($username);

        $view = new View();
        $view->render('profile.html.twig', [
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()
        ]);
    }

    public function update(): void {
        Auth::requireAuth();
        $username = $_SESSION['user'];

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $userRepo = new UserRepository();
        $user = $userRepo->findByUsername($username);

        if ($user) {
            $user->setEmail($email);

            if (!empty($password)) {
                $user->setPassword($password);
            }

            if ($userRepo->update($user)) {
                header('Location: /profile');
                exit;
            } else {
                echo "Erreur lors de la mise à jour.";
            }
        }
    }
}
