<?php

use App\Controllers\AdminController;
use App\Controllers\AdminUserController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\ProfileController;
use App\Controllers\TipsController;
use App\Controllers\MessageController;
use App\Core\Router;

/**
 * Déclaration de toutes les routes HTTP
 *
 * @param Router $router
 * @return void
 */
return function (Router $router): void {

    // Authentification
    $router->addRoute('GET', '/', [new HomeController(), 'index']);
    $router->addRoute('GET', '/login', [new AuthController(), 'loginForm']);
    $router->addRoute('POST', '/login', [new AuthController(), 'login']);
    $router->addRoute('GET', '/logout', [new AuthController(), 'logout']);
    $router->addRoute('GET', '/register', [new AuthController(), 'registerForm']);
    $router->addRoute('POST', '/register', [new AuthController(), 'register']);

    // Profil utilisateur
    $router->addRoute('GET', '/profile', [new ProfileController(), 'index']);
    $router->addRoute('POST','/profile/update', [new ProfileController(), 'update']);
    $router->addRoute('POST', '/profile/remove-avatar', [new ProfileController(), 'removeAvatar']);

    // Messagerie instantanée
    $router->addRoute('GET', '/messages', [new MessageController(), 'index']);
    $router->addRoute('GET', '/messages/new', [new MessageController(), 'create']);
    $router->addRoute('POST', '/messages/send', [new MessageController(), 'send']);
    $router->addRoute('GET', '/messages/read/{id}', [new MessageController(), 'read']);
    $router->addRoute('POST', '/messages/delete/{id}', [new MessageController(), 'delete']);
    $router->addRoute('GET', '/messages/reply/{id}', [new MessageController(), 'reply']);
    $router->addRoute('GET', '/messages/fetch', [new MessageController(), 'fetch']);
    $router->addRoute('GET', '/messages/unread-count', [new MessageController(), 'unreadCount']);


    // Administration
    $router->addRoute('GET', '/admin', [new AdminController(), 'dashboard']);
    $router->addRoute('GET', '/admin/users', [new AdminUserController(), 'index']);
    $router->addRoute('GET', '/admin/users/create', [new AdminUserController(), 'create']);
    $router->addRoute('POST', '/admin/users/store', [new AdminUserController(), 'store']);
    $router->addRoute('GET', '/admin/users/edit/{id}', [new AdminUserController(), 'edit']);
    $router->addRoute('POST', '/admin/users/update/{id}', [new AdminUserController(), 'update']);
    $router->addRoute('POST', '/admin/users/delete/{id}', [new AdminUserController(), 'delete']);

    // Tips
    $router->addRoute('GET', '/tips/add', [new TipsController(), 'addForm']);
    $router->addRoute('POST', '/tips/add', [new TipsController(), 'add']);
    $router->addRoute('GET', '/tips', [new TipsController(), 'index']);
    $router->addRoute('GET', '/tips/{category}', [new TipsController(), 'category']);
    $router->addRoute('GET', '/tips/{category}/{id}', [new TipsController(), 'show']);
    $router->addRoute('POST', '/tips/{category}/{id}/comment', [new TipsController(), 'addComment']);
};
