<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ClassGuideRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Récupère tous les guides groupés par classe
     */
    public function getAllGroupedByClass(): array
    {
        $stmt = $this->db->query("
            SELECT class_name, spec_name, slug, role, icon_path
            FROM class_guides
            ORDER BY class_name, spec_name
        ");

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $grouped = [];

        foreach ($results as $row) {
            $class = $row['class_name'];
            $grouped[$class][] = [
                'spec' => $row['spec_name'],
                'role' => $row['role'],
                'slug' => $row['slug'],
                'icon_path' => $row['icon_path'],
            ];
        }

        return $grouped;
    }

    /**
     * Récupère un guide à partir du slug combiné class-spec
     */
    public function findBySlug(string $class, string $spec): ?array
    {
        // Le slug est stocké dans une seule colonne `slug` comme "chaman-restauration"
        $combinedSlug = strtolower(trim($class)) . '-' . strtolower(trim($spec));

        $stmt = $this->db->prepare("SELECT * FROM class_guides WHERE slug = :slug");
        $stmt->bindParam(':slug', $combinedSlug);
        $stmt->execute();

        $guide = $stmt->fetch(PDO::FETCH_ASSOC);
        return $guide ?: null;
    }

    /**
     * Récupère un guide via son ID
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM class_guides WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Met à jour un guide
     */
    public function update(int $id, string $class, string $spec, string $content, ?string $talentImage = null): void
    {
        $slug = strtolower(trim($class)) . '-' . strtolower(trim($spec));

        $stmt = $this->db->prepare("
        UPDATE class_guides
        SET class_name = ?, spec_name = ?, slug = ?, content = ?, talent_tree_image = ?, updated_at = NOW()
        WHERE id = ?
    ");
        $stmt->execute([$class, $spec, $slug, $content, $talentImage, $id]);
    }


    /**
     * Récupère tous les guides
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM class_guides ORDER BY class_name ASC, spec_name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouveau guide
     */
    public function create(string $class, string $spec, string $content, ?string $talentImage = null): void
    {
        $slug = strtolower(trim($class)) . '-' . strtolower(trim($spec));

        $stmt = $this->db->prepare("
        INSERT INTO class_guides (class_name, spec_name, slug, content, talent_tree_image, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
        $stmt->execute([$class, $spec, $slug, $content, $talentImage]);
    }

}
