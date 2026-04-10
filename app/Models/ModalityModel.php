<?php

declare(strict_types=1);

namespace App\Models;

final class ModalityModel
{
    private const MAX_CARDS    = 4;
    private const ALLOWED_MIME = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    // ── Read ────────────────────────────────────────────────────────────

    public function all(): array
    {
        $stmt = get_pdo()->query(
            "SELECT id, title, content, cta_label, cta_url,
                    image_mime, image_filename, sort_order, active, created_at
             FROM modalities ORDER BY sort_order ASC, id ASC"
        );
        return $stmt->fetchAll() ?: [];
    }

    public function active(): array
    {
        $stmt = get_pdo()->query(
            "SELECT id, title, content, cta_label, cta_url,
                    image_mime, image_filename, sort_order
             FROM modalities WHERE active = 1
             ORDER BY sort_order ASC, id ASC LIMIT 4"
        );
        return $stmt->fetchAll() ?: [];
    }

    public function getById(int $id): ?array
    {
        $stmt = get_pdo()->prepare(
            "SELECT id, title, content, cta_label, cta_url,
                    image_mime, image_filename, sort_order, active
             FROM modalities WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function count(): int
    {
        return (int) get_pdo()->query("SELECT COUNT(*) FROM modalities")->fetchColumn();
    }

    public function hasImage(int $id): bool
    {
        $stmt = get_pdo()->prepare("SELECT COUNT(*) FROM modalities WHERE id = ? AND image_data IS NOT NULL");
        $stmt->execute([$id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    // ── Write ───────────────────────────────────────────────────────────

    /** @throws \RuntimeException */
    public function create(array $data, ?array $imageFile = null): int
    {
        if ($this->count() >= self::MAX_CARDS) {
            throw new \RuntimeException('Limite de ' . self::MAX_CARDS . ' modalidades atingido. Exclua uma antes de criar outra.');
        }
        $this->validateData($data);
        [$imgData, $imgMime, $imgFilename] = $this->extractImage($imageFile);

        $pdo  = get_pdo();
        $stmt = $pdo->prepare(
            "INSERT INTO modalities (title, content, cta_label, cta_url, image_data, image_mime, image_filename, sort_order, active)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['title'],
            $data['content']   ?? '',
            $data['cta_label'] ?? '',
            $data['cta_url']   ?? '',
            $imgData,
            $imgMime,
            $imgFilename,
            (int) ($data['sort_order'] ?? $this->count()),
            isset($data['active']) ? (int) $data['active'] : 1,
        ]);

        $id = (int) $pdo->lastInsertId();
        audit_log('modality_created', 'info', null, "Modalidade ID {$id} criada: '{$data['title']}'");
        return $id;
    }

    /** @throws \RuntimeException */
    public function update(int $id, array $data, ?array $imageFile = null): void
    {
        $this->validateData($data);

        $sets  = ["title = ?", "content = ?", "cta_label = ?", "cta_url = ?",
                  "sort_order = ?", "active = ?"];
        $binds = [
            $data['title'],
            $data['content']   ?? '',
            $data['cta_label'] ?? '',
            $data['cta_url']   ?? '',
            (int) ($data['sort_order'] ?? 0),
            isset($data['active']) ? (int) $data['active'] : 1,
        ];

        if ($imageFile && ($imageFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            [$imgData, $imgMime, $imgFilename] = $this->extractImage($imageFile);
            $sets[]  = "image_data = ?";
            $sets[]  = "image_mime = ?";
            $sets[]  = "image_filename = ?";
            $binds[] = $imgData;
            $binds[] = $imgMime;
            $binds[] = $imgFilename;
        }

        $binds[] = $id;
        get_pdo()->prepare("UPDATE modalities SET " . implode(', ', $sets) . " WHERE id = ?")->execute($binds);
        audit_log('modality_updated', 'info', null, "Modalidade ID {$id} atualizada");
    }

    public function delete(int $id): void
    {
        get_pdo()->prepare("DELETE FROM modalities WHERE id = ?")->execute([$id]);
        audit_log('modality_deleted', 'warning', null, "Modalidade ID {$id} excluída");
    }

    // ── Private ─────────────────────────────────────────────────────────

    private function validateData(array $data): void
    {
        if (trim($data['title'] ?? '') === '') {
            throw new \RuntimeException('Título é obrigatório.');
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
