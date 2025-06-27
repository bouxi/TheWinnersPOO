<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByUsername(string $username): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToUser($data) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToUser($data) : null;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->mapToUser($data) : null;
    }

    public function existsByEmail(string $email): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function save(User $user): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (username, email, password, role, birthdate, avatar, date_inscription)
            VALUES (:username, :email, :password, :role, :birthdate, :avatar, :date_inscription)
        ");

        return $stmt->execute([
            'username'          => $user->getUsername(),
            'email'             => $user->getEmail(),
            'password'          => $user->getPassword(),
            'role'              => $user->getRole(),
            'birthdate'         => $user->getBirthdate(),
            'avatar'            => $user->getAvatar(),
            'date_inscription'  => $user->getDateInscription(),
        ]);
    }

    public function update(User $user): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users SET
                username = :username,
                email = :email,
                password = :password,
                birthdate = :birthdate,
                avatar = :avatar,
                role = :role
            WHERE id = :id
        ");

        return $stmt->execute([
            'username'    => $user->getUsername(),
            'email'     => $user->getEmail(),
            'password'  => $user->getPassword(),
            'birthdate' => $user->getBirthdate(),
            'avatar'    => $user->getAvatar(),
            'role'      => $user->getRole(),
            'id'        => $user->getId(),
        ]);
    }

    private function mapToUser(array $data): User
    {
        $user = new User(
            $data['username'],
            $data['email'],
            $data['password'],
            $data['role'],
            (bool) ($data['is_verified'] ?? true),
            (int) $data['id'] ?? null
        );

        $user->setAvatar($data['avatar'] ?? null);
        $user->setBirthdate($data['birthdate'] ?? null);
        $user->setDateInscription($data['date_inscription'] ?? null);

        return $user;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'birthdate' => $this->birthdate,
            'avatar' => $this->avatar,
            'role' => $this->role,
            'date_inscription' => $this->dateInscription,
            'isHashed' => true,
        ];
    }

}
