<?php

namespace App\Controllers;

use App\Core\Security;
use App\Core\Utils;
use App\Models\UserRepository;
use App\Services\UploadService;
use App\Views\View;

class ProfileController
{
    /**
     * Affiche la page de profil avec les informations de l'utilisateur connecté
     */
    public function index(): void
    {
        // Vérifie que l'utilisateur est connecté
        Security::requireAuth();

        // Récupère l'utilisateur courant depuis la session
        $user = Security::getCurrentUser();

        // Si aucun utilisateur n'est trouvé (bug de session par exemple), redirige avec erreur
        if (!$user) {
            Utils::redirectError('/profile', 'Utilisateur introuvable');
        }

        // Affiche la vue avec l'objet complet `user` pour Twig
        $view = new View();
        $view->render('profile.html.twig', [
            'isAuthenticated' => Security::isAuthenticated(),
            'user' => $user,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ]);
    }

    /**
     * Met à jour les informations du profil utilisateur
     */
    public function update(): void
    {
        // Vérifie que l'utilisateur est connecté
        Security::requireAuth();

        // Récupère l'utilisateur actuel
        $user = Security::getCurrentUser();

        if (!$user) {
            Utils::redirectError('/profile', 'Utilisateur introuvable');
        }

        // Récupère les champs du formulaire
        $email           = $_POST['email'] ?? '';
        $password        = $_POST['password'] ?? ''; // nouveau mot de passe (facultatif)
        $birthdate       = $_POST['birthdate'] ?? '';
        $currentPassword = $_POST['current_password'] ?? ''; // requis
        $deleteAvatar    = !empty($_POST['delete_avatar']); // case cochée pour supprimer l'avatar

        // Vérifie que le mot de passe actuel est correct
        if (!$user->verifyPassword($currentPassword)) {
            Utils::redirectError('/profile', 'Mot de passe actuel incorrect');
        }

        // Mise à jour des informations utilisateur
        $user->setEmail($email);
        $user->setBirthdate($birthdate ?: null); // vide = null

        // Si un nouveau mot de passe est défini, on le met à jour (haché dans User::setPassword)
        if (!empty($password)) {
            $user->setPassword($password);
        }

        // Si un nouveau fichier avatar est fourni dans le formulaire
        if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['name'])) {
            // Définit le chemin de l'ancien avatar (à supprimer si remplacé)
            $oldAvatarPath = $user->getAvatar()
                ? __DIR__ . '/../../public/uploads/avatars/' . $user->getAvatar()
                : null;

            // Utilise le service d'upload pour traiter le nouveau fichier
            $newAvatar = UploadService::uploadImage(
                $_FILES['avatar'],
                __DIR__ . '/../../public/uploads/avatars/',
                $oldAvatarPath // sera supprimé automatiquement si nouveau OK
            );

            // Si l'upload échoue (format, extension ou taille), on arrête tout
            if (!$newAvatar) {
                Utils::redirectError('/profile', 'Fichier avatar invalide (format, taille ou type incorrect)');
            }

            $user->setAvatar($newAvatar);
        }

        // Si la case "supprimer avatar" est cochée
        if ($deleteAvatar && $user->getAvatar() && $user->getAvatar() !== 'default.png') {
            $avatarPath = __DIR__ . '/../../public/uploads/avatars/' . basename($user->getAvatar());

            // Supprime le fichier physique
            if (file_exists($avatarPath)) {
                unlink($avatarPath);
            }

            // Remplace par l'avatar par défaut
            $user->setAvatar('default.png');
        }

        // Mise à jour en base de données
        $userRepo = new UserRepository();
        if ($userRepo->update($user)) {
            $_SESSION['user'] = $user;
            Utils::redirectSuccess('/profile', 'Profil mis à jour avec succès');
        } else {
            Utils::redirectError('/profile', 'Erreur lors de la mise à jour');
        }
    }

    /**
     * Supprime uniquement l'avatar de l'utilisateur
     */
    public function removeAvatar(): void
    {
        // Vérifie que l'utilisateur est connecté
        Security::requireAuth();

        // Récupère l'utilisateur courant
        $user = Security::getCurrentUser();

        if (!$user) {
            Utils::redirectError('/profile', 'Utilisateur introuvable');
        }

        // Récupère le nom du fichier avatar
        $filename = basename($user->getAvatar());

        // Supprime le fichier si ce n’est pas l'avatar par défaut
        if ($filename && $filename !== 'default.png') {
            $path = __DIR__ . '/../../public/uploads/avatars/' . $filename;

            if (file_exists($path) && is_file($path)) {
                unlink($path);
            }

            // Remplace par l'avatar par défaut
            $user->setAvatar('default.png');
            (new UserRepository())->update($user);
        }

        // Redirection avec message de confirmation
        Utils::redirectSuccess('/profile', 'Avatar supprimé avec succès');
    }
}
