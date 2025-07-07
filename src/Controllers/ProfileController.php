<?php

namespace App\Controllers;

use App\Core\Security;
use App\Core\Utils;
use App\Repositories\UserRepository;
use App\Repositories\ApplicationRepository;
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
            Utils::redirectError('/user/profile', 'Utilisateur introuvable');
        }

        // 🔍 Vérifie si le user a déjà rejoint la guilde
        $appRepo = new ApplicationRepository();
        $hasJoined = $appRepo->hasJoinedGuild($user->getId());

        $view = new View();
        $view->render('user/profile.html.twig', [ // ✅ Chemin corrigé ici
            'isAuthenticated' => true,
            'user' => $user,
            'has_joined_guild' => $hasJoined,
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
            Utils::redirectError('/user/profile', 'Utilisateur introuvable');
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
            Utils::redirectError('/user/profile', 'Mot de passe actuel incorrect');
        }

        // 📧 Mise à jour email et date de naissance
        $user->setEmail($email);
        $user->setBirthdate($birthdate ?: null);

        // 🔐 Mise à jour du mot de passe si demandé
        if (!empty($newPassword) || !empty($confirmPassword)) {
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[\\W_]).{8,}$/', $newPassword)) {
                Utils::redirectError('/user/profile', 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.');
            }

            if ($newPassword !== $confirmPassword) {
                Utils::redirectError('/user/profile', 'Les mots de passe ne correspondent pas');
            }

            $user->setPassword(password_hash($newPassword, PASSWORD_DEFAULT));
        }

        // 🖼️ Suppression avatar
        if ($deleteAvatar && $user->getAvatar() !== 'default-avatar.png') {
            $avatarPath = __DIR__ . '/../../public/uploads/avatars/' . basename($user->getAvatar());
            if (is_file($avatarPath)) {
                unlink($avatarPath);
            }
            $user->setAvatar('default-avatar.png');
        }

        // 🖼️ Upload nouvel avatar
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
                Utils::redirectError('/user/profile', 'Fichier avatar invalide');
            }

            $user->setAvatar($newAvatar);
        }

        // 💾 Enregistrement
        $userRepo = new UserRepository();
        if ($userRepo->update($user)) {
            $_SESSION['user'] = $user->toArray();
            Utils::redirectSuccess('/user/profile', 'Profil mis à jour avec succès');
        } else {
            Utils::redirectError('/user/profile', 'Erreur lors de la mise à jour');
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
            Utils::redirectError('/user/profile', 'Utilisateur introuvable');
        }

        $filename = basename($user->getAvatar());

        if ($filename && $filename !== 'default-avatar.png') {
            $path = __DIR__ . '/../../public/uploads/avatars/' . $filename;

            if (file_exists($path) && is_file($path)) {
                unlink($path);
            }

            $user->setAvatar('default-avatar.png');
            (new UserRepository())->update($user);
            $_SESSION['user'] = $user->toArray();
        }

        Utils::redirectSuccess('/user/profile', 'Avatar supprimé avec succès');
    }
}
