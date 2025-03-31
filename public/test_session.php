<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Bootstrap;

// Démarrage global (sessions)
Bootstrap::start();

echo "<pre>";
print_r($_SESSION);
echo "</pre>";
