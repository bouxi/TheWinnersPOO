<?php
require __DIR__ . '/../vendor/autoload.php';



    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);



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

// 🚀 Démarrage session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ⚙️ Chargement de l'environnement
Env::load(); // charge les variables de ton fichier .env

$env = Env::get('APP_ENV');

// ⚙️ Boot de l'application
Bootstrap::start();

// 🧩 Configuration de Twig
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader, [
    'debug' => true,
]);

// 📌 Injection dans App
App::setTwig($twig);

// 🐞 Extension Debug (pour dump() etc.)
$twig->addExtension(new DebugExtension());

// ✅ Traitement de l'utilisateur en session
if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
    $userObject = User::fromArray($_SESSION['user']);
    $twig->addGlobal('user', $userObject);
    $twig->addGlobal('isAuthenticated', true);

    // 💬 Compteur de messages non lus
    $messageRepo = new MessageRepository();
    $unreadMessages = $messageRepo->findUnreadCountByUserId($userObject->getId());
    $twig->addGlobal('unreadMessages', $unreadMessages);
} else {
    $twig->addGlobal('isAuthenticated', false);
    $twig->addGlobal('unreadMessages', 0);
}

// 🛠️ Fonction asset() disponible dans Twig
$twig->addFunction(new TwigFunction('asset', function ($path) {
    return App::getBasePath() . '/' . ltrim($path, '/');
}));

// 🌍 Variables globales supplémentaires
$twig->addGlobal('app_name', App::getAppName());
$twig->addGlobal('base_path', App::getBasePath());
$twig->addGlobal('isDevMode', App::isDevMode());

// 🧭 Routing
$router = new Router();
(require __DIR__ . '/../routes.php')($router);
$router->handleRequest();

