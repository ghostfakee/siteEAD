<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($site['site_name']) ?></title>
    <link rel="stylesheet" href="assets/css/site.css?v=2">
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
                <?php $__hasNavLogo = (new \App\Models\SiteImageModel())->exists('logo_nav'); ?>
                <a class="nav__brand" href="index.php">
                    <?php if ($__hasNavLogo): ?>
                        <img src="site-image.php?key=logo_nav" alt="<?= e($site['logo_text']) ?>" class="nav__logo-img">
                    <?php else: ?>
                        <?= e($site['logo_text']) ?>
                    <?php endif; ?>
                </a>
                <nav class="nav__menu nav__menu--right">
                    <?php foreach ($rightMenu as $item): ?><a href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a><?php endforeach; ?>
                    <a class="nav__ava-btn" href="https://avaunivag.univagead.com.br/login/index.php" target="_blank" rel="noopener">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z" fill="currentColor"/><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z" fill="currentColor"/></svg>
                        Plataforma AVA
                    </a>
                </nav>
            </div>
        </div>
    </header>
    <main>
        <section class="hero">
            <div class="hero__track" data-carousel>
                <?php foreach ($heroSlides as $index => $slide): ?>
                    <article class="hero__slide <?= $index === 0 ? 'is-active' : '' ?>">
                        <?php if (!empty($slide['image'])): ?>
                            <img class="hero__bg" src="<?= e($slide['image']) ?>" alt="banner">
                        <?php endif; ?>
                        <?php
                            $hasCta  = !empty($slide['cta_label'])           && !empty($slide['cta_url']);
                            $hasCta2 = !empty($slide['secondary_cta_label']) && !empty($slide['secondary_cta_url']);
                        ?>
                        <?php if ($hasCta || $hasCta2): ?>
                            <div class="hero__buttons">
                                <?php if ($hasCta): ?><a class="hero__btn hero__btn--primary" href="<?= e($slide['cta_url']) ?>"><?= e($slide['cta_label']) ?></a><?php endif; ?>
                                <?php if ($hasCta2): ?><a class="hero__btn hero__btn--secondary" href="<?= e($slide['secondary_cta_url']) ?>"><?= e($slide['secondary_cta_label']) ?></a><?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
            <button class="hero__arrow hero__arrow--prev" type="button" data-carousel-prev>&#8249;</button>
            <button class="hero__arrow hero__arrow--next" type="button" data-carousel-next>&#8250;</button>
            <div class="hero__dots">
                <?php foreach ($heroSlides as $index => $slide): ?>
                    <button type="button" class="<?= $index === 0 ? 'is-active' : '' ?>" data-carousel-dot="<?= $index + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
        </section>
        <section class="section section--dark" id="porque-univag">
            <div class="container"><h2 class="section__title section__title--light"><?= e($content['why_univag']['title']) ?></h2><div class="card-grid card-grid--4"><?php foreach ($content['why_univag']['cards'] as $card): ?><?php $cardUrl = $card['slug'] === 'manuais' ? 'manuais/index.php' : 'page.php?slug=' . rawurlencode((string) $card['slug']); ?><a class="feature-card" href="<?= e($cardUrl) ?>"><img src="<?= e($card['image']) ?>" alt="<?= e($card['title']) ?>"><span class="feature-card__icon"><?= e($card['icon']) ?></span><h3><?= e($card['title']) ?></h3></a><?php endforeach; ?></div></div>
        </section>
        <section class="section" id="cursos">
            <div class="container"><h2 class="section__title"><?= e($content['courses']['title']) ?></h2><div class="card-grid card-grid--courses"><?php foreach ($content['courses']['items'] as $course): ?><a class="course-card" href="<?= e($course['url']) ?>" style="background-image: linear-gradient(rgba(10, 20, 90, .78), rgba(10, 20, 90, .78)), url('<?= e($course['image']) ?>')"><h3><?= e($course['title']) ?></h3><div class="course-card__tags"><?php foreach ($course['tags'] as $tag): ?><span><?= e($tag) ?></span><?php endforeach; ?></div></a><?php endforeach; ?></div></div>
        </section>
        <?php
            $modalityModel = new \App\Models\ModalityModel();
            $modalityItems = $modalityModel->active();
        ?>
        <?php if ($modalityItems): ?>
        <section class="section section--soft" id="modalidades">
            <div class="container">
                <h2 class="section__title"><?= e($content['modalities']['title']) ?></h2>
                <div class="modality-list">
                    <?php foreach ($modalityItems as $index => $item): ?>
                        <article class="modality <?= $index % 2 === 1 ? 'modality--reverse' : '' ?>">
                            <?php if ($modalityModel->hasImage((int) $item['id'])): ?>
                                <img src="modality-image.php?id=<?= (int) $item['id'] ?>" alt="<?= e($item['title']) ?>">
                            <?php endif; ?>
                            <div class="modality__content">
                                <h3><?= e($item['title']) ?></h3>
                                <?php if (!empty($item['content'])): ?><p><?= nl2html($item['content']) ?></p><?php endif; ?>
                                <?php if (!empty($item['cta_label']) && !empty($item['cta_url'])): ?>
                                    <a class="btn" href="<?= e($item['cta_url']) ?>"><?= e($item['cta_label']) ?></a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>
        <?php if ((new \App\Models\SiteImageModel())->exists('scholarships_cover')): ?>
        <section class="bolsas-banner" id="bolsas">
            <img src="site-image.php?key=scholarships_cover" alt="Bolsas e Parcelamentos">
        </section>
        <?php endif; ?>

        <?php
            $newsModel    = new \App\Models\NewsModel();
            $newsFeatured = $newsModel->getFeatured();
            $newsRegular  = $newsModel->getRegular();
        ?>
        <?php if ($newsFeatured || $newsRegular): ?>
        <section class="section" id="noticias">
            <div class="container">
                <h2 class="section__title"><?= e($content['news']['title']) ?></h2>
                <?php if ($newsFeatured): ?>
                    <a class="news-featured" href="<?= e($newsFeatured['url'] ?? '#') ?>">
                        <?php if ($newsModel->hasImage((int) $newsFeatured['id'])): ?>
                            <img src="news-image.php?id=<?= (int) $newsFeatured['id'] ?>" alt="<?= e($newsFeatured['title']) ?>">
                        <?php endif; ?>
                        <div>
                            <span><?= e(format_date_br($newsFeatured['published_at'])) ?></span>
                            <h3><?= e($newsFeatured['title']) ?></h3>
                            <?php if (!empty($newsFeatured['excerpt'])): ?><p><?= e($newsFeatured['excerpt']) ?></p><?php endif; ?>
                        </div>
                    </a>
                <?php endif; ?>
                <?php if ($newsRegular): ?>
                    <div class="news-grid">
                        <?php foreach ($newsRegular as $item): ?>
                            <a class="news-card" href="<?= e($item['url'] ?? '#') ?>">
                                <?php if ($newsModel->hasImage((int) $item['id'])): ?>
                                    <img src="news-image.php?id=<?= (int) $item['id'] ?>" alt="<?= e($item['title']) ?>">
                                <?php else: ?>
                                    <div style="height:220px; background:#e0e4f0;"></div>
                                <?php endif; ?>
                                <span><?= e(format_date_br($item['published_at'])) ?></span>
                                <h3><?= e($item['title']) ?></h3>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>
    </main>
    <footer class="footer"><div class="container footer__cta"><?php foreach ($site['footer_ctas'] as $cta): ?><a href="<?= e($cta['url']) ?>"><?= e($cta['label']) ?></a><?php endforeach; ?></div><div class="container footer__main"><div class="footer__brand"><?php $__hasFooterLogo = (new \App\Models\SiteImageModel())->exists('logo_footer'); ?><a href="index.php" class="nav__brand nav__brand--footer"><?php if ($__hasFooterLogo): ?><img src="site-image.php?key=logo_footer" alt="<?= e($site['logo_text']) ?>" class="nav__logo-img nav__logo-img--footer"><?php else: ?><?= e($site['logo_text']) ?><?php endif; ?></a></div><div class="footer__columns"><?php foreach ($site['footer_columns'] as $column): ?><div><h3><?= e($column['title']) ?></h3><?php foreach ($column['links'] as $link): ?><a href="<?= e($link['url']) ?>"><?= e($link['label']) ?></a><?php endforeach; ?></div><?php endforeach; ?></div></div><div class="footer__addresses"><div class="container footer__address-grid"><?php foreach ($site['addresses'] as $address): ?><a href="<?= e($address['url']) ?>"><h3><?= e($address['title']) ?></h3><p><?= nl2html($address['description']) ?></p></a><?php endforeach; ?></div></div><div class="footer__bottom"><div class="container"><a href="<?= e($site['privacy_url']) ?>"><?= e($site['privacy_text']) ?></a></div></div></footer>
    <script src="assets/js/site.js"></script>
</body>
</html>
