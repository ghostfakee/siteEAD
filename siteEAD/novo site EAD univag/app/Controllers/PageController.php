<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\ContentModel;

final class PageController
{
    public function show(string $slug): void
    {
        if ($slug === 'manuais') {
            header('Location: manuais/index.php');
            exit;
        }

        $content = (new ContentModel())->getAll();
        $page = $content['pages'][$slug] ?? null;
        if (!$page) {
            http_response_code(404);
        }

        View::render('public.page', [
            'content' => $content,
            'site' => $content['site'],
            'page' => $page,
        ]);
    }
}
