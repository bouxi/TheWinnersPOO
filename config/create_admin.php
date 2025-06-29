<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;
use App\Repositories\UserRepository;

// Définir les infos du compte admin
$pseudo = 'Bouxi003';
$email = 'bouxi@example.com';
$plainPassword = '123456789';
$role = 'ROLE_ADMIN';
$birthdate = '1986-12-24';
$avatar = '/assets/images/default-avatar.png'; // ou le chemin de ton avatar par défaut
$dateInscription = (new DateTime())->format('Y-m-d H:i:s');

// Hachage du mot de passe
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// Instancier l'utilisateur
$user = new User($pseudo, $email, $hashedPassword, $role, $birthdate, $avatar, $dateInscription);

// Récupération du repo
$userRepo = new UserRepository();

// Vérifier si un utilisateur avec le même email existe déjà
if ($userRepo->existsByEmail($email)) {
    echo "⚠️ Un utilisateur avec cet email existe déjà.";
    exit;
}

// Sauvegarde en base
if ($userRepo->save($user)) {
    echo "✅ Utilisateur admin enregistré avec succès !";
} else {
    echo "❌ Erreur lors de l'enregistrement.";
}
