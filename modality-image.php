<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); exit; }

try {
    $stmt = get_pdo()->prepare("SELECT image_data, image_mime, image_filename, updated_at FROM modalities WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
} catch (\Throwable) { http_response_code(500); exit; }

if (!$row || empty($row['image_data'])) { http_response_code(404); exit; }

$etag = md5((string) $row['updated_at']);
if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
    http_response_code(304); exit;
}

header('Content-Type: ' . $row['image_mime']);
header('Content-Length: ' . strlen((string) $row['image_data']));
header('Cache-Control: public, max-age=86400');
header('ETag: ' . $etag);
header('Content-Disposition: inline; filename="' . addslashes((string) $row['image_filename']) . '"');
echo $row['image_data'];
