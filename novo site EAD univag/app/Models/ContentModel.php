<?php

declare(strict_types=1);

namespace App\Models;

final class ContentModel
{
    public function getAll(): array
    {
        return content_data();
    }

    public function save(array $content): void
    {
        save_content($content);
    }
}
