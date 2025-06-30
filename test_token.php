<?php

// Activer les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Charger l'autoloader de Composer
require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Bootstrap;
use App\Repositories\UserRepository;

// ⚙️ Initialise ton système (si besoin)
Bootstrap::start(); // si tu en as besoin pour la BDD

// ⚠️ Données de test
$userId = 1; // Id d’un utilisateur réel
$token = bin2hex(random_bytes(32));
$expiresAt = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

// Appel de la méthode avec var_dump pour debug
$userRepo = new UserRepository();
$result = $userRepo->saveResetToken($userId, $token, $expiresAt);

// 🔍 Affichage
echo "<pre>";
var_dump("Résultat de saveResetToken:", $result);
echo "</pre>";
