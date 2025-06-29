#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\App;
use App\Core\Database;
use App\Core\Router;
use App\Models\User;
use App\Repositories\UserRepository;

// Charge la config
App::loadConfig();

// Récupère la commande
$command = $argv[1] ?? null;

switch ($command) {
    case 'db:reset':
        Database::reset();
        $pdo = Database::getConnection();
        echo "✅ Connexion PDO réinitialisée.\n";
        break;

    case 'db:test':
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->query('SELECT 1');
            echo "✅ Connexion à la base de données : OK\n";
        } catch (\Throwable $e) {
            echo "❌ Erreur DB : " . $e->getMessage() . "\n";
        }
        break;

    case 'user:create':
        $username = readline("Nom d'utilisateur : ");
        $email    = readline("Adresse e-mail : ");
        $password = readline("Mot de passe : ");
        $role     = readline("Rôle (ex: admin) [admin par défaut] : ");
        $birthdate = readline("Date de naissance (YYYY-MM-DD) : ");
        $avatar   = 'default.png';

        $role = $role ?: 'admin';

        $user = new User($username, $email, $password, $role);
        $user->setAvatar($avatar);
        $user->setBirthdate($birthdate);
        $user->setDateInscription(date('Y-m-d H:i:s'));

        $repo = new UserRepository();
        if ($repo->save($user)) {
            echo "✅ Utilisateur '$username' créé avec succès.\n";
        } else {
            echo "❌ Erreur lors de la création.\n";
        }
        break;

    case 'route:list':
        $router = new Router();
        (require __DIR__ . '/routes.php')($router); // charge les vraies routes

        echo "🛣️  Liste des routes :\n";
        foreach ($router->getRoutes() as $route) {
            echo "- [{$route['method']}] {$route['path']}\n";
        }
        break;


    case 'test:.env.prod.prod':
        echo "App: " . App::getAppName() . "\n";
        echo "Env : " . (App::isDevMode() ? 'dev' : 'prod') . "\n";
        break;

    default:
        echo "❓ Commande inconnue.\n";
        echo "Commandes disponibles :\n";
        echo " - db:reset       Réinitialise la connexion DB\n";
        echo " - db:test        Teste la connexion à la DB\n";
        echo " - user:create    Crée un utilisateur\n";
        echo " - route:list     Affiche les routes connues\n";
        echo " - test:.env.prod.prod       Affiche l'environnement\n";
        exit(1);
}
