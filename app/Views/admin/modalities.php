<?php require BASE_PATH . '/app/Views/admin/partials/header.php'; ?>
<?php if ($flash): ?><div class="admin-alert admin-alert--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>

<h1 class="admin-page-title">Modalidades</h1>

<!-- Regra de negócio -->
<div style="display:flex; align-items:center; gap:12px; margin-bottom:20px; padding:12px 18px; background:#eef2ff; border-radius:12px; font-size:.88rem; color:#3730a3;">
    <span style="font-size:1.4rem;">🎓</span>
    <div>
        <strong><?= $count ?>/4 modalidades cadastradas.</strong>
        Mínimo 0, máximo <strong>4 cards</strong>. Cada card pode ter imagem, descrição e botão com link.
    </div>
    <?php if ($count < 4): ?>
        <button class="admin-btn admin-btn--success" style="margin-left:auto; white-space:nowrap;"
            onclick="document.getElementById('form-new').style.display=document.getElementById('form-new').style.display==='none'?'block':'none'">
            + Nova modalidade
        </button>
    <?php endif; ?>
</div>

<!-- ── Cards existentes ────────────────────────────────────────── -->
<?php foreach ($modalityList as $m): ?>
<section class="admin-panel" style="margin-bottom:14px;">
    <div class="admin-panel__header" style="cursor:pointer; user-select:none;"
         onclick="toggleEdit(<?= (int) $m['id'] ?>)">
        <div style="display:flex; align-items:center; gap:14px;">
            <?php if ($modalityModel->hasImage((int) $m['id'])): ?>
                <img src="../modality-image.php?id=<?= (int) $m['id'] ?>"
                     style="width:72px; height:48px; object-fit:cover; border-radius:8px; flex-shrink:0;">
            <?php else: ?>
                <div style="width:72px; height:48px; background:#e0e4f0; border-radius:8px; display:grid; place-items:center; font-size:.7rem; color:#888;">sem img</div>
            <?php endif; ?>
            <div>
                <h2 style="margin:0;"><?= e($m['title']) ?></h2>
                <small style="opacity:.55;"><?= !empty($m['cta_label']) ? 'Botão: ' . e($m['cta_label']) : 'Sem botão' ?> &nbsp;·&nbsp; Ordem: <?= (int) $m['sort_order'] ?> &nbsp;·&nbsp;
                    <span class="admin-badge <?= $m['active'] ? 'admin-badge--active' : 'admin-badge--inactive' ?>"><?= $m['active'] ? 'Ativo' : 'Inativo' ?></span>
                </small>
            </div>
        </div>
        <span style="font-size:.8rem; opacity:.5;">▼ expandir</span>
    </div>

    <div id="edit-<?= (int) $m['id'] ?>" style="display:<?= ($editId === (int) $m['id']) ? 'block' : 'none' ?>; padding:20px 0 0;">
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
            <div class="admin-form-grid admin-form-grid--2">
                <div class="admin-field">
                    <label>Título <span style="color:#e03e5e;">*</span></label>
                    <input type="text" name="title" value="<?= e($m['title']) ?>" required>
                </div>
                <div class="admin-field">
                    <label>Status</label>
                    <select name="active">
                        <option value="1" <?= $m['active'] ? 'selected' : '' ?>>Ativo (visível no site)</option>
                        <option value="0" <?= !$m['active'] ? 'selected' : '' ?>>Inativo (oculto)</option>
                    </select>
                </div>
                <div class="admin-field" style="grid-column:1/-1;">
                    <label>Descrição</label>
                    <textarea name="content" rows="3"><?= e($m['content']) ?></textarea>
                </div>
                <div class="admin-field">
                    <label>Texto do botão <small style="opacity:.6;">(deixe vazio para ocultar)</small></label>
                    <input type="text" name="cta_label" value="<?= e($m['cta_label']) ?>" placeholder="Ex: Saiba mais">
                </div>
                <div class="admin-field">
                    <label>Link do botão</label>
                    <input type="text" name="cta_url" value="<?= e($m['cta_url']) ?>" placeholder="https://...">
                </div>
                <div class="admin-field">
                    <label>Imagem <small style="opacity:.6;">(JPG, PNG, WebP — máx 5 MB)</small></label>
                    <?php if ($modalityModel->hasImage((int) $m['id'])): ?>
                        <img src="../modality-image.php?id=<?= (int) $m['id'] ?>"
                             style="height:80px; border-radius:8px; object-fit:cover; display:block; margin-bottom:8px;">
                        <small style="opacity:.6; display:block; margin-bottom:6px;">Imagem atual. Novo upload substitui.</small>
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
                </div>
                <div class="admin-field">
                    <label>Ordem de exibição</label>
                    <input type="number" name="sort_order" value="<?= (int) $m['sort_order'] ?>" min="0" max="3">
                    <small style="opacity:.6;">0 = primeiro, 3 = último</small>
                </div>
            </div>
            <div class="admin-actions-row" style="margin-top:16px;">
                <button type="submit" class="admin-btn admin-btn--primary">Salvar alterações</button>
                <form method="post" style="display:inline;" onsubmit="return confirm('Excluir a modalidade \'<?= e($m['title']) ?>\'?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                    <button type="submit" class="admin-btn admin-btn--danger">Excluir</button>
                </form>
            </div>
        </form>
    </div>
