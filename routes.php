<?php

use App\Controllers\AdminController;
use App\Controllers\AdminUserController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\ProfileController;
use App\Controllers\TipsController;
use App\Controllers\MessageController;
use App\Core\Auth;
use App\Controllers\AdminMessageController;
use App\Core\Router;

/**
 * Déclaration de toutes les routes HTTP
 *
 * @param Router $router
 * @return void
 */
return function (Router $router): void {

    // Constante locale pour protection admin
    $requireAdmin = fn() => \App\Core\Auth::requireRole(['admin', 'guild_master']);

    // --- Authentification ---
    $router->addRoute('GET', '/', [new HomeController(), 'index']);
    $router->addRoute('GET', '/login', [new AuthController(), 'loginForm']);
    $router->addRoute('POST', '/login', [new AuthController(), 'login']);
    $router->addRoute('GET', '/logout', [new AuthController(), 'logout']);
    $router->addRoute('GET', '/register', [new AuthController(), 'registerForm']);
    $router->addRoute('POST', '/register', [new AuthController(), 'register']);

    // --- Profil utilisateur ---
    $router->addRoute('GET', '/profile', [new ProfileController(), 'index']);
    $router->addRoute('POST', '/profile/update', [new ProfileController(), 'update']);
    $router->addRoute('POST', '/profile/remove-avatar', [new ProfileController(), 'removeAvatar']);

    // --- Messagerie instantanée ---
    $router->addRoute('GET', '/messages', [new MessageController(), 'index']);
    $router->addRoute('GET', '/messages/new', [new MessageController(), 'create']);
    $router->addRoute('POST', '/messages/send', [new MessageController(), 'send']);
    $router->addRoute('GET', '/messages/read/{id}', [new MessageController(), 'read']);
    $router->addRoute('POST', '/messages/delete/{id}', [new MessageController(), 'delete']);
    $router->addRoute('GET', '/messages/reply/{id}', [new MessageController(), 'reply']);
    $router->addRoute('GET', '/messages/fetch', [new MessageController(), 'fetch']);
    $router->addRoute('GET', '/messages/unread-count', [new MessageController(), 'unreadCount']);

    // --- Administration (protégée) ---
    $router->addRoute('GET', '/admin', function () use ($requireAdmin) {
        $requireAdmin();
        (new AdminController())->dashboard();
    });

    $router->addRoute('GET', '/admin/users', function () use ($requireAdmin) {
        $requireAdmin();
        (new AdminUserController())->index();
    });

    $router->addRoute('GET', '/admin/users/create', function () use ($requireAdmin) {
        $requireAdmin();
        (new AdminUserController())->create();
    });

    $router->addRoute('POST', '/admin/users/store', function () use ($requireAdmin) {
        $requireAdmin();
        (new AdminUserController())->store();
    });

    $router->addRoute('GET', '/admin/users/edit/{id}', function ($id) use ($requireAdmin) {
        $requireAdmin();
        (new AdminUserController())->edit($id);
    });

    $router->addRoute('POST', '/admin/users/update/{id}', function ($id) use ($requireAdmin) {
        $requireAdmin();
        (new AdminUserController())->update($id);
    });

    $router->addRoute('POST', '/admin/users/delete/{id}', function ($id) use ($requireAdmin) {
        $requireAdmin();
        (new AdminUserController())->delete($id);
    });

    // Gestion des messages (admin)
    $router->addRoute('GET', '/admin/messages', function () use ($requireAdmin) {
        $requireAdmin();
        (new AdminMessageController())->index();
    });

    $router->addRoute('POST', '/admin/messages/delete/{id}', function ($id) use ($requireAdmin) {
        $requireAdmin();
        (new AdminMessageController())->delete($id);
    });

    $router->addRoute('GET', '/admin/messages/view/{id}', function ($id) use ($requireAdmin) {
        $requireAdmin();
        (new \App\Controllers\AdminMessageController())->view($id);
    });


    // --- Conseils / Astuces ---
    $router->addRoute('GET', '/tips/add', [new TipsController(), 'addForm']);
    $router->addRoute('POST', '/tips/add', [new TipsController(), 'add']);
    $router->addRoute('GET', '/tips', [new TipsController(), 'index']);
    $router->addRoute('GET', '/tips/{category}', [new TipsController(), 'category']);
    $router->addRoute('GET', '/tips/{category}/{id}', [new TipsController(), 'show']);
    $router->addRoute('POST', '/tips/{category}/{id}/comment', [new TipsController(), 'addComment']);
};
