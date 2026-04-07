<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($site['site_name']) ?></title>
    <link rel="stylesheet" href="assets/css/site.css">
</head>
<body>
    <header class="header">
        <div class="topbar">
            <div class="container topbar__links">
                <?php foreach ($site['top_links'] as $link): ?><a href="<?= e($link['url']) ?>"><?= e($link['label']) ?></a><?php endforeach; ?>
            </div>
        </div>
        <div class="nav">
            <div class="container nav__inner">
                <?php
                    $menuItems  = $site['main_menu'];
                    $half       = (int) ceil(count($menuItems) / 2);
                    $leftMenu   = array_slice($menuItems, 0, $half);
                    $rightMenu  = array_slice($menuItems, $half);
                ?>
                <nav class="nav__menu nav__menu--left">
                    <?php foreach ($leftMenu as $item): ?><a href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a><?php endforeach; ?>
                </nav>
                <a class="nav__brand" href="index.php"><?= e($site['logo_text']) ?></a>
                <nav class="nav__menu nav__menu--right">
                    <?php foreach ($rightMenu as $item): ?><a href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a><?php endforeach; ?>
                </nav>
            </div>
        </div>
    </header>
    <main>
        <section class="hero">
            <div class="hero__track" data-carousel>
                <?php foreach ($heroSlides as $index => $slide): ?>
                    <article class="hero__slide <?= $index === 0 ? 'is-active' : '' ?>">
                        <div class="container hero__content">
                            <div class="hero__card">
                                <?php if (!empty($slide['badge'])): ?><span class="hero__badge"><?= e($slide['badge']) ?></span><?php endif; ?>
                                <h1><?= e($slide['title']) ?></h1>
                                <?php if (!empty($slide['subtitle'])): ?><p><?= e($slide['subtitle']) ?></p><?php endif; ?>
                                <div class="hero__buttons">
                                    <?php if (!empty($slide['cta_label'])): ?><a class="btn btn--light" href="<?= e($slide['cta_url']) ?>"><?= e($slide['cta_label']) ?></a><?php endif; ?>
                                    <?php if (!empty($slide['secondary_cta_label'])): ?><a class="btn" href="<?= e($slide['secondary_cta_url']) ?>"><?= e($slide['secondary_cta_label']) ?></a><?php endif; ?>
                                </div>
                            </div>
                            <div class="hero__visual">
                                <?php if (!empty($slide['image'])): ?><img src="<?= e($slide['image']) ?>" alt="<?= e($slide['title']) ?>"><?php endif; ?>
                            </div>
                            <div class="hero__info">
                                <?php if (!empty($slide['info_title'])): ?><h2><?= e($slide['info_title']) ?></h2><?php endif; ?>
                                <?php if (!empty($slide['info_lines'])): ?><ul><?php foreach ($slide['info_lines'] as $line): ?><li><?= e($line) ?></li><?php endforeach; ?></ul><?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="hero__controls container">
                <button type="button" data-carousel-prev>Anterior</button>
                <div class="hero__dots"><?php foreach ($heroSlides as $index => $slide): ?><button type="button" class="<?= $index === 0 ? 'is-active' : '' ?>" data-carousel-dot="<?= $index + 1 ?>"><?= $index + 1 ?></button><?php endforeach; ?></div>
                <button type="button" data-carousel-next>Próximo</button>
            </div>
        </section>
        <section class="section section--dark" id="porque-univag">
            <div class="container"><h2 class="section__title section__title--light"><?= e($content['why_univag']['title']) ?></h2><div class="card-grid card-grid--4"><?php foreach ($content['why_univag']['cards'] as $card): ?><?php $cardUrl = $card['slug'] === 'manuais' ? 'manuais/index.php' : 'page.php?slug=' . rawurlencode((string) $card['slug']); ?><a class="feature-card" href="<?= e($cardUrl) ?>"><img src="<?= e($card['image']) ?>" alt="<?= e($card['title']) ?>"><span class="feature-card__icon"><?= e($card['icon']) ?></span><h3><?= e($card['title']) ?></h3></a><?php endforeach; ?></div></div>
        </section>
        <section class="section" id="cursos">
            <div class="container"><h2 class="section__title"><?= e($content['courses']['title']) ?></h2><div class="card-grid card-grid--courses"><?php foreach ($content['courses']['items'] as $course): ?><a class="course-card" href="<?= e($course['url']) ?>" style="background-image: linear-gradient(rgba(10, 20, 90, .78), rgba(10, 20, 90, .78)), url('<?= e($course['image']) ?>')"><h3><?= e($course['title']) ?></h3><div class="course-card__tags"><?php foreach ($course['tags'] as $tag): ?><span><?= e($tag) ?></span><?php endforeach; ?></div></a><?php endforeach; ?></div></div>
        </section>
        <section class="section section--soft" id="modalidades">
            <div class="container"><h2 class="section__title"><?= e($content['modalities']['title']) ?></h2><div class="modality-list"><?php foreach ($content['modalities']['items'] as $index => $item): ?><article class="modality <?= $index % 2 === 1 ? 'modality--reverse' : '' ?>"><img src="<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>"><div class="modality__content"><h3><?= e($item['title']) ?></h3><p><?= nl2html($item['content']) ?></p><a class="btn" href="<?= e($item['cta_url']) ?>"><?= e($item['cta_label']) ?></a></div></article><?php endforeach; ?></div></div>
        </section>
        <section class="section" id="bolsas">
            <div class="container"><h2 class="section__title"><?= e($content['scholarships']['title']) ?></h2><div class="benefits-grid"><?php foreach ($content['scholarships']['items'] as $item): ?><a href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a><?php endforeach; ?></div></div>
        </section>
        <section class="section section--soft"><div class="container structure-callout"><div><span class="eyebrow"><?= e($content['structure']['title']) ?></span><a class="btn" href="<?= e($content['structure']['cta_url']) ?>"><?= e($content['structure']['cta_label']) ?></a></div></div></section>

        <section class="section" id="noticias"><div class="container"><h2 class="section__title"><?= e($content['news']['title']) ?></h2><a class="news-featured" href="<?= e($content['news']['featured']['url']) ?>"><img src="<?= e($content['news']['featured']['image']) ?>" alt="<?= e($content['news']['featured']['title']) ?>"><div><span><?= e(format_date_br($content['news']['featured']['date'])) ?></span><h3><?= e($content['news']['featured']['title']) ?></h3><p><?= e($content['news']['featured']['excerpt']) ?></p></div></a><div class="news-grid"><?php foreach ($content['news']['items'] as $item): ?><a class="news-card" href="<?= e($item['url']) ?>"><img src="<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>"><span><?= e(format_date_br($item['date'])) ?></span><h3><?= e($item['title']) ?></h3></a><?php endforeach; ?></div></div></section>
    </main>
    <footer class="footer"><div class="container footer__cta"><?php foreach ($site['footer_ctas'] as $cta): ?><a href="<?= e($cta['url']) ?>"><?= e($cta['label']) ?></a><?php endforeach; ?></div><div class="container footer__main"><div class="footer__brand"><div class="nav__brand nav__brand--footer"><?= e($site['logo_text']) ?></div></div><div class="footer__columns"><?php foreach ($site['footer_columns'] as $column): ?><div><h3><?= e($column['title']) ?></h3><?php foreach ($column['links'] as $link): ?><a href="<?= e($link['url']) ?>"><?= e($link['label']) ?></a><?php endforeach; ?></div><?php endforeach; ?></div></div><div class="footer__addresses"><div class="container footer__address-grid"><?php foreach ($site['addresses'] as $address): ?><a href="<?= e($address['url']) ?>"><h3><?= e($address['title']) ?></h3><p><?= nl2html($address['description']) ?></p></a><?php endforeach; ?></div></div><div class="footer__bottom"><div class="container"><a href="<?= e($site['privacy_url']) ?>"><?= e($site['privacy_text']) ?></a></div></div></footer>
    <script src="assets/js/site.js"></script>
</body>
</html>
