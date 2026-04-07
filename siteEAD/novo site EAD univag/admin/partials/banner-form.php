<?php
$isEdit = isset($slide);
$values = $slide ?? [
    'title' => '',
    'subtitle' => '',
    'badge' => '',
    'image' => '',
    'cta_label' => '',
    'cta_url' => '',
    'secondary_cta_label' => '',
    'secondary_cta_url' => '',
    'info_title' => '',
    'info_lines' => [],
    'active' => true,
    'order' => 0,
];
?>
<form method="post" enctype="multipart/form-data">
    <section class="admin-panel">
        <div class="admin-panel__header"><h2>Informacoes principais</h2></div>
        <div class="admin-form-grid">
            <div class="admin-field"><label>Titulo</label><input type="text" name="title" value="<?= e($values['title']) ?>" required></div>
            <div class="admin-field"><label>Badge</label><input type="text" name="badge" value="<?= e($values['badge']) ?>"></div>
            <div class="admin-field"><label>Subtitulo</label><textarea name="subtitle"><?= e($values['subtitle']) ?></textarea></div>
            <div class="admin-field"><label>Imagem URL</label><input type="text" name="image" value="<?= e($values['image']) ?>"></div>
            <div class="admin-field"><label>Upload de imagem</label><input type="file" name="image_file" accept="image/*"></div>
            <div class="admin-field"><label>Titulo lateral</label><input type="text" name="info_title" value="<?= e($values['info_title']) ?>"></div>
            <div class="admin-field"><label>Linhas laterais</label><textarea name="info_lines"><?= e(implode("\n", $values['info_lines'])) ?></textarea></div>
            <div class="admin-field"><label>Ordem</label><input type="number" name="order" value="<?= (int) $values['order'] ?>"></div>
            <div class="admin-field"><label>Status</label><select name="active"><option value="1" <?= !empty($values['active']) ? 'selected' : '' ?>>Ativo</option><option value="0" <?= empty($values['active']) ? 'selected' : '' ?>>Inativo</option></select></div>
        </div>
    </section>
    <section class="admin-panel">
        <div class="admin-panel__header"><h2>Botoes do banner</h2></div>
        <div class="admin-form-grid admin-form-grid--4">
            <div class="admin-field"><label>Texto botao 1</label><input type="text" name="cta_label" value="<?= e($values['cta_label']) ?>"></div>
            <div class="admin-field"><label>Link botao 1</label><input type="text" name="cta_url" value="<?= e($values['cta_url']) ?>"></div>
            <div class="admin-field"><label>Texto botao 2</label><input type="text" name="secondary_cta_label" value="<?= e($values['secondary_cta_label']) ?>"></div>
            <div class="admin-field"><label>Link botao 2</label><input type="text" name="secondary_cta_url" value="<?= e($values['secondary_cta_url']) ?>"></div>
        </div>
    </section>
    <div class="admin-actions-row">
        <button type="submit" class="admin-btn admin-btn--primary"><?= $isEdit ? 'Salvar alteracoes' : 'Criar banner' ?></button>
        <a class="admin-btn admin-btn--secondary" href="banners.php">Cancelar</a>
    </div>
</form>
