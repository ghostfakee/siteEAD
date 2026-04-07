<?php

declare(strict_types=1);

namespace App\Models;

final class ManualModel
{
    // ── Page config ──────────────────────────────────────────────────────────

    public function getPageConfig(): array
    {
        $defaults = [
            'title'      => 'Manuais e Tutoriais',
            'intro'      => 'Selecione a categoria de manuais abaixo',
            'hero_image' => '',
            'hero_logo'  => '',
        ];

        try {
            $pdo  = get_pdo();
            $stmt = $pdo->query("SELECT config_key, config_value FROM manual_page_config");
            $rows = $stmt->fetchAll();
            foreach ($rows as $row) {
                $defaults[$row['config_key']] = (string) $row['config_value'];
            }
        } catch (\Throwable) { /* use defaults */ }

        return $defaults;
    }

    public function savePageConfig(array $config): void
    {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare(
                "INSERT INTO manual_page_config (config_key, config_value) VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE config_value = ?"
            );
            foreach ($config as $key => $value) {
                $stmt->execute([$key, $value, $value]);
            }
        } catch (\Throwable) { /* silent */ }
    }

    // ── Categories ───────────────────────────────────────────────────────────

    public function getCategories(): array
    {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->query(
                "SELECT * FROM manual_categories ORDER BY sort_order ASC"
            );
            return $stmt->fetchAll() ?: [];
        } catch (\Throwable) {
            return [];
        }
    }

    public function saveCategoryConfig(string $key, array $config): void
    {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare(
                "UPDATE manual_categories SET title=?, manuals_title=?, videos_title=? WHERE category_key=?"
            );
            $stmt->execute([
                $config['title']         ?? '',
                $config['manuals_title'] ?? '',
                $config['videos_title']  ?? '',
                $key,
            ]);
        } catch (\Throwable) { /* silent */ }
    }

    // ── Manuals ──────────────────────────────────────────────────────────────

    public function getByCategory(string $category): array
    {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare(
                "SELECT id, category, title, description, image_mime, image_filename,
                        media_type, pdf_url, pdf_mime, pdf_filename, video_url, sort_order, created_at
                 FROM manuals WHERE category = ? ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute([$category]);
            return $stmt->fetchAll() ?: [];
        } catch (\Throwable) {
            return [];
        }
    }

    public function getById(int $id): ?array
    {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare(
                "SELECT id, category, title, description, image_mime, image_filename,
                        media_type, pdf_url, video_url, sort_order
                 FROM manuals WHERE id = ?"
            );
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function getImage(int $id): ?array
    {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare(
                "SELECT image_data, image_mime, image_filename FROM manuals WHERE id = ? AND image_data IS NOT NULL"
            );
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function hasImage(int $id): bool
    {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM manuals WHERE id = ? AND image_data IS NOT NULL"
            );
            $stmt->execute([$id]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    public function hasPdf(int $id): bool
    {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM manuals WHERE id = ? AND pdf_data IS NOT NULL"
            );
            $stmt->execute([$id]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    /** @param array|null $imageFile $_FILES['image'] entry, @param array|null $pdfFile $_FILES['pdf'] entry */
    public function create(array $data, ?array $imageFile = null, ?array $pdfFile = null): int
    {
        $pdo = get_pdo();
        $img = $this->resolveImage($imageFile);
        $pdf = $this->resolvePdf($pdfFile);

        // If a PDF file was uploaded, it takes priority over typed URL
        $pdfUrl = $pdf['data'] !== null ? '' : ($data['pdf_url'] ?? '');

        $stmt = $pdo->prepare(
            "INSERT INTO manuals
                (category, title, description, image_data, image_mime, image_filename,
                 media_type, pdf_url, pdf_data, pdf_mime, pdf_filename, video_url, sort_order)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['category']    ?? '',
            $data['title']       ?? '',
            $data['description'] ?? '',
            $img['data'],
            $img['mime'],
            $img['filename'],
            $data['media_type']  ?? 'pdf',
            $pdfUrl,
            $pdf['data'],
            $pdf['mime'],
            $pdf['filename'],
            $data['video_url']   ?? '',
            (int) ($data['sort_order'] ?? 0),
        ]);

        return (int) $pdo->lastInsertId();
    }

    /** @param array|null $imageFile $_FILES['image'] entry, @param array|null $pdfFile $_FILES['pdf'] entry */
    public function update(int $id, array $data, ?array $imageFile = null, ?array $pdfFile = null): void
    {
        $pdo = get_pdo();
        $img = ($imageFile !== null && ($imageFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK)
            ? $this->resolveImage($imageFile)
            : null;
        $pdf = ($pdfFile !== null && ($pdfFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK)
            ? $this->resolvePdf($pdfFile)
            : null;

        // Build SET clause dynamically
        $sets   = ['title=?', 'description=?', 'media_type=?', 'video_url=?', 'sort_order=?'];
        $params = [
            $data['title']       ?? '',
            $data['description'] ?? '',
            $data['media_type']  ?? 'pdf',
            $data['video_url']   ?? '',
            (int) ($data['sort_order'] ?? 0),
        ];

        if ($img !== null) {
            $sets[]   = 'image_data=?';
            $sets[]   = 'image_mime=?';
            $sets[]   = 'image_filename=?';
            $params[] = $img['data'];
            $params[] = $img['mime'];
            $params[] = $img['filename'];
        }

        if ($pdf !== null) {
            $sets[]   = 'pdf_data=?';
            $sets[]   = 'pdf_mime=?';
            $sets[]   = 'pdf_filename=?';
            $sets[]   = 'pdf_url=?';   // clear typed URL when file uploaded
            $params[] = $pdf['data'];
            $params[] = $pdf['mime'];
            $params[] = $pdf['filename'];
            $params[] = '';
        } else {
            $sets[]   = 'pdf_url=?';
            $params[] = trim((string) ($data['pdf_url'] ?? ''));
        }

        $params[] = $id;
        $sql      = 'UPDATE manuals SET ' . implode(', ', $sets) . ' WHERE id=?';
        $pdo->prepare($sql)->execute($params);

    }

    public function delete(int $id): void
    {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare("DELETE FROM manuals WHERE id = ?");
            $stmt->execute([$id]);
        } catch (\Throwable) { /* silent */ }
    }

    // ── Videos ───────────────────────────────────────────────────────────────

    public function getVideos(string $category): array
    {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare(
                "SELECT id, category, title, video_url, sort_order
                 FROM manual_videos WHERE category = ? ORDER BY sort_order ASC, id ASC"
            );
            $stmt->execute([$category]);
            return $stmt->fetchAll() ?: [];
        } catch (\Throwable) {
            return [];
        }
    }

    public function saveVideos(string $category, array $videos): void
    {
        try {
            $pdo = get_pdo();
            $pdo->prepare("DELETE FROM manual_videos WHERE category = ?")->execute([$category]);
            $stmt = $pdo->prepare(
                "INSERT INTO manual_videos (category, title, video_url, sort_order) VALUES (?, ?, ?, ?)"
            );
            foreach ($videos as $index => $video) {
                $title = trim((string) ($video['title'] ?? ''));
                if ($title === '') {
                    continue;
                }
                $stmt->execute([
                    $category,
                    $title,
                    trim((string) ($video['video_url'] ?? '')),
                    $index,
                ]);
            }
        } catch (\Throwable) { /* silent */ }
    }

    public function deleteVideo(int $id): void
    {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare("DELETE FROM manual_videos WHERE id = ?");
            $stmt->execute([$id]);
        } catch (\Throwable) { /* silent */ }
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function resolveImage(?array $file): array
    {
        $empty = ['data' => null, 'mime' => null, 'filename' => null];

        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return $empty;
        }

        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mime    = mime_content_type($file['tmp_name']);

        if (!in_array($mime, $allowed, true)) {
            throw new \RuntimeException('Formato de imagem inválido. Use JPG, PNG, GIF ou WebP.');
        }

        $data     = file_get_contents($file['tmp_name']);
        $filename = basename((string) $file['name']);

        return ['data' => $data, 'mime' => $mime, 'filename' => $filename];
    }

    private function resolvePdf(?array $file): array
    {
        $empty = ['data' => null, 'mime' => null, 'filename' => null];

        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return $empty;
        }

        $mime = mime_content_type($file['tmp_name']);

        // Accept PDF and common office formats
        $allowed = ['application/pdf', 'application/x-pdf'];
        if (!in_array($mime, $allowed, true) && !str_ends_with(strtolower((string) $file['name']), '.pdf')) {
            throw new \RuntimeException('Apenas arquivos PDF são aceitos.');
        }

        $mime     = 'application/pdf';
        $data     = file_get_contents($file['tmp_name']);
        $filename = basename((string) $file['name']);

        return ['data' => $data, 'mime' => $mime, 'filename' => $filename];
    }
}
