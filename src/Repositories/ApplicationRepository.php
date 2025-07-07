<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ApplicationRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // 📥 Enregistre une nouvelle candidature
    public function create(int $userId, string $class, string $spec, string $playtime, string $availability, string $motivation): void {
        $stmt = $this->db->prepare("
        INSERT INTO applications (user_id, class, specialization, playtime, availability, motivation, status, submitted_at)
        VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
        $stmt->execute([$userId, $class, $spec, $playtime, $availability, $motivation]);
    }


    // 📋 Récupère toutes les candidatures (avec filtre éventuel)
    public function findAll(?string $filterStatus = null): array {
        if ($filterStatus && in_array($filterStatus, ['pending', 'accepted', 'refused'])) {
            $stmt = $this->db->prepare("
                SELECT a.*, u.username 
                FROM applications a
                JOIN users u ON a.user_id = u.id
                WHERE a.status = :status
                ORDER BY a.submitted_at DESC
            ");
            $stmt->execute(['status' => $filterStatus]);
        } else {
            $stmt = $this->db->prepare("
                SELECT a.*, u.username 
                FROM applications a
                JOIN users u ON a.user_id = u.id
                ORDER BY a.submitted_at DESC
            ");
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🗑️ Supprime une candidature
    public function delete(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM applications WHERE id = ?");
        $stmt->execute([$id]);
    }

    // 🔄 Met à jour le statut d'une candidature (accepted, refused, pending)
    public function updateStatus(int $applicationId, string $status): bool {
        $allowedStatuses = ['pending', 'accepted', 'refused'];

        if (!in_array($status, $allowedStatuses, true)) {
            throw new \InvalidArgumentException("Statut invalide : $status");
        }

        // 🔍 Récupère l'application pour savoir quel utilisateur est concerné
        $stmt = $this->db->prepare("SELECT user_id FROM applications WHERE id = ?");
        $stmt->execute([$applicationId]);
        $application = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$application) {
            return false;
        }

        $userId = $application['user_id'];

        if ($status === 'accepted') {
            // 🔄 Accepte la candidature + marque le joueur comme "dans la guilde"
            $this->db->beginTransaction();

            try {
                // 1. Met à jour la candidature
                $stmt1 = $this->db->prepare("
                UPDATE applications
                SET status = 'accepted', has_joined_guild = 1
                WHERE id = ?
            ");
                $stmt1->execute([$applicationId]);

                // 2. Met à jour le rôle de l'utilisateur
                $stmt2 = $this->db->prepare("
                UPDATE users
                SET role = 'recrue'
                WHERE id = ?
            ");
                $stmt2->execute([$userId]);

                $this->db->commit();
                return true;
            } catch (\Exception $e) {
                $this->db->rollBack();
                return false;
            }
        } else {
            // Pour refusé ou en attente, on ne touche pas à has_joined_guild ni au rôle
            $stmt = $this->db->prepare("UPDATE applications SET status = :status WHERE id = :id");
            return $stmt->execute([
                'status' => $status,
                'id' => $applicationId
            ]);
        }
    }



    // 🔍 Récupère les candidatures paginées
    public function findPaginated(int $limit, int $offset): array {
        $stmt = $this->db->prepare("
        SELECT a.*, u.username
        FROM applications a
        JOIN users u ON a.user_id = u.id
        ORDER BY a.submitted_at DESC
        LIMIT :limit OFFSET :offset
    ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// 🔢 Compte total des candidatures
    public function countAll(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM applications");
        return (int) $stmt->fetchColumn();
    }

    // 🔍 Récupère les candidatures filtrées + paginées
    public function findAllFiltered(?string $status, int $limit, int $offset): array {
        if ($status) {
            $stmt = $this->db->prepare("
            SELECT a.*, u.username
            FROM applications a
            JOIN users u ON a.user_id = u.id
            WHERE a.status = :status
            ORDER BY a.submitted_at DESC
            LIMIT :limit OFFSET :offset
        ");
            $stmt->bindValue(':status', $status);
        } else {
            $stmt = $this->db->prepare("
            SELECT a.*, u.username
            FROM applications a
            JOIN users u ON a.user_id = u.id
            ORDER BY a.submitted_at DESC
            LIMIT :limit OFFSET :offset
        ");
        }

        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

// 📊 Compte total des candidatures selon filtre
    public function countFiltered(?string $status): int {
        if ($status) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM applications WHERE status = :status");
            $stmt->execute(['status' => $status]);
        } else {
            $stmt = $this->db->query("SELECT COUNT(*) FROM applications");
        }
        return (int)($status ? $stmt->fetchColumn() : $stmt->fetchColumn());
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
        SELECT a.*, u.username
        FROM applications a
        JOIN users u ON a.user_id = u.id
        WHERE a.id = ?
    ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function hasJoinedGuild(int $userId): bool {
        $stmt = $this->db->prepare("
        SELECT COUNT(*) FROM applications
        WHERE user_id = ? AND has_joined_guild = 1
    ");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() > 0;
    }

}
