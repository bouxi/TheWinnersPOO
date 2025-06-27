<?php

namespace App\Controllers;

use App\Core\Security;
use App\Core\Utils;
use App\Models\UserRepository;
use App\Services\UploadService;
use App\Views\View;
use App\Models\User;
use App\Core\App;

class ProfileController
{
    /**
     * Affiche la page de profil avec les infos utilisateur
     */
    public function index(): void
    {
        Security::requireAuth();

        $user = Security::getCurrentUser();

        if (!$user) {
            Utils::redirectError('/profile', 'Utilisateur introuvable');
        }


        $view = new View();
        $view->render('profile.html.twig', [
            'isAuthenticated' => true,
            'user' => $user,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null,
        ]);

    }

    /**
     * Met à jour les infos utilisateur (email, mdp, avatar...)
     */
    public function update(): void
    {
        Security::requireAuth();
        $user = Security::getCurrentUser();

        if (!$user) {
            Utils::redirectError('/profile', 'Utilisateur introuvable');
        }

        $email           = $_POST['email'] ?? '';
        $password        = $_POST['password'] ?? '';
        $birthdate       = $_POST['birthdate'] ?? '';
        $currentPassword = $_POST['current_password'] ?? '';
        $deleteAvatar    = !empty($_POST['delete_avatar']);

        // Vérifie que le mot de passe actuel est correct
        if (!$user->verifyPassword($currentPassword)) {
            Utils::redirectError('/profile', 'Mot de passe actuel incorrect');
        }

        $user->setEmail($email);
        $user->setBirthdate($birthdate ?: null);

        if (!empty($password)) {
            $user->setPassword($password);
        }

        // Avatar : suppression
        if ($deleteAvatar && $user->getAvatar() !== 'default.png') {
            $avatarPath = __DIR__ . '/../../public/uploads/avatars/' . basename($user->getAvatar());

            if (is_file($avatarPath)) {
                unlink($avatarPath);
            }

            $user->setAvatar('default.png');
        }

        // Avatar : upload
        if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['name'])) {
            $oldAvatarPath = $user->getAvatar()
                ? __DIR__ . '/../../public/uploads/avatars/' . $user->getAvatar()
                : null;

            $newAvatar = UploadService::uploadImage(
                $_FILES['avatar'],
                __DIR__ . '/../../public/uploads/avatars/',
                $oldAvatarPath
            );

            if (!$newAvatar) {
                Utils::redirectError('/profile', 'Fichier avatar invalide');
            }

            $user->setAvatar($newAvatar);
        }

        // Mise à jour en base de données
        $userRepo = new UserRepository();
        if ($userRepo->update($user)) {
            // Mise à jour de la session avec toArray()
            $_SESSION['user'] = $user->toArray();

            Utils::redirectSuccess('/profile', 'Profil mis à jour avec succès');
        } else {
            Utils::redirectError('/profile', 'Erreur lors de la mise à jour');
        }
    }

    /**
     * Supprime uniquement l’avatar de l’utilisateur
     */
    public function removeAvatar(): void
    {
        Security::requireAuth();
        $user = Security::getCurrentUser();

        if (!$user) {
            Utils::redirectError('/profile', 'Utilisateur introuvable');
        }

        $filename = basename($user->getAvatar());

        if ($filename && $filename !== 'default.png') {
            $path = __DIR__ . '/../../public/uploads/avatars/' . $filename;

            if (file_exists($path) && is_file($path)) {
                unlink($path);
            }

            $user->setAvatar('default.png');
            (new UserRepository())->update($user);

            // Met à jour la session après suppression
            $_SESSION['user'] = $user->toArray();
        }

        Utils::redirectSuccess('/profile', 'Avatar supprimé avec succès');
    }
}
