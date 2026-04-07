<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page['title'] ?? 'Página não encontrada') ?> | <?= e($site['site_name']) ?></title>
    <link rel="stylesheet" href="assets/css/site.css">
</head>
<body>
    <header class="header"><div class="nav"><div class="container nav__inner"><a class="nav__brand" href="index.php"><?= e($site['logo_text']) ?></a><nav class="nav__menu"><a href="index.php">Home</a><a href="index.php#porque-univag">Por que ser UNIVAG</a><a href="admin/login.php">CMS</a></nav></div></div></header>
    <main class="page"><div class="container"><?php if (!$page): ?><h1 class="section__title">Página não encontrada</h1><?php else: ?><h1 class="section__title"><?= e($page['title']) ?></h1><p class="page__intro"><?= e($page['intro']) ?></p><?php if (!empty($page['blocks'])): ?><div class="page__blocks"><?php foreach ($page['blocks'] as $block): ?><article class="page-block"><h2><?= e($block['title']) ?></h2><p><?= nl2html($block['content']) ?></p></article><?php endforeach; ?></div><?php endif; ?><?php endif; ?></div></main>
    <script src="assets/js/site.js"></script>
</body>
</html>
