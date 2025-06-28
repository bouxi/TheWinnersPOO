<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Message;
use App\Repositories\UserRepository;
use PDO;

class MessageRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAllByUserId(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM messages WHERE recipient_id = :id ORDER BY created_at DESC');
        $stmt->execute(['id' => $userId]);

        $messages = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = $this->mapToMessage($row);
        }

        return $messages;
    }

    public function findUnreadCountByUserId(int $userId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM messages WHERE recipient_id = :id AND is_read = 0');
        $stmt->execute(['id' => $userId]);

        return (int)$stmt->fetchColumn();
    }

    public function sendMessage(int $senderId, int $recipientId, string $content): bool
    {
        $stmt = $this->db->prepare('
            INSERT INTO messages (sender_id, recipient_id, content, is_read, created_at)
            VALUES (:sender_id, :recipient_id, :content, 0, NOW())
        ');

        return $stmt->execute([
            'sender_id' => $senderId,
            'recipient_id' => $recipientId,
            'content' => $content
        ]);
    }

    public function markAsRead(int $messageId): bool
    {
        $stmt = $this->db->prepare('UPDATE messages SET is_read = 1 WHERE id = :id');
        return $stmt->execute(['id' => $messageId]);
    }

    public function delete(int $messageId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM messages WHERE id = :id');
        return $stmt->execute(['id' => $messageId]);
    }

    private function mapToMessage(array $data): Message
    {
        $userRepo = new UserRepository();
        $sender = $userRepo->findById($data['sender_id']);

        return new Message(
            $data['id'],
            $sender, // ici : objet User et plus l’ID brut
            (int) $data['recipient_id'],
            $data['content'],
            new \DateTime($data['created_at']),
            (bool) $data['is_read'],
        );
    }

    public function findById(int $id): ?Message
    {
        $stmt = $this->db->prepare('SELECT * FROM messages WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToMessage($data) : null;
    }


    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM messages ORDER BY created_at DESC');

        $messages = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = $this->mapToMessage($row);
        }

        return $messages;
    }

}
