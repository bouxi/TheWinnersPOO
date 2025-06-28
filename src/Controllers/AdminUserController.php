<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\User;
use App\Repositories\UserRepository;
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userRepo = new UserRepository();
            $userRepo->delete($id);

            header('Location: /admin/users');
            exit;
        } else {
            echo "Méthode non autorisée.";
        }
    }


    public function edit(int $id): void {
        Auth::requireRole(['admin', 'guild_master']);

        $userRepo = new UserRepository();
        $user = $userRepo->findById($id);

        if (!$user) {
            echo "Utilisateur non trouvé.";
            return;
        }

        $view = new View();
        $view->render('admin/edit_user.html.twig', [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()
        ]);
    }

    public function update(int $id): void {
        Auth::requireRole(['admin', 'guild_master']);

        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'member';

        $userRepo = new UserRepository();
        $user = $userRepo->findById($id);

        if ($user) {
            $user->setUsername($username);
            $user->setEmail($email);

            if (!empty($password)) {
                $user->setPassword($password);
            }

            $user->setRole($role);

            if ($userRepo->update($user)) {
                header('Location: /admin/users');
                exit;
            } else {
                echo "Erreur lors de la mise à jour.";
            }
        } else {
            echo "Utilisateur non trouvé.";
        }
    }
}
