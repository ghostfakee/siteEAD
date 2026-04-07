<?php

declare(strict_types=1);

session_start();

define('BASE_PATH', __DIR__ . '/..');
define('DATA_PATH', BASE_PATH . '/data');
define('MEDIA_PATH', BASE_PATH . '/media');
define('CONTENT_FILE', DATA_PATH . '/content.json');
define('USERS_FILE', DATA_PATH . '/users.php');

require_once BASE_PATH . '/includes/db.php';
require_once BASE_PATH . '/includes/storage.php';
require_once BASE_PATH . '/includes/functions.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativePath = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = BASE_PATH . '/app/' . $relativePath . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
