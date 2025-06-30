<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Views\View;

class AdminUserController
{
    public function index(): void
    {
        Auth::requireRole(['admin', 'guild_master']);

        $repo = new UserRepository();
        $users = $repo->findAll();

        (new View())->render('admin/users/index.html.twig', [
            'users' => $users
        ]);
    }

    public function edit(int $id): void
    {
        Auth::requireRole(['admin', 'guild_master']);

        $repo = new UserRepository();
        $user = $repo->findById($id);

        if (!$user) {
            http_response_code(404);
            echo "Utilisateur introuvable";
            return;
        }

        (new View())->render('admin/users/edit.html.twig', [
            'user' => $user
        ]);
    }

    public function update(int $id): void
    {
        Auth::requireRole(['admin', 'guild_master']);

        $repo = new UserRepository();
        $user = $repo->findById($id);

        if (!$user) {
            http_response_code(404);
            echo "Utilisateur introuvable";
            return;
        }

        $newRole = $_POST['role'] ?? null;

        if (in_array($newRole, ['admin', 'guild_master', 'officier', 'veteran', 'membre', 'recrue', 'visitor', 'postulant'])) {
            $user->setRole($newRole);
            $repo->update($user);
            header("Location: /admin/users");
        } else {
            echo "Rôle non valide";
        }
    }

    public function delete(int $id): void
    {
        Auth::requireRole(['admin', 'guild_master']);

        $repo = new UserRepository();
        $repo->delete($id);
        header("Location: /admin/users");
    }
}
