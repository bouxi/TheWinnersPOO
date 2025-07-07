<?php

namespace App\Controllers;

use App\Repositories\MessageRepository;
use App\Repositories\UserRepository;
use App\Views\View;
use App\Core\Utils;

class AdminMessageController {

    public function index(): void {
        $search = $_GET['q'] ?? null;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $repo = new MessageRepository();
        $messages = $repo->findPaginatedWithSearch($limit, $offset, $search);
        $total = $repo->countAllWithSearch($search);

        $view = new View();
        $view->render('admin/messages.html.twig', [
            'messages' => $messages,
            'flash_success' => \App\Core\Utils::getFlashSuccess(),
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => ceil($total / $limit)
        ]);
    }


    public function delete(int $id): void {
        (new MessageRepository())->delete($id);
        Utils::flashSuccess("Message supprimé avec succès.");
        header('Location: /admin/messages');
        exit;
    }

    public function view(int $id): void {
        $message = (new MessageRepository())->findWithUsernamesById($id);

        if (!$message) {
            \App\Core\Utils::flashError("Message introuvable.");
            header('Location: /admin/messages');
            exit;
        }

        $view = new View();
        $view->render('admin/message_view.html.twig', [
            'message' => $message
        ]);
    }

    public function messages(): void
    {
        Auth::requireRole(['admin', 'guild_master']);

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $repo = new MessageRepository();
        $messages = $repo->findPaginatedWithSearch($limit, $offset);  // 🔍 tu peux rajouter $search si besoin
        $total = $repo->countAllWithSearch(); // 🔍 idem, rajoute $search

        $totalPages = ceil($total / $limit);

        $view = new View();
        $view->render('admin/messages.html.twig', [
            'messages' => $messages,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

}