</section>
<?php endforeach; ?>

<?php if ($count === 0): ?>
    <div style="text-align:center; padding:40px; opacity:.5;">Nenhuma modalidade cadastrada ainda.</div>
<?php endif; ?>

<!-- ── Nova modalidade ────────────────────────────────────────── -->
<?php if ($count < 4): ?>
<section class="admin-panel" id="form-new" style="display:none; margin-top:24px;">
    <div class="admin-panel__header"><h2>Nova modalidade</h2></div>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create">
        <div class="admin-form-grid admin-form-grid--2">
            <div class="admin-field">
                <label>Título <span style="color:#e03e5e;">*</span></label>
                <input type="text" name="title" required placeholder="Ex: Presencial">
            </div>
            <div class="admin-field">
                <label>Status</label>
                <select name="active">
                    <option value="1">Ativo</option>
                    <option value="0">Inativo</option>
                </select>
            </div>
            <div class="admin-field" style="grid-column:1/-1;">
                <label>Descrição</label>
                <textarea name="content" rows="3" placeholder="Descreva esta modalidade de ensino…"></textarea>
            </div>
            <div class="admin-field">
                <label>Texto do botão <small style="opacity:.6;">(deixe vazio para ocultar)</small></label>
                <input type="text" name="cta_label" placeholder="Ex: Saiba mais">
            </div>
            <div class="admin-field">
                <label>Link do botão</label>
                <input type="text" name="cta_url" placeholder="https://...">
            </div>
            <div class="admin-field">
                <label>Imagem <small style="opacity:.6;">(JPG, PNG, WebP — máx 5 MB)</small></label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
            </div>
            <div class="admin-field">
                <label>Ordem de exibição</label>
                <input type="number" name="sort_order" value="<?= $count ?>" min="0" max="3">
            </div>
        </div>
        <div class="admin-actions-row" style="margin-top:16px;">
            <button type="submit" class="admin-btn admin-btn--success">Criar modalidade</button>
            <button type="button" class="admin-btn admin-btn--secondary"
                onclick="document.getElementById('form-new').style.display='none'">Cancelar</button>
        </div>
    </form>
</section>
<?php endif; ?>

<script>
function toggleEdit(id) {
    const el = document.getElementById('edit-' + id);
    document.querySelectorAll('[id^="edit-"]').forEach(e => { if (e !== el) e.style.display = 'none'; });
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>

<?php require BASE_PATH . '/app/Views/admin/partials/footer.php'; ?>
