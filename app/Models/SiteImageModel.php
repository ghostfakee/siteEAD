<?php

declare(strict_types=1);

namespace App\Models;

final class SiteImageModel
{
    public function exists(string $key): bool
    {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM site_images WHERE image_key = ?");
            $stmt->execute([$key]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    public function getMeta(string $key): ?array
    {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare(
                "SELECT image_key, image_mime, image_filename, image_size, updated_at FROM site_images WHERE image_key = ?"
            );
            $stmt->execute([$key]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Save an uploaded image to the database.
     * @param array $file  $_FILES entry
     * @param string $key  logical key (e.g. 'scholarships_cover')
     * @throws \RuntimeException on invalid format or DB failure
     */
    public function saveUpload(array $file, string $key): void
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Erro no upload do arquivo.');
        }

        $maxBytes = 5 * 1024 * 1024; // 5 MB
        if (($file['size'] ?? 0) > $maxBytes) {
            throw new \RuntimeException('Imagem muito grande. Máximo 5 MB.');
        }

        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        $mime    = mime_content_type($file['tmp_name']);
        // Browsers often send SVG as text/plain or text/xml — normalise
        if ($mime === 'text/plain' || $mime === 'text/xml') {
            $peek = file_get_contents($file['tmp_name'], false, null, 0, 256);
            if (str_contains($peek, '<svg') || str_contains($peek, '<?xml')) {
                $mime = 'image/svg+xml';
            }
        }

        if (!in_array($mime, $allowed, true)) {
            throw new \RuntimeException('Formato inválido. Use JPG, PNG, WebP ou GIF.');
        }

        $data     = file_get_contents($file['tmp_name']);
        $filename = basename((string) $file['name']);
        $size     = strlen($data);

        $pdo  = get_pdo();
        $stmt = $pdo->prepare(
            "INSERT INTO site_images (image_key, image_data, image_mime, image_filename, image_size)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                image_data=VALUES(image_data), image_mime=VALUES(image_mime),
                image_filename=VALUES(image_filename), image_size=VALUES(image_size)"
        );
        $stmt->execute([$key, $data, $mime, $filename, $size]);

        audit_log('site_image_upload', 'info', null,
            "Imagem '{$key}' salva no banco",
            ['filename' => $filename, 'mime' => $mime, 'bytes' => $size]
        );
    }

    public function delete(string $key): void
    {
        try {
            $pdo = get_pdo();
            $pdo->prepare("DELETE FROM site_images WHERE image_key = ?")->execute([$key]);
            audit_log('site_image_delete', 'info', null, "Imagem '{$key}' removida");
        } catch (\Throwable) { /* silent */ }
    }
}
