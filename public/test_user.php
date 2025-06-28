<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;
use App\Repositories\UserRepository;

$userRepo = new UserRepository();
$user = new User('Bouxi', 'bouxi@example.com', '$2y$12$VW67v9brNzhklAwu9095neemQSnVdCaJVSPH9h3EarBpbGBa9McLG', 'admin', '1986-12-24');

if ($userRepo->save($user)) {
    echo "Utilisateur enregistré avec succès !";
} else {
    echo "Erreur lors de l'enregistrement.";
}
