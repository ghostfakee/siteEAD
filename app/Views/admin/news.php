<?php require BASE_PATH . '/app/Views/admin/partials/header.php'; ?>
<?php if ($flash): ?><div class="admin-alert admin-alert--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>

<h1 class="admin-page-title">Notícias</h1>

<!-- Regra de negócio -->
<div style="display:flex; align-items:center; gap:12px; margin-bottom:20px; padding:12px 18px; background:#eef2ff; border-radius:12px; font-size:.88rem; color:#3730a3;">
    <span style="font-size:1.4rem;">📰</span>
    <div>
        <strong><?= $count ?>/5 notícias cadastradas.</strong>
        Máximo de <strong>5 notícias</strong> — 1 destaque + 4 regulares. Para adicionar uma nova, exclua uma existente.
    </div>
    <?php if ($count < 5): ?>
        <button class="admin-btn admin-btn--success" style="margin-left:auto; white-space:nowrap;"
            onclick="document.getElementById('form-new').style.display=document.getElementById('form-new').style.display==='none'?'block':'none'">
            + Nova notícia
        </button>
    <?php endif; ?>
</div>

<!-- ── Tabela ──────────────────────────────────────────────────── -->
<section class="admin-panel">
    <div class="admin-panel__header"><h2>Lista de notícias</h2></div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Título</th>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Ordem</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($newsList as $n): ?>
                    <tr>
                        <td>
                            <?php if ($newsModel->hasImage((int) $n['id'])): ?>
                                <img src="../news-image.php?id=<?= (int) $n['id'] ?>" style="width:80px; height:52px; object-fit:cover; border-radius:6px;">
                            <?php else: ?>
                                <span style="opacity:.4; font-size:.8rem;">sem imagem</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= e($n['title']) ?></strong>
                            <?php if (!empty($n['excerpt'])): ?>
                                <br><small style="opacity:.6;"><?= e(mb_strimwidth($n['excerpt'], 0, 60, '…')) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><small><?= e($n['published_at']) ?></small></td>
                        <td>
                            <?php if ($n['featured']): ?>
                                <span class="admin-badge admin-badge--active">⭐ Destaque</span>
                            <?php else: ?>
                                <span class="admin-badge">Regular</span>
                            <?php endif; ?>
                        </td>
                        <td style="width:70px;">
                            <form method="post">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= (int) $n['id'] ?>">
                                <input type="hidden" name="title" value="<?= e($n['title']) ?>">
                                <input type="hidden" name="excerpt" value="<?= e($n['excerpt']) ?>">
                                <input type="hidden" name="url" value="<?= e($n['url']) ?>">
                                <input type="hidden" name="published_at" value="<?= e($n['published_at']) ?>">
                                <input type="hidden" name="featured" value="<?= (int) $n['featured'] ?>">
                                <input type="number" name="sort_order" value="<?= (int) $n['sort_order'] ?>" min="0" style="width:60px;" onchange="this.form.submit()">
                            </form>
                        </td>
                        <td class="admin-actions-row" style="gap:6px; flex-wrap:wrap;">
                            <button class="admin-btn admin-btn--primary" style="font-size:.75rem;"
                                onclick="toggleEdit(<?= (int) $n['id'] ?>)">Editar</button>

                            <?php if (!$n['featured']): ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="action" value="feature">
                                    <input type="hidden" name="id" value="<?= (int) $n['id'] ?>">
                                    <button class="admin-btn admin-btn--warning" style="font-size:.75rem;" type="submit">⭐ Destaque</button>
                                </form>
                            <?php endif; ?>

                            <form method="post" onsubmit="return confirm('Excluir esta notícia?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $n['id'] ?>">
                                <button class="admin-btn admin-btn--danger" style="font-size:.75rem;" type="submit">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <!-- Inline edit form -->
                    <tr id="edit-<?= (int) $n['id'] ?>" style="display:<?= ($editNews && (int)$editNews['id'] === (int)$n['id']) ? 'table-row' : 'none' ?>;">
                        <td colspan="6" style="padding:20px; background:#f9f9ff;">
                            <form method="post" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= (int) $n['id'] ?>">
                                <div class="admin-form-grid admin-form-grid--2" style="margin-bottom:14px;">
                                    <div class="admin-field">
                                        <label>Título</label>
                                        <input type="text" name="title" value="<?= e($n['title']) ?>" required>
                                    </div>
                                    <div class="admin-field">
                                        <label>Link da notícia</label>
                                        <input type="text" name="url" value="<?= e($n['url']) ?>" placeholder="https://...">
                                    </div>
                                    <div class="admin-field" style="grid-column:1/-1;">
                                        <label>Resumo</label>
                                        <textarea name="excerpt" rows="2"><?= e($n['excerpt']) ?></textarea>
                                    </div>
                                    <div class="admin-field">
                                        <label>Data de publicação</label>
                                        <input type="date" name="published_at" value="<?= e($n['published_at']) ?>" required>
                                    </div>
                                    <div class="admin-field">
                                        <label>Imagem <small style="opacity:.6;">(JPG, PNG, WebP — máx 5 MB)</small></label>
                                        <?php if ($newsModel->hasImage((int) $n['id'])): ?>
                                            <img src="../news-image.php?id=<?= (int) $n['id'] ?>" style="height:60px; border-radius:6px; object-fit:cover; display:block; margin-bottom:6px;">
                                        <?php endif; ?>
                                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
                                    </div>
                                    <div class="admin-field">
                                        <label>Tipo</label>
                                        <select name="featured">
                                            <option value="0" <?= !$n['featured'] ? 'selected' : '' ?>>Regular</option>
                                            <option value="1" <?= $n['featured'] ? 'selected' : '' ?>>⭐ Destaque</option>
                                        </select>
                                        <small style="opacity:.6;">Ao marcar como destaque, a notícia atual de destaque será rebaixada.</small>
                                    </div>
                                    <div class="admin-field">
                                        <label>Ordem</label>
                                        <input type="number" name="sort_order" value="<?= (int) $n['sort_order'] ?>" min="0">
                                    </div>
                                </div>
                                <div class="admin-actions-row">
                                    <button type="submit" class="admin-btn admin-btn--primary">Salvar alterações</button>
                                    <button type="button" class="admin-btn admin-btn--secondary" onclick="toggleEdit(<?= (int) $n['id'] ?>)">Cancelar</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- ── Nova notícia ────────────────────────────────────────────── -->
