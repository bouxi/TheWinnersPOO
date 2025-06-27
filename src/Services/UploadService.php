<?php

namespace App\Services;

class UploadService
{
    public const MAX_UPLOAD_SIZE_MB = 2; // 2 Mo
    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif'];
    public const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif'];

    public static function uploadImage(array $file, string $targetDir, ?string $oldFilePath = null): ?string
    {
        if (!isset($file['name']) || empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXTENSIONS)) {
            return null;
        }

        if ($file['size'] > self::MAX_UPLOAD_SIZE_MB * 1024 * 1024) {
            return null;
        }

        // Vérification du type MIME réel
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            return null;
        }

        // Créer le dossier si nécessaire
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Supprimer l'ancien fichier si demandé
        if ($oldFilePath && file_exists($oldFilePath)) {
            unlink($oldFilePath);
        }

        // Générer un nom de fichier unique
        $filename = uniqid('img_') . '.' . $ext;
        $destination = rtrim($targetDir, '/') . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }

        return null;
    }
}
