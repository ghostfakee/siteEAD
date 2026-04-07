<?php require BASE_PATH . '/app/Views/admin/partials/header.php'; ?>
<?php if ($flash): ?><div class="admin-alert admin-alert--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<h1 class="admin-page-title">Gerenciar banners</h1>
<div class="admin-grid-cards">
    <section class="admin-stat-card"><h2>Total</h2><strong><?= count($slides) ?></strong></section>
    <section class="admin-stat-card"><h2>Ativos</h2><strong><?= count(array_filter($slides, static fn(array $slide): bool => !empty($slide['active']))) ?></strong></section>
    <section class="admin-stat-card"><h2>Com CTA secundario</h2><strong><?= count(array_filter($slides, static fn(array $slide): bool => !empty($slide['secondary_cta_label']))) ?></strong></section>
</div>
<section class="admin-panel">
    <div class="admin-panel__header"><h2>Lista de banners</h2><a class="admin-btn admin-btn--success" href="upload.php">Adicionar banner</a></div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Imagem</th><th>Informacoes</th><th>Status</th><th>Ordem</th><th>Acoes</th></tr></thead>
            <tbody>
                <?php foreach ($slides as $slide): ?>
                    <tr>
                        <td><img class="admin-thumb" src="<?= e($slide['image']) ?>" alt="<?= e($slide['title']) ?>"></td>
                        <td><strong><?= e($slide['title']) ?></strong><br><small><?= e($slide['subtitle']) ?></small></td>
                        <td><span class="admin-badge <?= !empty($slide['active']) ? 'admin-badge--active' : 'admin-badge--inactive' ?>"><?= !empty($slide['active']) ? 'Ativo' : 'Inativo' ?></span></td>
                        <td><form method="post"><input type="hidden" name="action" value="order"><input type="hidden" name="id" value="<?= e($slide['id']) ?>"><input type="number" name="order" value="<?= (int) ($slide['order'] ?? 0) ?>" onchange="this.form.submit()"></form></td>
                        <td class="admin-actions-row">
                            <form method="post"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= e($slide['id']) ?>"><button class="admin-btn <?= !empty($slide['active']) ? 'admin-btn--warning' : 'admin-btn--success' ?>" type="submit"><?= !empty($slide['active']) ? 'Desativar' : 'Ativar' ?></button></form>
                            <a class="admin-btn admin-btn--primary" href="edit-banner.php?id=<?= e($slide['id']) ?>">Editar</a>
                            <form method="post" onsubmit="return confirm('Deseja excluir este banner?');"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e($slide['id']) ?>"><button class="admin-btn admin-btn--danger" type="submit">Excluir</button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require BASE_PATH . '/app/Views/admin/partials/footer.php'; ?>
