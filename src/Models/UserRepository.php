<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class UserRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function save(User $user): bool {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
        return $stmt->execute([
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(), // Cryptage du mot de passe
            'role' => $user->getRole()
        ]);
    }

    public function findByUsername(string $username): ?User {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new User($data['username'], $data['email'], $data['password'], $data['role']);
        }

        return null;
    }
}
