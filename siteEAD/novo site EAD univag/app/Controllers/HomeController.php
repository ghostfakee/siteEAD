<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\ContentModel;

final class HomeController
{
    public function index(): void
    {
        $content = (new ContentModel())->getAll();
        $heroSlides = $content['hero']['slides'] ?? [];
        usort($heroSlides, static fn(array $a, array $b): int => ((int) ($a['order'] ?? 0)) <=> ((int) ($b['order'] ?? 0)));
        $heroSlides = array_values(array_filter($heroSlides, static fn(array $slide): bool => !array_key_exists('active', $slide) || !empty($slide['active'])));

        View::render('public.home', [
            'content' => $content,
            'site' => $content['site'],
            'heroSlides' => $heroSlides,
            'manualsPage' => $content['pages']['manuais'] ?? null,
        ]);
    }
}
