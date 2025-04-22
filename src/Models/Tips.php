<?php

namespace App\Models;

class Tips {
    private int $id;
    private string $title;
    private string $category;
    private string $content;
    private int $id_author;
    private string $created_at;

    // Getters
    public function getId(): int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getCategory(): string { return $this->category; }
    public function getContent(): string { return $this->content; }
    public function getAuthorId(): int { return $this->id_author; }
    public function getCreatedAt(): string { return $this->created_at; }

    // Setters
    public function setTitle(string $title): void { $this->title = $title; }
    public function setCategory(string $category): void { $this->category = $category; }
    public function setContent(string $content): void { $this->content = $content; }
    public function setAuthorId(int $id_author): void { $this->id_author = $id_author; }
}
