<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Bootstrap;
use App\Core\Router;

// Démarrage global (sessions)
Bootstrap::start();

$router = new Router();
$router->handleRequest();
