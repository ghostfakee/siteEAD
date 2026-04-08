<?php
$values = $slide ?? ['image' => '', 'cta_label' => '', 'cta_url' => '', 'secondary_cta_label' => '', 'secondary_cta_url' => '', 'active' => true, 'order' => 0];
require BASE_PATH . '/app/Views/admin/partials/header.php';
?>
<?php if ($flash): ?><div class="admin-alert admin-alert--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<h1 class="admin-page-title"><?= $isEdit ? 'Editar banner' : 'Novo banner' ?></h1>
<form method="post" enctype="multipart/form-data">
    <section class="admin-panel">
        <div class="admin-panel__header">
            <h2>Imagem do banner</h2>
            <p style="margin:4px 0 0; font-size:.88rem; opacity:.65;">O banner é uma imagem full-width. Faça upload da imagem já com os textos e design prontos.</p>
        </div>
        <div class="admin-form-grid admin-form-grid--2">
            <div class="admin-field">
                <label>Upload da imagem <small style="font-weight:400;opacity:.6;">(JPG, PNG, WebP — recomendado 1920×600px)</small></label>
                <?php if (!empty($values['image'])): ?>
                    <div style="margin-bottom:10px;">
                        <img src="<?= e($values['image']) ?>" style="max-height:100px; border-radius:8px; object-fit:cover; max-width:100%;" alt="preview">
                        <small style="display:block;margin-top:4px;opacity:.6;">Imagem atual. Novo upload substitui.</small>
                    </div>
                <?php endif; ?>
                <input type="file" name="image_file" accept="image/*">
                <input type="hidden" name="image" value="<?= e($values['image']) ?>">
            </div>
            <div class="admin-field" style="display:flex; flex-direction:column; gap:12px; justify-content:flex-end;">
                <div class="admin-field">
                    <label>Ordem</label>
                    <input type="number" name="order" value="<?= (int) $values['order'] ?>">
                </div>
                <div class="admin-field">
                    <label>Status</label>
                    <select name="active">
                        <option value="1" <?= !empty($values['active']) ? 'selected' : '' ?>>Ativo</option>
                        <option value="0" <?= empty($values['active']) ? 'selected' : '' ?>>Inativo</option>
                    </select>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-panel">
        <div class="admin-panel__header">
            <h2>Botões <small style="font-weight:400; font-size:.85rem; opacity:.65;">— só aparecem se texto E link estiverem preenchidos</small></h2>
        </div>
        <div class="admin-form-grid admin-form-grid--4">
            <div class="admin-field"><label>Texto botão 1</label><input type="text" name="cta_label" value="<?= e($values['cta_label']) ?>" placeholder="Ex: Inscrições abertas"></div>
            <div class="admin-field"><label>Link botão 1</label><input type="text" name="cta_url" value="<?= e($values['cta_url']) ?>" placeholder="https://..."></div>
            <div class="admin-field"><label>Texto botão 2</label><input type="text" name="secondary_cta_label" value="<?= e($values['secondary_cta_label']) ?>" placeholder="Ex: Saiba mais"></div>
            <div class="admin-field"><label>Link botão 2</label><input type="text" name="secondary_cta_url" value="<?= e($values['secondary_cta_url']) ?>" placeholder="https://..."></div>
        </div>
    </section>
    <div class="admin-actions-row">
        <button type="submit" class="admin-btn admin-btn--primary"><?= $isEdit ? 'Salvar alterações' : 'Criar banner' ?></button>
        <a class="admin-btn admin-btn--secondary" href="banners.php">Cancelar</a>
    </div>
</form>
<?php require BASE_PATH . '/app/Views/admin/partials/footer.php'; ?>
