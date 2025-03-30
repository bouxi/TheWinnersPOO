<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\UserRepository;
use App\Models\User;
use App\Views\View;

class AdminUserController {

    public function index(): void {
        Auth::requireRole(['admin', 'guild_master']);
        $userRepo = new UserRepository();
        $users = $userRepo->getAllUsers();

        $view = new View();
        $view->render('admin/users.html.twig', ['users' => $users]);
    }

    public function create(): void {
        Auth::requireRole(['admin', 'guild_master']);
        $view = new View();
        $view->render('admin/create_user.html.twig');
    }

    public function store(): void {
        Auth::requireRole(['admin', 'guild_master']);
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'member';

        $userRepo = new UserRepository();
        $user = new User($username, $email, $password, $role);
        $userRepo->save($user);

        header('Location: /admin/users');
        exit;
    }

    public function delete(int $id): void {
        Auth::requireRole(['admin', 'guild_master']);
        $userRepo = new UserRepository();
        $userRepo->delete($id);

        header('Location: /admin/users');
        exit;
    }
}
