<?php
// 🔍 Affichage des erreurs (utile en développement)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 📦 Autoload + .env
require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Mailer;
use App\Core\Env;
Env::load();

// 📨 Création du mailer
$mailer = new Mailer();

// 📍 Données pour test
$toEmail = 'djbouxi@gmail.com';
$toName = 'Bouxi';
$subject = '🔐 Test Réinitialisation de mot de passe - TheWinners';

// 🔐 Génération d’un lien de test
$token = bin2hex(random_bytes(32));
$resetLink = $_ENV['APP_URL'] . "/reset-password/$token";

// 📄 Contenu HTML du mail
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
    <p>Bonjour <strong>$toName</strong>,</p>
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

// ✉️ Envoi du mail
if ($mailer->send($toEmail, $toName, $subject, $body)) {
    echo "✅ Mail envoyé avec succès à $toEmail\n";
    echo "🔗 Lien utilisé : $resetLink\n";
} else {
    echo "❌ Échec de l'envoi du mail. Vérifie les logs.\n";
}
