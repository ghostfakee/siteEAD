<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$id   = (int) ($_GET['id'] ?? 0);
$type = trim((string) ($_GET['type'] ?? 'manual'));

if ($id <= 0 || !in_array($type, ['manual'], true)) {
    http_response_code(404);
    exit;
}

try {
    if ($type === 'manual') {
        $model = new App\Models\ManualModel();
        $img   = $model->getImage($id);
    } else {
        http_response_code(404);
        exit;
    }
} catch (\Throwable $e) {
    http_response_code(500);
    exit;
}

if (!$img || empty($img['image_data'])) {
    http_response_code(404);
    exit;
}

$mime     = $img['image_mime'] ?? 'image/jpeg';
$filename = $img['image_filename'] ?? 'image';

header('Content-Type: ' . $mime);
header('Content-Length: ' . strlen((string) $img['image_data']));
header('Cache-Control: public, max-age=86400');
header('Content-Disposition: inline; filename="' . addslashes($filename) . '"');

echo $img['image_data'];
