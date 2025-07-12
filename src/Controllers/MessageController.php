<?php

namespace App\Controllers;

use App\Core\Security;
use App\Core\Utils;
use App\Repositories\MessageRepository;
use App\Repositories\UserRepository;
use App\Views\View;

class MessageController
{
    public function index(): void
    {
        Security::requireAuth();
        $user = Security::getCurrentUser();
        $userId = $user->getId();

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 6;
        $offset = ($page - 1) * $limit;

        $repo = new MessageRepository();
        $messages = $repo->findPaginatedByUserId($userId, $limit, $offset);
        $total = $repo->countByUserId($userId);
        $totalPages = ceil($total / $limit);

        (new View())->render('messages/index.html.twig', [
            'user' => $user,
            'messages' => $messages,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function create(): void
    {
        Security::requireAuth();
        $users = (new UserRepository())->findAll();

        (new View())->render('messages/create.html.twig', [
            'users' => $users,
        ]);
    }

    public function send(): void
    {
        Security::requireAuth();
        $user = Security::getCurrentUser();

        $recipientId = filter_var($_POST['recipient_id'] ?? 0, FILTER_VALIDATE_INT);
        $content = trim($_POST['content'] ?? '');

        if ($recipientId && $content) {
            $repo = new MessageRepository();
            $repo->sendMessage($user->getId(), $recipientId, $content);
            Utils::redirectSuccess('/messages', 'Message envoyé');
        }

        Utils::redirectError('/messages/new', 'Formulaire invalide');
    }

    public function read(int $id): void
    {
        Security::requireAuth();
        $user = Security::getCurrentUser();

        $repo = new MessageRepository();
        $message = $repo->findById($id);

        if ($message && $message['recipient_id'] == $user->getId()) {
            $repo->markAsRead($id);
            (new View())->render('messages/read.html.twig', [
                'message' => $message,
                'user' => $user,
            ]);
        } else {
            Utils::redirectError('/messages', 'Message introuvable ou vous n\'avez pas l\'autorisation de le lire');
        }
    }

    public function delete(int $id): void
    {
        Security::requireAuth();
        $user = Security::getCurrentUser();

        $repo = new MessageRepository();
        $message = $repo->findById($id);

        if ($message && $message['recipient_id'] == $user->getId()) {
            $repo->delete($id);
            Utils::redirectSuccess('/messages', 'Message supprimé');
        } else {
            Utils::redirectError('/messages', 'Message introuvable ou vous n\'avez pas l\'autorisation de le supprimer');
        }
    }

    public function reply(int $recipientId): void
    {
        Security::requireAuth();
        $currentUser = Security::getCurrentUser();
        $recipient = (new UserRepository())->findById($recipientId);

        if (!$recipient) {
            Utils::redirectError('/messages', 'Utilisateur introuvable');
        }

        (new View())->render('messages/reply.html.twig', [
            'recipient' => $recipient,
            'sender' => $currentUser,
        ]);
    }

    public function fetch(): void
    {
        Security::requireAuth();
        $user = Security::getCurrentUser();
        $repo = new MessageRepository();
        $messages = $repo->findAllByUserId($user->getId());

        (new View())->render('messages/_messages.html.twig', [
            'messages' => $messages,
            'user' => $user,
        ]);
    }

    public function unreadCount(): void
    {
        header('Content-Type: application/json');

        try {
            $count = 0;
            if (Security::isAuthenticated()) {
                $user = Security::getCurrentUser();
                $repo = new MessageRepository();
                $count = $repo->findUnreadCountByUserId($user->getId());
            }

            echo json_encode(['count' => $count]);
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Une erreur est survenue lors du comptage des messages.']);
            // Log l'erreur si nécessaire
            error_log($e->getMessage());
        }
    }
}
