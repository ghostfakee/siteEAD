<?php require BASE_PATH . '/app/Views/admin/partials/header.php'; ?>
<?php if ($flash): ?><div class="admin-alert admin-alert--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<h1 class="admin-page-title">Dashboard</h1>
<div class="admin-grid-cards">
    <section class="admin-stat-card"><h2>Total de banners</h2><strong><?= count($slides) ?></strong></section>
    <section class="admin-stat-card"><h2>Banners ativos</h2><strong><?= count($activeSlides) ?></strong></section>
    <section class="admin-stat-card"><h2>Noticias</h2><strong><?= count($content['news']['items'] ?? []) + 1 ?></strong></section>
    <section class="admin-stat-card"><h2>Paginas internas</h2><strong><?= count($content['pages'] ?? []) ?></strong></section>
</div>
<section class="admin-panel">
    <div class="admin-panel__header">
        <h2>Banners recentes</h2>
        <div class="admin-actions-row">
            <a class="admin-btn admin-btn--primary" href="upload.php">Adicionar banner</a>
            <a class="admin-btn admin-btn--secondary" href="content.php">Editar conteudo</a>
        </div>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Imagem</th><th>Titulo</th><th>Status</th><th>Ordem</th><th>Acoes</th></tr></thead>
            <tbody>
                <?php foreach (array_slice($slides, 0, 5) as $slide): ?>
                    <tr>
                        <td><img class="admin-thumb" src="<?= e($slide['image']) ?>" alt="<?= e($slide['title']) ?>"></td>
                        <td><strong><?= e($slide['title']) ?></strong><br><small><?= e($slide['badge'] ?? '') ?></small></td>
                        <td><span class="admin-badge <?= !empty($slide['active']) ? 'admin-badge--active' : 'admin-badge--inactive' ?>"><?= !empty($slide['active']) ? 'Ativo' : 'Inativo' ?></span></td>
                        <td><?= (int) ($slide['order'] ?? 0) ?></td>
                        <td class="admin-actions-row"><a class="admin-btn admin-btn--primary" href="edit-banner.php?id=<?= e($slide['id']) ?>">Editar</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require BASE_PATH . '/app/Views/admin/partials/footer.php'; ?>
