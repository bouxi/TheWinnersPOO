<?php
namespace App\Models;

class User {
    private int $id;
    private string $username;
    private string $password;
    private string $role;

    public function __construct(string $username, string $password, string $role) {
        $this->username = $username;
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        $this->role = $role;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->password);
    }
}
