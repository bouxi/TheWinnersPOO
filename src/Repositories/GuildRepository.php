<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class GuildRepository
{
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findAllMembers(?string $class, ?string $role, string $sort, string $order, int $limit, int $offset): array
    {
        $validSorts = ['username', 'class', 'role'];
        $validOrders = ['asc', 'desc'];

        $sort = in_array($sort, $validSorts) ? $sort : 'username';
        $order = in_array(strtolower($order), $validOrders) ? strtoupper($order) : 'ASC';

        $sql = "
            SELECT u.username, u.role, u.avatar, a.class, a.specialization
            FROM users u
            JOIN applications a ON u.id = a.user_id
            WHERE a.has_joined_guild = 1
        ";

        $params = [];

        if ($class) {
            $sql .= " AND a.class = :class";
            $params['class'] = $class;
        }

        if ($role) {
            $sql .= " AND u.role = :role";
            $params['role'] = $role;
        }

        $sql .= " ORDER BY $sort $order LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAllMembers(?string $class, ?string $role): int
    {
        $sql = "
            SELECT COUNT(*) 
            FROM users u
            JOIN applications a ON u.id = a.user_id
            WHERE a.has_joined_guild = 1
        ";

        $params = [];

        if ($class) {
            $sql .= " AND a.class = :class";
            $params['class'] = $class;
        }

        if ($role) {
            $sql .= " AND u.role = :role";
            $params['role'] = $role;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function getAvailableClasses(): array
    {
        $stmt = $this->db->query("SELECT DISTINCT class FROM applications WHERE has_joined_guild = 1 ORDER BY class");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getAvailableRoles(): array
    {
        $stmt = $this->db->query("SELECT DISTINCT role FROM users ORDER BY role");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
