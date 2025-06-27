<?php

return [
    // 🏷️ Infos globales sur l'application
    'APP_NAME' => 'TheWinners',
    'APP_ENV'  => 'dev', // 'dev' ou 'prod'

    // 🔧 Détection automatique du chemin de base
    'BASE_PATH' => str_replace('\\', '/', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/')),

    // 💾 Configuration de la base de données (prête pour PDO)
    'DB' => [
        'host'     => 'localhost',
        'port'     => '3306',
        'dbname'   => 'thewinners',
        'user'     => 'root',
        'password' => '',
        'charset'  => 'utf8mb4'
    ],
];
