<?php

declare(strict_types=1);

// Secure session configuration — must run BEFORE session_start()
ini_set('session.use_only_cookies', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', '7200'); // 2h timeout

session_start();

define('BASE_PATH', __DIR__ . '/..');
define('DATA_PATH', BASE_PATH . '/data');
define('MEDIA_PATH', BASE_PATH . '/media');
define('CONTENT_FILE', DATA_PATH . '/content.json');
define('USERS_FILE', DATA_PATH . '/users.php');

require_once BASE_PATH . '/includes/db.php';
require_once BASE_PATH . '/includes/security.php';
require_once BASE_PATH . '/includes/storage.php';
require_once BASE_PATH . '/includes/functions.php';

// Send security headers on every request
send_security_headers();

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
