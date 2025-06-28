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

        $repo = new MessageRepository();
        $messages = $repo->findAllByUserId($user->getId());

        (new View())->render('messages/index.html.twig', [
            'messages' => $messages,
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

        $recipientId = (int)$_POST['recipient_id'] ?? 0;
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
        $repo = new MessageRepository();
        $message = $repo->findById($id);

        if ($message) {
            $repo->markAsRead($id);
            (new View())->render('messages/read.html.twig', [
                'message' => $message,
            ]);
        } else {
            Utils::redirectError('/messages', 'Message introuvable');
        }
    }

    public function delete(int $id): void
    {
        Security::requireAuth();
        (new MessageRepository())->delete($id);
        Utils::redirectSuccess('/messages', 'Message supprimé');
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
        ]);
    }

    public function unreadCount(): void
    {
        header('Content-Type: application/json');

        $count = 0;
        if (\App\Core\Security::isAuthenticated()) {
            $user = \App\Core\Security::getCurrentUser();
            $repo = new \App\Repositories\MessageRepository();
            $count = $repo->findUnreadCountByUserId($user->getId());
        }

        echo json_encode(['count' => $count]);
    }



}
