<?php $user = $_SESSION['admin_user'] ?? 'admin'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> - CMS UNIVAG</title>
    <link rel="stylesheet" href="../assets/css/site.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="admin-shell">
    <header class="admin-topbar">
        <div class="admin-topbar__title">
            <strong>CMS UNIVAG</strong>
            <span><?= e($title) ?></span>
        </div>
        <div class="admin-topbar__user">
            <span>Ola, <?= e((string) $user) ?></span>
            <a class="admin-btn admin-btn--danger" href="logout.php">Sair</a>
        </div>
    </header>
    <aside class="admin-sidebar">
        <nav>
            <?php
            $items = [
                'dashboard' => ['Dashboard', 'index.php'],
                'banners' => ['Banners', 'banners.php'],
                'upload' => ['Novo Banner', 'upload.php'],
                'content' => ['Conteudo do Site', 'content.php'],
                'manuals' => ['Manuais', 'manuals.php'],
                'settings' => ['Configuracoes', 'settings.php'],
            ];
            foreach ($items as $key => [$label, $url]):
            ?>
                <a href="<?= e($url) ?>" class="<?= $active === $key ? 'is-active' : '' ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
            <a href="../index.php" target="_blank" rel="noopener">Ver Site</a>
        </nav>
    </aside>
    <main class="admin-main">
