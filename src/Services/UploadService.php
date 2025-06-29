<?php

namespace App\Services;

class UploadService
{
    public const MAX_UPLOAD_SIZE_MB = 2; // 2 Mo
    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif'];
    public const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif'];

    /**
     * Upload une image avec contrôle des extensions, taille et MIME
     *
     * @param array $file Données issues de $_FILES['...']
     * @param string $targetDir Dossier cible (ex: 'public/uploads/avatars')
     * @param string|null $oldFilePath Ancien fichier à supprimer (chemin complet)
     * @return string|null Nom du fichier sauvegardé, ou null en cas d'erreur
     */
    public static function uploadImage(array $file, string $targetDir, ?string $oldFilePath = null): ?string
    {
        if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            return null;
        }

        if ($file['size'] > self::MAX_UPLOAD_SIZE_MB * 1024 * 1024) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            return null;
        }

        // Créer le dossier cible si nécessaire
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Supprimer l'ancien fichier si présent
        if ($oldFilePath && file_exists($oldFilePath)) {
            unlink($oldFilePath);
        }

        // Générer un nom de fichier unique
        $filename = uniqid('img_', true) . '.' . $ext;
        $destination = rtrim($targetDir, '/') . '/' . $filename;

        return move_uploaded_file($file['tmp_name'], $destination) ? $filename : null;
    }
}
