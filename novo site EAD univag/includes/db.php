<?php

declare(strict_types=1);

function get_pdo(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $host = (string) ($_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'mysql');
    $name = (string) ($_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'univag_cms');
    $user = (string) ($_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'root');
    $pass = (string) ($_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: 'univag123');

    $pdo = new PDO(
        "mysql:host={$host};dbname={$name};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

    return $pdo;
}

function db_available(): bool
{
    try {
        get_pdo();
        return true;
    } catch (\Throwable $e) {
        return false;
    }
}
