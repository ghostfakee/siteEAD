<?php require BASE_PATH . '/app/Views/admin/partials/header.php'; ?>
<?php if ($flash): ?><div class="admin-alert admin-alert--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<h1 class="admin-page-title">Configuracoes</h1>
<section class="admin-panel">
    <div class="admin-panel__header"><h2>Informacoes do sistema</h2></div>
    <div class="admin-form-grid">
        <div class="admin-field"><label>Versao PHP</label><input type="text" value="<?= e(phpversion()) ?>" readonly></div>
        <div class="admin-field"><label>Total de banners</label><input type="text" value="<?= count($slides) ?>" readonly></div>
        <div class="admin-field"><label>Paginas internas</label><input type="text" value="<?= count($content['pages']) ?>" readonly></div>
        <div class="admin-field"><label>Usuario</label><input type="text" value="<?= e((string) ($_SESSION['admin_user'] ?? 'admin')) ?>" readonly></div>
    </div>
</section>
<section class="admin-panel">
    <div class="admin-panel__header"><h2>Alterar senha</h2></div>
    <form method="post" class="admin-form-grid">
        <div class="admin-field"><label>Senha atual</label><input type="password" name="current_password" required></div>
        <div class="admin-field"><label>Nova senha</label><input type="password" name="new_password" required minlength="6"></div>
        <div class="admin-field"><label>Confirmar senha</label><input type="password" name="confirm_password" required minlength="6"></div>
        <div class="admin-actions-row"><button type="submit" class="admin-btn admin-btn--primary">Salvar nova senha</button></div>
    </form>
</section>
<?php require BASE_PATH . '/app/Views/admin/partials/footer.php'; ?>
