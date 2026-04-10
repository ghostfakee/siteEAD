<?php

declare(strict_types=1);

namespace App\Models;

final class NewsModel
{
    private const MAX_NEWS     = 5;
    private const ALLOWED_MIME = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    // ── Read ────────────────────────────────────────────────────────────

    public function all(): array
    {
        $pdo  = get_pdo();
        $stmt = $pdo->query(
            "SELECT id, title, excerpt, url, image_mime, image_filename,
                    published_at, featured, sort_order, created_at
             FROM news ORDER BY featured DESC, sort_order ASC, published_at DESC"
        );
        return $stmt->fetchAll() ?: [];
    }

    public function getFeatured(): ?array
    {
        $pdo  = get_pdo();
        $stmt = $pdo->query(
            "SELECT id, title, excerpt, url, image_mime, image_filename, published_at
             FROM news WHERE featured = 1 LIMIT 1"
        );
        return $stmt->fetch() ?: null;
    }

    public function getRegular(): array
    {
        $pdo  = get_pdo();
        $stmt = $pdo->query(
            "SELECT id, title, excerpt, url, image_mime, image_filename, published_at
             FROM news WHERE featured = 0 ORDER BY sort_order ASC, published_at DESC LIMIT 4"
        );
        return $stmt->fetchAll() ?: [];
    }

    public function getById(int $id): ?array
    {
        $pdo  = get_pdo();
        $stmt = $pdo->prepare(
            "SELECT id, title, excerpt, url, image_mime, image_filename,
                    published_at, featured, sort_order
             FROM news WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function count(): int
    {
        return (int) get_pdo()->query("SELECT COUNT(*) FROM news")->fetchColumn();
    }

    public function hasImage(int $id): bool
    {
        $stmt = get_pdo()->prepare("SELECT COUNT(*) FROM news WHERE id = ? AND image_data IS NOT NULL");
        $stmt->execute([$id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    // ── Write ───────────────────────────────────────────────────────────

    /**
     * @throws \RuntimeException
     */
    public function create(array $data, ?array $imageFile = null): int
    {
        if ($this->count() >= self::MAX_NEWS) {
            throw new \RuntimeException('Limite de ' . self::MAX_NEWS . ' notícias atingido. Exclua uma antes de criar outra.');
        }

        $pdo = get_pdo();
        $this->validateData($data);

        // If this will be featured, unfeature others
        if (!empty($data['featured'])) {
            $pdo->exec("UPDATE news SET featured = 0");
        }

        [$imageData, $imageMime, $imageFilename] = $this->extractImage($imageFile);

        $stmt = $pdo->prepare(
            "INSERT INTO news (title, excerpt, url, image_data, image_mime, image_filename, published_at, featured, sort_order)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['title'],
            $data['excerpt'] ?? '',
            $data['url'] ?? '',
            $imageData,
            $imageMime,
            $imageFilename,
            $data['published_at'],
            !empty($data['featured']) ? 1 : 0,
            (int) ($data['sort_order'] ?? 0),
        ]);

        $id = (int) $pdo->lastInsertId();
        audit_log('news_created', 'info', null, "Notícia ID {$id} criada: '{$data['title']}'");
        return $id;
    }

    /**
     * @throws \RuntimeException
     */
    public function update(int $id, array $data, ?array $imageFile = null): void
    {
        $pdo = get_pdo();
        $this->validateData($data);

        if (!empty($data['featured'])) {
            $pdo->prepare("UPDATE news SET featured = 0 WHERE id != ?")->execute([$id]);
        }

        $sets  = ["title = ?", "excerpt = ?", "url = ?", "published_at = ?",
                  "featured = ?", "sort_order = ?"];
        $binds = [
            $data['title'],
            $data['excerpt'] ?? '',
            $data['url'] ?? '',
            $data['published_at'],
            !empty($data['featured']) ? 1 : 0,
            (int) ($data['sort_order'] ?? 0),
        ];

        if ($imageFile && ($imageFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            [$imageData, $imageMime, $imageFilename] = $this->extractImage($imageFile);
            $sets[]  = "image_data = ?";
            $sets[]  = "image_mime = ?";
            $sets[]  = "image_filename = ?";
            $binds[] = $imageData;
            $binds[] = $imageMime;
            $binds[] = $imageFilename;
        }

        $binds[] = $id;
        $pdo->prepare("UPDATE news SET " . implode(', ', $sets) . " WHERE id = ?")->execute($binds);
        audit_log('news_updated', 'info', null, "Notícia ID {$id} atualizada");
    }

    public function delete(int $id): void
    {
        get_pdo()->prepare("DELETE FROM news WHERE id = ?")->execute([$id]);
        audit_log('news_deleted', 'warning', null, "Notícia ID {$id} excluída");
    }

    public function setFeatured(int $id): void
    {
        $pdo = get_pdo();
        $pdo->exec("UPDATE news SET featured = 0");
        $pdo->prepare("UPDATE news SET featured = 1 WHERE id = ?")->execute([$id]);
    }

    // ── Private ─────────────────────────────────────────────────────────

    private function validateData(array $data): void
    {
        if (trim($data['title'] ?? '') === '') {
            throw new \RuntimeException('Título é obrigatório.');
        }
        if (trim($data['published_at'] ?? '') === '') {
            throw new \RuntimeException('Data de publicação é obrigatória.');
        }
    }

    private function extractImage(?array $file): array
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return [null, null, null];
        }

        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new \RuntimeException('Imagem muito grande. Máximo 5 MB.');
        }

        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED_MIME, true)) {
            throw new \RuntimeException('Formato inválido. Use JPG, PNG, WebP ou GIF.');
        }

        return [
            file_get_contents($file['tmp_name']),
            $mime,
            basename((string) $file['name']),
        ];
    }
}
