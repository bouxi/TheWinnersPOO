<?php

namespace App\Models;

class User {
    private int $id;
    private string $username;
    private string $email;
    private string $password;
    private string $role;

    public function __construct(string $username, string $email, string $password, string $role = 'member') {
        $this->username = $username;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        $this->role = $role;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->password);
    }

    public function setPassword(string $password): void {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    public function setRole(string $role): void {
        $this->role = $role;
    }

    public function getPassword(): string {
        return $this->password;
    }

}
