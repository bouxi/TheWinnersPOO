<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Bootstrap;
use App\Core\Router;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

// Vérifier si la session n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

Bootstrap::start();

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

// Vérifier si l'utilisateur est connecté
$isAuthenticated = isset($_SESSION['user']);

// Rendre la variable globale pour tous les templates Twig
$twig->addGlobal('isAuthenticated', $isAuthenticated);

// Ajouter la fonction 'asset' pour gérer les liens vers les fichiers statiques
$twig->addFunction(new TwigFunction('asset', function ($asset) {
    return sprintf('/assets/%s', ltrim($asset, '/'));
}));



$router = new Router();

// Route de debug pour lister toutes les routes
$router->addRoute('GET', '/debug/routes', function () use ($router) {
    echo "<h2>Liste des Routes</h2>";
    echo "<table border='1'><tr><th>Méthode</th><th>Chemin</th><th>Callback</th></tr>";
    foreach ($router->getRoutes() as $route) {
        echo "<tr><td>{$route['method']}</td><td>{$route['path']}</td><td>" . gettype($route['callback']) . "</td></tr>";
    }
    echo "</table>";
});

// Routes d'authentification
$router->addRoute('GET', '/', [new \App\Controllers\HomeController(), 'index']);
$router->addRoute('GET', '/login', [new \App\Controllers\AuthController(), 'loginForm']);
$router->addRoute('POST', '/login', [new \App\Controllers\AuthController(), 'login']);
$router->addRoute('GET', '/logout', [new \App\Controllers\AuthController(), 'logout']);
$router->addRoute('GET', '/register', [new \App\Controllers\AuthController(), 'registerForm']);
$router->addRoute('POST', '/register', [new \App\Controllers\AuthController(), 'register']);
$router->addRoute('GET', '/profile', [new \App\Controllers\ProfileController(), 'index']);
$router->addRoute('POST','/profile/update', [new \App\Controllers\ProfileController(), 'update']);

// Routes d'administration
$router->addRoute('GET', '/admin', [new \App\Controllers\AdminController(), 'dashboard']);
$router->addRoute('GET', '/admin/users', [new \App\Controllers\AdminUserController(), 'index']);
$router->addRoute('GET', '/admin/users/create', [new \App\Controllers\AdminUserController(), 'create']);
$router->addRoute('POST', '/admin/users/store', [new \App\Controllers\AdminUserController(), 'store']);
$router->addRoute('GET', '/admin/users/edit/{id}', [new \App\Controllers\AdminUserController(), 'edit']);
$router->addRoute('POST', '/admin/users/update/{id}', [new \App\Controllers\AdminUserController(), 'update']);
$router->addRoute('POST', '/admin/users/delete/{id}', [new \App\Controllers\AdminUserController(), 'delete']);


// Routes pour ajouter une astuce (protégé pour les utilisateurs connectés)
$router->addRoute('GET', '/tips/add', [new \App\Controllers\TipsController(), 'addForm']);
$router->addRoute('POST', '/tips/add', [new \App\Controllers\TipsController(), 'add']);
// Routes pour la section Astuces
$router->addRoute('GET', '/tips', [new \App\Controllers\TipsController(), 'index']);
$router->addRoute('GET', '/tips/{category}', [new \App\Controllers\TipsController(), 'category']);
$router->addRoute('GET', '/tips/{category}/{id}', [new \App\Controllers\TipsController(), 'show']);
$router->addRoute('POST', '/tips/{category}/{id}/comment', [new \App\Controllers\TipsController(), 'addComment']);



// Gestion de la requête
$router->handleRequest();