<?php if ($count < 5): ?>
<section class="admin-panel" id="form-new" style="display:none;">
    <div class="admin-panel__header"><h2>Nova notícia</h2></div>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create">
        <div class="admin-form-grid admin-form-grid--2">
            <div class="admin-field">
                <label>Título <span style="color:#e03e5e;">*</span></label>
                <input type="text" name="title" required placeholder="Ex: UNIVAG inaugura novo laboratório">
            </div>
            <div class="admin-field">
                <label>Link da notícia</label>
                <input type="text" name="url" placeholder="https://...">
            </div>
            <div class="admin-field" style="grid-column:1/-1;">
                <label>Resumo</label>
                <textarea name="excerpt" rows="2" placeholder="Breve descrição exibida no card"></textarea>
            </div>
            <div class="admin-field">
                <label>Data de publicação <span style="color:#e03e5e;">*</span></label>
                <input type="date" name="published_at" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="admin-field">
                <label>Imagem <small style="opacity:.6;">(JPG, PNG, WebP — máx 5 MB)</small></label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
            </div>
            <div class="admin-field">
                <label>Tipo</label>
                <select name="featured">
                    <option value="0">Regular</option>
                    <option value="1">⭐ Destaque (exibida maior na home)</option>
                </select>
            </div>
            <div class="admin-field">
                <label>Ordem de exibição</label>
                <input type="number" name="sort_order" value="<?= $count ?>" min="0">
            </div>
        </div>
        <div class="admin-actions-row" style="margin-top:16px;">
            <button type="submit" class="admin-btn admin-btn--success">Publicar notícia</button>
        </div>
    </form>
</section>
<?php endif; ?>

<script>
function toggleEdit(id) {
    const row = document.getElementById('edit-' + id);
    document.querySelectorAll('[id^="edit-"]').forEach(r => { if (r !== row) r.style.display = 'none'; });
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
</script>

<?php require BASE_PATH . '/app/Views/admin/partials/footer.php'; ?>
