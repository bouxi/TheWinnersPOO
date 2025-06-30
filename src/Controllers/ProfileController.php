<?php

namespace App\Controllers;

use App\Core\Security;
use App\Core\Utils;
use App\Repositories\UserRepository;
use App\Services\UploadService;
use App\Views\View;

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

        // ✏️ Récupération des champs du formulaire
        $email           = $_POST['email'] ?? '';
        $birthdate       = $_POST['birthdate'] ?? '';
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $deleteAvatar    = !empty($_POST['delete_avatar']);

        // 🔒 Vérifie que le mot de passe actuel est correct
        if (!$user->verifyPassword($currentPassword)) {
            Utils::redirectError('/profile', 'Mot de passe actuel incorrect');
        }

        // 📧 Mise à jour email et date de naissance
        $user->setEmail($email);
        $user->setBirthdate($birthdate ?: null);

        // 🔐 Si l'utilisateur souhaite modifier son mot de passe
        if (!empty($newPassword) || !empty($confirmPassword)) {
            // ✅ Vérifie la force du nouveau mot de passe
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[\\W_]).{8,}$/', $newPassword)) {
                Utils::redirectError('/profile', 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.');
            }

            // 🔁 Vérifie que les deux champs correspondent
            if ($newPassword !== $confirmPassword) {
                Utils::redirectError('/profile', 'Les mots de passe ne correspondent pas');
            }

            // 🔐 Hachage et mise à jour
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $user->setPassword($hashedPassword);
        }

        // 🖼️ Avatar : suppression
        if ($deleteAvatar && $user->getAvatar() !== 'default.png') {
            $avatarPath = __DIR__ . '/../../public/uploads/avatars/' . basename($user->getAvatar());

            if (is_file($avatarPath)) {
                unlink($avatarPath);
            }

            $user->setAvatar('default.png');
        }

        // 🖼️ Avatar : upload d’un nouveau
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

        // 💾 Mise à jour en base de données
        $userRepo = new UserRepository();
        if ($userRepo->update($user)) {
            // Mise à jour de la session avec les nouvelles infos
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
