<?php

namespace App\Controllers;

use App\Core\Security;
use App\Models\UserRepository;
use App\Views\View;
use App\Core\Utils;

class ProfileController
{
    public function index(): void
    {
        Security::requireAuth();

        $user = Security::getCurrentUser();

        if (!$user) {
            echo "Utilisateur introuvable.";
            return;
        }

        $success = $_GET['success'] ?? null;
        $error = $_GET['error'] ?? null;

        $view = new View();
        $view->render('profile.html.twig', [
            'isAuthenticated' => Security::isAuthenticated(),
            'username'     => $user->getUsername(),
            'email'        => $user->getEmail(),
            'role'         => $user->getRole(),
            'isAdmin'      => $user->isAdmin(),
            'isGuildMaster'=> $user->isGuildMaster(),
            'isOfficer'    => $user->isOfficer(),
            'isVeteran'    => $user->isVeteran(),
            'isMember'     => $user->isMember(),
            'isRecruit'    => $user->isRecruit(),
            'isVisitor'    => $user->isVisitor(),
            'isApplicant'  => $user->isApplicant(),
            'success'      => $success,
            'error'        => $error
        ]);
    }

    public function update(): void
    {
        Security::requireAuth();

        $user = Security::getCurrentUser();

        if (!$user) {
            Utils::redirectError('/profile', 'Utilisateur introuvable');
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user->setEmail($email);

        if (!empty($password)) {
            $user->setPassword($password);
        }

        $userRepo = new UserRepository();

        if ($userRepo->update($user)) {
            Utils::redirectSuccess('/profile', 'Mise à jour réussie');
        } else {
            Utils::redirectError('/profile', 'Erreur lors de la mise à jour');
        }
    }
}
