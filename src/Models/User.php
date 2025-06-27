<?php

namespace App\Models;

class User
{
    private ?int $id = null;
    private string $username;
    private string $email;
    private string $password;
    private string $role;
    private ?string $birthdate = null;
    private ?string $dateInscription = null;
    private ?string $avatar = null;
    private bool $isHashed;

    public function __construct(
        string $username,
        string $email,
        string $password,
        string $role = 'member',
        bool $isHashed = false,
        ?int $id = null
    ) {
        $this->username = $username;
        $this->email = $email;
        $this->isHashed = $isHashed;
        $this->password = $isHashed ? $password : password_hash($password, PASSWORD_DEFAULT);
        $this->role = $role;
        $this->id = $id;
    }

    // Getters / Setters
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getUsername(): string { return $this->username; }
    public function setUsername(string $username): void { $this->username = $username; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): void {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $plain): bool {
        return password_verify($plain, $this->password);
    }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): void { $this->role = $role; }

    public function getBirthdate(): ?string { return $this->birthdate; }
    public function setBirthdate(?string $birthdate): void { $this->birthdate = $birthdate; }

    public function getDateInscription(): ?string { return $this->dateInscription; }
    public function setDateInscription(?string $dateInscription): void { $this->dateInscription = $dateInscription; }

    public function getAvatar(): ?string { return $this->avatar; }
    public function setAvatar(?string $avatar): void { $this->avatar = $avatar; }

    // Rôle helpers
    public function isAdmin(): bool         { return $this->role === 'admin'; }
    public function isGuildMaster(): bool   { return $this->role === 'guildmaster'; }
    public function isOfficer(): bool       { return $this->role === 'officer'; }
    public function isVeteran(): bool       { return $this->role === 'veteran'; }
    public function isMember(): bool        { return $this->role === 'member'; }
    public function isRecruit(): bool       { return $this->role === 'recruit'; }
    public function isVisitor(): bool       { return $this->role === 'visitor'; }
    public function isApplicant(): bool     { return $this->role === 'applicant'; }

    public function __toString(): string
    {
        return $this->username . ' (' . $this->email . ')';
    }
}
