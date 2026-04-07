<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageConfig['title'] ?? 'Manuais') ?> | <?= e($site['site_name']) ?></title>
    <link rel="stylesheet" href="../assets/css/site.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="manuals-header">
        <div class="manuals-header__inner" style="<?= !empty($pageConfig['hero_image']) ? 'background-image: linear-gradient(90deg, rgba(22,10,91,.82), rgba(22,10,91,.96)), url(' . e($pageConfig['hero_image']) . ');' : '' ?>">
            <div class="manuals-header__content">
                <?php if (!empty($pageConfig['hero_logo'])): ?>
                    <img class="manuals-header__logo" src="<?= e($pageConfig['hero_logo']) ?>" alt="<?= e($pageConfig['title']) ?>">
                <?php else: ?>
                    <div class="manuals-header__brand">
                        <span class="manuals-header__brand-univag">UNIVAG</span>
                        <span class="manuals-header__brand-sep">|</span>
                        <span class="manuals-header__brand-ead">EAD</span>
                        <span class="manuals-header__brand-title"><?= e($pageConfig['title'] ?? 'Manuais e Tutoriais') ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <a class="manuals-header__back" href="../index.php">&#8592; Voltar ao site</a>
    </header>

    <main class="manuals-main">
        <div class="container">
            <?php if (!empty($pageConfig['intro'])): ?>
                <p class="manuals-intro"><?= e($pageConfig['intro']) ?></p>
            <?php endif; ?>

            <div class="manuals-accordion">
                <?php foreach ($categories as $cat): ?>
                    <?php
                        $key      = $cat['category_key'];
                        $manuals  = $manualsByCategory[$key] ?? [];
                        $videos   = $videosByCategory[$key]  ?? [];
                    ?>
                    <div class="manuals-accordion__item" data-accordion-item="<?= e($key) ?>">
                        <button type="button" class="manuals-accordion__btn" data-accordion-toggle="<?= e($key) ?>">
                            <?= e($cat['title']) ?>
                            <span class="manuals-accordion__arrow">&#9660;</span>
                        </button>
                        <div class="manuals-accordion__body" data-accordion-body="<?= e($key) ?>" hidden>

                            <?php if (!empty($manuals)): ?>
                                <h2 class="manuals-section-title"><?= e($cat['manuals_title'] ?: $cat['title']) ?></h2>
                                <div class="manuals-grid">
                                    <?php foreach ($manuals as $manual): ?>
                                        <article class="manual-card">
                                            <div class="manual-card__cover <?= $manual['image_mime'] ? '' : 'manual-card__cover--placeholder' ?>">
                                                <?php if ($manual['image_mime']): ?>
                                                    <img src="../image.php?id=<?= (int)$manual['id'] ?>&type=manual" alt="<?= e($manual['title']) ?>">
                                                <?php else: ?>
                                                    <span>&#128196;</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="manual-card__body">
                                                <h3><?= e($manual['title']) ?></h3>
                                                <?php if (!empty($manual['description'])): ?>
                                                    <p><?= e($manual['description']) ?></p>
                                                <?php endif; ?>
                                                <div class="manual-card__actions">
                                                    <?php if ($manual['media_type'] === 'video' && !empty($manual['video_url'])): ?>
                                                        <?php $embed = youtube_embed_url($manual['video_url']); ?>
                                                        <?php if ($embed): ?>
                                                            <div class="manual-inline-video">
                                                                <iframe src="<?= e($embed) ?>" title="<?= e($manual['title']) ?>" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <?php
                                                            $pdfHref = $manual['pdf_mime']
                                                                ? '../file.php?id=' . (int)$manual['id']
                                                                : ($manual['pdf_url'] ?? '');
                                                        ?>
                                                        <?php if ($pdfHref): ?>
                                                            <a class="btn-manual" href="<?= e($pdfHref) ?>" target="_blank" rel="noopener">Acesse o Manual</a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($videos)): ?>
                                <div class="manuals-videos-section">
                                    <h2 class="manuals-section-title"><?= e($cat['videos_title'] ?: 'Vídeos Tutoriais') ?></h2>
                                    <div class="manuals-videos-grid">
                                        <?php foreach ($videos as $video): ?>
                                            <?php $embed = youtube_embed_url($video['video_url'] ?? ''); ?>
                                            <?php if (!$embed) continue; ?>
                                            <article class="manual-video-card">
                                                <div class="manual-video-frame">
                                                    <iframe src="<?= e($embed) ?>" title="<?= e($video['title']) ?>" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen loading="lazy"></iframe>
                                                </div>
                                                <?php if (!empty($video['title'])): ?>
                                                    <p class="manual-video-card__title"><?= e($video['title']) ?></p>
                                                <?php endif; ?>
                                            </article>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (empty($manuals) && empty($videos)): ?>
                                <p style="padding:24px; opacity:.6; text-align:center;">Nenhum manual disponível nesta categoria ainda.</p>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <script src="js/script.js"></script>
</body>
</html>
