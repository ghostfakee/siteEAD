<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

(new App\Controllers\PageController())->show(trim((string) ($_GET['slug'] ?? '')));
