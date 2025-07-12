<?php
// Démarrer le timer
//define('DEBUG_START', microtime(true)); // Pour le debug

// Inclure l'autoloader, démarrer l'application...
require __DIR__ . '/../vendor/autoload.php';
//require __DIR__ . '/../src/Debug/Debug.php'; // Notre futur système

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
// ✅ Fuseau horaire
date_default_timezone_set('Europe/Brussels');

use App\Core\Env;
use App\Core\Bootstrap;
use App\Core\Router;
use App\Core\App;
use App\Models\User;
use App\Repositories\MessageRepository;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Twig\Extension\DebugExtension;

// 🚀 Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ⚙️ Environnement
Env::load();
$env = Env::get('APP_ENV');

// ⚙️ Boot
Bootstrap::start();

// 🧩 Twig
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader, ['debug' => true]);
App::setTwig($twig);

// 🐞 Extension dump()
$twig->addExtension(new DebugExtension());

// ✅ Variables Twig globales pour utilisateur connecté
if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
    $userObject = User::fromArray($_SESSION['user']);
    $twig->addGlobal('user', $userObject);
    $twig->addGlobal('isAuthenticated', true);

    $messageRepo = new MessageRepository();
    $unreadMessages = $messageRepo->findUnreadCountByUserId($userObject->getId());
    $twig->addGlobal('unreadMessages', $unreadMessages);
} else {
    $twig->addGlobal('isAuthenticated', false);
    $twig->addGlobal('unreadMessages', 0);
}

// ✅ Fonctions Twig
$twig->addFunction(new TwigFunction('asset', function ($asset) {
    return sprintf('/assets/%s', ltrim($asset, '/'));
}));

$twig->addFunction(new TwigFunction('avatar_url', function (?string $filename): string {
    $file = $filename ?: 'default-avatar.png';
    return App::getBasePath() . '/uploads/avatars/' . ltrim($file, '/');
}));

// ✅ Autres globales
$twig->addGlobal('app_name', App::getAppName());
$twig->addGlobal('base_path', App::getBasePath());
$twig->addGlobal('isDevMode', App::isDevMode());

// 🧭 Routes
$router = new Router();
(require __DIR__ . '/../routes.php')($router);
$router->handleRequest();

// Affichage de la DebugBar
//\App\Debug\Debug::render(); // Pour le debug
