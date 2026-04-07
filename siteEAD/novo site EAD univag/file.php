<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$id   = (int) ($_GET['id'] ?? 0);
$type = trim((string) ($_GET['type'] ?? 'pdf'));

if ($id <= 0) {
    http_response_code(404);
    exit;
}

try {
    $pdo  = get_pdo();
    $stmt = $pdo->prepare(
        "SELECT pdf_data, pdf_mime, pdf_filename FROM manuals WHERE id = ? AND pdf_data IS NOT NULL"
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch();
} catch (\Throwable $e) {
    http_response_code(500);
    exit;
}

if (!$row || empty($row['pdf_data'])) {
    http_response_code(404);
    exit;
}

$mime     = $row['pdf_mime']     ?? 'application/pdf';
$filename = $row['pdf_filename'] ?? 'manual.pdf';

header('Content-Type: ' . $mime);
header('Content-Length: ' . strlen((string) $row['pdf_data']));
header('Content-Disposition: inline; filename="' . addslashes($filename) . '"');
header('Cache-Control: public, max-age=86400');

echo $row['pdf_data'];
