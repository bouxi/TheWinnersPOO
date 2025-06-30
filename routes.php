<?php

use App\Controllers\AdminController;
use App\Controllers\AdminMessageController;
use App\Controllers\AdminUserController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\MessageController;
use App\Controllers\ProfileController;
use App\Controllers\TipsController;
use App\Core\Router;
use App\Controllers\NotAvailableController;
use App\Views\View;

return function (Router $router): void {
    // 🔐 Protection pour les routes admin
    $requireAdmin = fn() => \App\Core\Auth::requireRole(['admin', 'guild_master']);

    // 🏠 Pages publiques
    $router->addRoute('GET', '/', [new HomeController(), 'index']);

    // 🔑 Authentification
    $router->addRoute('GET', '/login', [new AuthController(), 'loginForm']);
    $router->addRoute('POST', '/login', [new AuthController(), 'login']);
    $router->addRoute('GET', '/logout', [new AuthController(), 'logout']);
    $router->addRoute('GET', '/register', [new AuthController(), 'registerForm']);
    $router->addRoute('POST', '/register', [new AuthController(), 'register']);

    // 👤 Profil utilisateur
    $router->addRoute('GET', '/profile', [new ProfileController(), 'index']);
    $router->addRoute('POST', '/profile/update', [new ProfileController(), 'update']);
    $router->addRoute('POST', '/profile/remove-avatar', [new ProfileController(), 'removeAvatar']);

    // 💬 Messagerie utilisateur
    $router->addRoute('GET', '/messages', [new MessageController(), 'index']);
    $router->addRoute('GET', '/messages/new', [new MessageController(), 'create']);
    $router->addRoute('POST', '/messages/send', [new MessageController(), 'send']);
    $router->addRoute('GET', '/messages/read/{id}', [new MessageController(), 'read']);
    $router->addRoute('POST', '/messages/delete/{id}', [new MessageController(), 'delete']);
    $router->addRoute('GET', '/messages/reply/{id}', [new MessageController(), 'reply']);
    $router->addRoute('GET', '/messages/fetch', [new MessageController(), 'fetch']);
    $router->addRoute('GET', '/messages/unread-count', [new MessageController(), 'unreadCount']);

    // 🛠️ Administration
    $router->addRoute('GET', '/admin', function () use ($requireAdmin) {
        $requireAdmin();
        (new AdminController())->dashboard();
    });

    // 👥 Gestion des utilisateurs
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
    $router->addRoute('GET', '/admin/users/{id}/edit', function ($id) use ($requireAdmin) {
        $requireAdmin();
        (new AdminUserController())->edit((int)$id);
    });


    $router->addRoute('POST', '/admin/users/update/{id}', function ($id) use ($requireAdmin) {
        $requireAdmin();
        (new AdminUserController())->update((int)$id);
    });
    $router->addRoute('POST', '/admin/users/delete/{id}', function ($id) use ($requireAdmin) {
        $requireAdmin();
        (new AdminUserController())->delete((int)$id);
    });

    // ✉️ Gestion des messages admin
    $router->addRoute('GET', '/admin/messages', function () use ($requireAdmin) {
        $requireAdmin();
        (new AdminMessageController())->index();
    });
    $router->addRoute('GET', '/admin/messages/view/{id}', function ($id) use ($requireAdmin) {
        $requireAdmin();
        (new AdminMessageController())->view((int)$id);
    });
    $router->addRoute('POST', '/admin/messages/delete/{id}', function ($id) use ($requireAdmin) {
        $requireAdmin();
        (new AdminMessageController())->delete((int)$id);
    });

    $router->addRoute('GET','/en-travaux', [new NotAvailableController(), 'index']);

    // --- Récupération de mot de passe ---
    $router->addRoute('GET', '/forgot-password', fn() => (new AuthController())->forgotPasswordForm());
    $router->addRoute('POST', '/forgot-password', fn() => (new AuthController())->handleForgotPassword());
    $router->addRoute('GET', '/reset-password/{token}', fn($token) => (new AuthController())->resetPasswordForm($token));
    $router->addRoute('POST', '/reset-password/{token}', fn($token) => (new AuthController())->handleResetPassword($token));


    // 💡 Conseils / Astuces
    $router->addRoute('GET', '/tips', [new TipsController(), 'index']);
    $router->addRoute('GET', '/tips/add', [new TipsController(), 'addForm']);
    $router->addRoute('POST', '/tips/add', [new TipsController(), 'add']);
    $router->addRoute('GET', '/tips/{category}', [new TipsController(), 'category']);
    $router->addRoute('GET', '/tips/{category}/{id}', [new TipsController(), 'show']);
    $router->addRoute('POST', '/tips/{category}/{id}/comment', [new TipsController(), 'addComment']);

    // 📄 Pages publiques (footer)
    $router->addRoute('GET', '/news', fn() => (new View())->render('pages/news.html.twig'));
    $router->addRoute('GET', '/members', fn() => (new View())->render('pages/members.html.twig'));
    $router->addRoute('GET', '/forum', fn() => (new View())->render('pages/forum.html.twig'));
    $router->addRoute('GET', '/faq', fn() => (new View())->render('pages/faq.html.twig'));
    $router->addRoute('GET', '/cgu', fn() => (new View())->render('pages/cgu.html.twig'));
    $router->addRoute('GET', '/confidentialite', fn() => (new View())->render('pages/confidentialite.html.twig'));
    $router->addRoute('GET', '/legal', fn() => (new View())->render('pages/legal.html.twig'));
};
