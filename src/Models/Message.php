<?php
// src/Models/Message.php

namespace App\Models;

class Message
{
    public function __construct(
        private int $id,
        private User $sender,
        private int $recipientId,
        private string $content,
        private \DateTime $sentAt,
        private bool $isRead = false
    ) {}

    // ✅ Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getSender(): User
    {
        return $this->sender;
    }

    public function getRecipientId(): int
    {
        return $this->recipientId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getSentAt(): \DateTime
    {
        return $this->sentAt;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    // 🆕 Obtenir le nom d'utilisateur de l'expéditeur
    public function getSenderUsername(): string
    {
        return $this->sender->getUsername();
    }

    public function getSenderId(): int
    {
        return $this->sender->getId();
    }


    // ❌ Plus besoin de setters dans ce cas si l’objet est immuable après création.
    // ➕ Si tu veux modifier le message plus tard, on pourra ajouter des setters.
}
