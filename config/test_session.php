<?php

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'path' => '/',
        'domain' => 'localhost',
        'secure' => false, // Mettre à true si HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

$_SESSION['test'] = 'Session active';
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
