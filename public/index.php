<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Bootstrap;
use App\Core\Router;
use App\Core\App;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

// Démarrer la session si elle ne l’est pas déjà
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialisation de l’application
Bootstrap::start();

// Configuration de Twig
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

// ✅ Utilisateur connecté ?
$isAuthenticated = isset($_SESSION['user']);
$twig->addGlobal('isAuthenticated', $isAuthenticated);

// ✅ Ajouter l’objet utilisateur si présent
if ($isAuthenticated) {
    $twig->addGlobal('user', $_SESSION['user']);
}

// Fonction Twig pour asset()
$twig->addFunction(new TwigFunction('asset', function ($path) {
    return App::getBasePath() . '/' . ltrim($path, '/');
}));

// Autres variables globales
$twig->addGlobal('app_name', App::getAppName());
$twig->addGlobal('base_path', App::getBasePath());

// Création du routeur
$router = new Router();

// Chargement des routes
(require __DIR__ . '/../routes.php')($router);

// Gestion de la requête
$router->handleRequest();
