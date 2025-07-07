<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Message;
use PDO;

class MessageRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // 📨 Envoi de message
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

    // ✅ Lecture d’un message par ID
    public function findById(int $id): ?Message
    {
        $stmt = $this->db->prepare('SELECT * FROM messages WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToMessage($data) : null;
    }

    // ✅ Tous les messages reçus d’un utilisateur
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

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM messages WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function markAsRead(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE messages SET is_read = 1 WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function countAllUnreadMessages(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM messages WHERE is_read = 0");
        return (int) $stmt->fetchColumn();
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

    // ✅ Hydratation : créer un objet Message avec un User (expéditeur complet)
    private function mapToMessage(array $data): Message
    {
        $userRepo = new UserRepository();
        $sender = $userRepo->findById((int)$data['sender_id']);

        return new Message(
            (int)$data['id'],
            $sender,
            (int)$data['recipient_id'],
            $data['content'],
            new \DateTime($data['created_at']),
            (bool)$data['is_read'],
        );
    }

    public function findPaginatedByUserId(int $userId, int $limit, int $offset): array
    {
        $stmt = $this->db->prepare("
        SELECT * FROM messages
        WHERE recipient_id = :id
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ");
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $messages = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = $this->mapToMessage($row);
        }

        return $messages;
    }

    public function countByUserId(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM messages WHERE recipient_id = :id");
        $stmt->execute(['id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public function findPaginatedWithSearch(int $limit, int $offset, ?string $search = null, string $sort = 'created_at', string $order = 'DESC'): array
    {
        $allowedSorts = ['created_at', 'is_read'];
        $allowedOrders = ['ASC', 'DESC'];

        if (!in_array($sort, $allowedSorts)) $sort = 'created_at';
        if (!in_array(strtoupper($order), $allowedOrders)) $order = 'DESC';

        $sql = "
        SELECT m.id, m.content, m.created_at, m.is_read,
               u1.username AS senderUsername,
               u2.username AS recipientUsername
        FROM messages m
        LEFT JOIN users u1 ON m.sender_id = u1.id
        LEFT JOIN users u2 ON m.recipient_id = u2.id
    ";

        $params = [];

        if ($search) {
            $sql .= " WHERE (m.content LIKE :search1 OR u1.username LIKE :search2 OR u2.username LIKE :search3) ";
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
            $params[':search3'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY $sort $order LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        if ($search) {
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val, PDO::PARAM_STR);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAllWithSearch(?string $search = null): int
    {
        $sql = "
        SELECT COUNT(*) FROM messages m
        LEFT JOIN users u1 ON m.sender_id = u1.id
        LEFT JOIN users u2 ON m.recipient_id = u2.id
    ";

        if ($search) {
            $sql .= " WHERE (m.content LIKE :search1 OR u1.username LIKE :search2 OR u2.username LIKE :search3) ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':search1', '%' . $search . '%', PDO::PARAM_STR);
            $stmt->bindValue(':search2', '%' . $search . '%', PDO::PARAM_STR);
            $stmt->bindValue(':search3', '%' . $search . '%', PDO::PARAM_STR);
            $stmt->execute();
        } else {
            $stmt = $this->db->query($sql);
        }

        return (int) $stmt->fetchColumn();
    }


}
