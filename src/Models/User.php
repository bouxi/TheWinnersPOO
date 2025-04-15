<?php

namespace App\Models;

class User {
    private int $id;
    private string $username;
    private string $email;
    private string $password;
    private string $role;

    // Rôles disponibles
    public const ROLE_ADMIN = 'admin';           // Maitre de Guilde (Admin)
    public const ROLE_GUILD_MASTER = 'guild_master'; // Maître de Guilde
    public const ROLE_OFFICER = 'officer';       // Officier
    public const ROLE_VETERAN = 'veteran';       // Vétéran
    public const ROLE_MEMBER = 'member';         // Membre
    public const ROLE_RECRUIT = 'recruit';       // Recrue
    public const ROLE_VISITOR = 'visitor';       // Visiteur
    public const ROLE_APPLICANT = 'applicant';   // Postulant

    public function __construct(string $username, string $email, string $password, string $role = self::ROLE_VISITOR, bool $hashed = false, int $id = 0) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $hashed ? $password : password_hash($password, PASSWORD_BCRYPT);
        $this->role = $role;
    }
    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getRole(): string {
        return $this->role;
    }

    // Setters
    public function setUsername(string $username): void {
        $this->username = $username;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setPassword(string $password): void {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    public function setRole(string $role): void {
        $this->role = $role;
    }

    // Vérification du mot de passe
    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->password);
    }

    // Vérification des rôles
    public function hasRole(string $role): bool {
        return $this->role === $role;
    }

    public function isAdmin(): bool {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isGuildMaster(): bool {
        return $this->role === self::ROLE_GUILD_MASTER;
    }

    public function isOfficer(): bool {
        return $this->role === self::ROLE_OFFICER;
    }

    public function isVeteran(): bool {
        return $this->role === self::ROLE_VETERAN;
    }

    public function isMember(): bool {
        return $this->role === self::ROLE_MEMBER;
    }

    public function isRecruit(): bool {
        return $this->role === self::ROLE_RECRUIT;
    }

    public function isVisitor(): bool {
        return $this->role === self::ROLE_VISITOR;
    }

    public function isApplicant(): bool {
        return $this->role === self::ROLE_APPLICANT;
    }
}
