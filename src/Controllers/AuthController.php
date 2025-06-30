<?php

namespace App\Controllers;

use App\Core\Utils;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UploadService;
use App\Views\View;
use App\Services\Mailer;
use App\Core\App;

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

        Utils::redirectSuccess('/login', 'Inscription réussie ! Vous pouvez vous connecter.');
    }


    // Mots de passe oublié
    // Affiche le formulaire de demande de réinitialisation
    public function forgotPasswordForm(): void
    {
        $view = new View();
        $view->render('auth/forgot_password.html.twig');
    }

    // Envoie le lien de réinitialisation
    public function handleForgotPassword(): void
    {
        $email = $_POST['email'] ?? '';

        // ✅ Vérifications de base
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

        // 🔍 Recherche utilisateur
        $userRepo = new UserRepository();
        $user = $userRepo->findByEmail($email);

        // 🛑 Ne révèle pas si l'email est inconnu
        if (!$user) {
            Utils::flashSuccess("Si un compte existe, un lien a été envoyé à votre adresse e-mail.");
            Utils::redirect('/forgot-password');
            return;
        }

        // 🔐 Génère un token et une date d’expiration
        $token = bin2hex(random_bytes(32));
        $expiresAt = (new \DateTime('+1 hour'))->format('Y-m-d H:i:s');

        // 💾 Sauvegarde du token
        $userRepo->saveResetToken($user->getId(), $token, $expiresAt);

        // 🔗 Lien de réinitialisation
        $baseUrl = $_ENV['APP_URL'] ?? 'http://thewinners.test';
        $resetLink = "$baseUrl/reset-password/$token";

        // 📨 Contenu du mail HTML
        $subject = "🔐 Réinitialisation de votre mot de passe - TheWinners";
        $body = <<<HTML
    <!DOCTYPE html>
    <html lang="fr">
    <head>
      <meta charset="UTF-8">
      <title>Réinitialisation</title>
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
      <div style="max-width: 600px; margin: auto; background: #fff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h2 style="color: #d62828;">🔐 Réinitialisation de votre mot de passe</h2>
        <p>Bonjour <strong>{$user->getUsername()}</strong>,</p>
        <p>Vous avez demandé une réinitialisation de mot de passe.</p>
        <p>Veuillez cliquer sur le bouton ci-dessous pour choisir un nouveau mot de passe :</p>
        <p style="text-align: center; margin: 30px 0;">
          <a href="$resetLink" style="background-color: #d62828; color: white; padding: 12px 20px; border-radius: 5px; text-decoration: none;">Réinitialiser mon mot de passe</a>
        </p>
        <p>Ce lien est valable pendant 1 heure.</p>
        <p style="font-size: 14px; color: #888;">— L’équipe TheWinners</p>
      </div>
    </body>
    </html>
    HTML;

        // 📬 Envoi du mail
        $mailer = new \App\Services\Mailer();
        $mailer->send($email, $user->getUsername(), $subject, $body);

        // ✅ Message utilisateur
        Utils::flashSuccess("Si un compte existe, un lien a été envoyé à votre adresse e-mail.");
        Utils::redirect('/login');
    }


    // Affiche le formulaire pour entrer un nouveau mot de passe
    public function resetPasswordForm(string $token): void
    {
        $userRepo = new UserRepository();
        $user = $userRepo->findByResetToken($token);

        // 🔐 On vérifie que le token est valide et non expiré
        if (!$user || $user->getResetTokenExpiresAt() < date('Y-m-d H:i:s')) {
            Utils::flashError("Lien invalide ou expiré.");
            Utils::redirect('/login');
            return;
        }

        // 📄 On affiche le formulaire avec le token en hidden
        $view = new View();
        $view->render('auth/reset_password.html.twig', [
            'token' => $token
        ]);
    }

    /*
    public function resetPasswordForm(string $token): void
    {
        $userRepo = new UserRepository();
        $user = $userRepo->findByResetToken($token);

        // 🧪 DEBUG TEMPORAIRE
        echo "<pre>";
        var_dump("Token reçu :", $token);
        var_dump("Utilisateur trouvé :", $user);
        echo "</pre>";
        exit; // 💣 Arrête l’exécution pour bien voir le résultat
    }
    */



    // Traite la soumission du nouveau mot de passe
    public function handleResetPassword(string $token): void
    {
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $userRepo = new UserRepository();
        $user = $userRepo->findByResetToken($token);

        // 🔐 Vérifie que le token existe et n’a pas expiré
        if (!$user || $user->getResetTokenExpiresAt() < date('Y-m-d H:i:s')) {
            Utils::flashError("Lien de réinitialisation invalide ou expiré.");
            Utils::redirect('/login');
            return;
        }

        // 🧪 Vérifie les mots de passe
        if (empty($password) || $password !== $confirmPassword) {
            Utils::flashError("Les mots de passe ne correspondent pas.");
            Utils::redirect("/reset-password/$token");
            return;
        }

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
            Utils::flashError("Le mot de passe ne respecte pas les critères de sécurité.");
            Utils::redirect("/reset-password/$token");
            return;
        }


        // 🔐 Hachage sécurisé
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // 💾 Mise à jour
        $user->setPassword($hashedPassword);
        $user->setResetToken(null);
        $user->setResetTokenExpiresAt(null);
        $userRepo->updatePasswordAndClearToken($user);

        // ✅ Confirmation utilisateur
        Utils::flashSuccess("Votre mot de passe a bien été modifié. Vous pouvez maintenant vous connecter.");
        Utils::redirect('/login');
    }

    // Ajoute cette fonction privée dans AuthController :
    private function isPasswordStrong(string $password): bool {
        return strlen($password) >= 8 &&
            preg_match('/[A-Z]/', $password) &&
            preg_match('/[a-z]/', $password) &&
            preg_match('/[0-9]/', $password) &&
            preg_match('/[^A-Za-z0-9]/', $password);
    }


}
