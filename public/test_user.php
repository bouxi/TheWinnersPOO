<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;
use App\Models\UserRepository;

$userRepo = new UserRepository();
$user = new User('Bouxi', 'bouxi@example.com', 'password123');

if ($userRepo->save($user)) {
    echo "Utilisateur enregistré avec succès !";
} else {
    echo "Erreur lors de l'enregistrement.";
}
