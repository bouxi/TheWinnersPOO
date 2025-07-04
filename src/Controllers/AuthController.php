<?php

namespace App\Controllers;

use App\Core\Utils;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UploadService;
use App\Views\View;
use App\Services\Mailer;
use App\Core\App;
use DateTime;

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


            // ✅ Message flash facultatif ici
            Utils::flashSuccess("Bienvenue " . $user->getUsername() . " !");

            // ✅ Redirection vers profil (ou dashboard)
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

        // 💪 Vérification de la force du mot de passe
        if (!$this->isPasswordStrong($password)) {
            $view->render('register.html.twig', [
                'error' => 'Mot de passe trop faible. Il doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.'
            ]);
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

        $user = new User($username, $email, $password, 'visitor');
        $user->setBirthdate($birthdate ?: null);
        $user->setAvatar($avatarFilename);

        $userRepo->save($user);

        Utils::flashSuccess("Inscription réussie ! Vous pouvez maintenant vous connecter.");
        Utils::redirect('/login');
    }


    // Mots de passe oublié
    // 1. Affiche le formulaire de "mot de passe oublié"
    public function forgotPasswordForm(): void
    {
        $view = new View();
        $view->render('auth/forgot_password.html.twig');
    }

    // 2. Traite la demande de réinitialisation
    public function handleForgotPassword(): void
    {
        $email = $_POST['email'] ?? '';

        // Vérification basique
        if (empty($email)) {
            Utils::flashError("Veuillez entrer votre adresse e-mail.");
            Utils::redirect('/forgot-password');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Utils::flashError("Adresse e-mail invalide.");
            Utils::redirect('/forgot-password');
            return;
        }

        // Recherche utilisateur
        $userRepo = new UserRepository();
        $user = $userRepo->findByEmail($email);

        // Ne révèle pas si l'e-mail existe
        if (!$user) {
            Utils::flashSuccess("Si un compte existe, un lien de réinitialisation a été envoyé.");
            Utils::redirect('/forgot-password');
            return;
        }

        // Génère token
        $token = bin2hex(random_bytes(32));
        $expiresAt = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');
        $userRepo->saveResetToken($user->getId(), $token, $expiresAt);

        // Envoie e-mail
        $resetLink = ($_ENV['APP_URL'] ?? 'http://thewinners.test') . "/reset-password/$token";
        $subject = "🔐 Réinitialisation de votre mot de passe";
        $body = $this->buildResetEmailBody($user->getUsername(), $resetLink);

        $mailer = new Mailer();
        $mailer->send($user->getEmail(), $user->getUsername(), $subject, $body);

        // Affiche une page de confirmation (au lieu de redirect login)
        $view = new View();
        $view->render('auth/forgot_password_success.html.twig');
    }

    // 3. Affiche le formulaire avec le token
    public function resetPasswordForm(string $token): void
    {
        $userRepo = new UserRepository();
        $user = $userRepo->findByResetToken($token);

        if (!$user || $user->getResetTokenExpiresAt() < date('Y-m-d H:i:s')) {
            Utils::flashError("Lien invalide ou expiré.");
            Utils::redirect('/login');
            return;
        }

        $view = new View();
        $view->render('auth/reset_password.html.twig', ['token' => $token]);
    }

    // 4. Traite le nouveau mot de passe
    public function handleResetPassword(string $token): void
    {
       // var_dump("handleResetPassword exécuté ✅");
        //exit;

        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        /*
        // debug
        var_dump('PWD:', $password, 'CONFIRM:', $confirmPassword);
        exit;
        */


        $userRepo = new UserRepository();
        $user = $userRepo->findByResetToken($token);

        if (!$user || $user->getResetTokenExpiresAt() < date('Y-m-d H:i:s')) {
            Utils::flashError("Lien de réinitialisation invalide ou expiré.");
            Utils::redirect('/login');
            return;
        }

        // Validation des mots de passe
        if (empty($password) || $password !== $confirmPassword) {
            Utils::flashError("Les mots de passe ne correspondent pas.");
            Utils::redirect("/reset-password/$token");
            return;
        }

        if (!$this->isPasswordStrong($password)) {
            Utils::flashError("Le mot de passe ne respecte pas les critères de sécurité.");
            Utils::redirect("/reset-password/$token");
            return;
        }

        // Mise à jour
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        $user->setResetToken(null);
        $user->setResetTokenExpiresAt(null);
        $userRepo->updatePasswordAndClearToken($user);

        Utils::flashSuccess("Mot de passe mis à jour. Vous pouvez vous connecter.");
        Utils::redirect('/login');
    }

    // Fonction réutilisable : vérifie la complexité
    private function isPasswordStrong(string $password): bool
    {
        return strlen($password) >= 8 &&
            preg_match('/[A-Z]/', $password) &&
            preg_match('/[a-z]/', $password) &&
            preg_match('/\d/', $password) &&
            preg_match('/[\W_]/', $password);
    }

    // Génère le contenu HTML du mail
    private function buildResetEmailBody(string $username, string $resetLink): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Réinitialisation</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
  <div style="max-width: 600px; margin: auto; background: #fff; border-radius: 8px; padding: 30px;">
    <h2 style="color: #d62828;">🔐 Réinitialisation de votre mot de passe</h2>
    <p>Bonjour <strong>{$username}</strong>,</p>
    <p>Vous avez demandé une réinitialisation. Cliquez ci-dessous pour créer un nouveau mot de passe :</p>
    <p style="text-align: center;">
      <a href="$resetLink" style="background-color: #d62828; color: white; padding: 12px 20px; border-radius: 5px; text-decoration: none;">
        Réinitialiser mon mot de passe
      </a>
    </p>
    <p>Ce lien est valable 1 heure.</p>
    <p style="font-size: 14px; color: #888;">— L’équipe TheWinners</p>
  </div>
</body>
</html>
HTML;
    }


}
