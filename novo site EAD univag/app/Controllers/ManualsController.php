<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\ContentModel;
use App\Models\ManualModel;

final class ManualsController
{
    public function index(): void
    {
        $content     = (new ContentModel())->getAll();
        $manualModel = new ManualModel();

        $categories = $manualModel->getCategories();

        $manualsByCategory = [];
        $videosByCategory  = [];
        foreach ($categories as $cat) {
            $key                      = $cat['category_key'];
            $manualsByCategory[$key]  = $manualModel->getByCategory($key);
            $videosByCategory[$key]   = $manualModel->getVideos($key);
        }

        View::render('public.manuals', [
            'site'               => $content['site'],
            'pageConfig'         => $manualModel->getPageConfig(),
            'categories'         => $categories,
            'manualsByCategory'  => $manualsByCategory,
            'videosByCategory'   => $videosByCategory,
        ]);
    }
}
