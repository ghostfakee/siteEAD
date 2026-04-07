<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $path, array $data = []): void
    {
        $file = BASE_PATH . '/app/Views/' . str_replace('.', '/', $path) . '.php';
        if (!file_exists($file)) {
            throw new \RuntimeException("View not found: {$path}");
        }

        extract($data, EXTR_SKIP);
        require $file;
    }
}
