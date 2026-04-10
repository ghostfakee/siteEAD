<?php require BASE_PATH . '/app/Views/admin/partials/header.php'; ?>
<?php if ($flash): ?>
    <div class="admin-alert admin-alert--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>

<h1 class="admin-page-title">Manuais e Tutoriais</h1>

<?php
    $catAluno     = null;
    $catProfessor = null;
    foreach ($categories as $cat) {
        if ($cat['category_key'] === 'aluno') $catAluno = $cat;
        if ($cat['category_key'] === 'professor') $catProfessor = $cat;
    }
?>


<!-- ── Seção Manuais do Aluno ──────────────────────────────────────── -->
<section class="admin-panel" style="margin-top:32px;">
    <div class="admin-panel__header" style="display:flex; align-items:center; justify-content:space-between;">
        <h2>Manuais do Aluno</h2>
        <button type="button" class="admin-btn admin-btn--primary" onclick="toggleAddForm('add-aluno')">+ Lançar manual para alunos</button>
    </div>

    <!-- Add form (hidden by default) -->
    <div id="add-aluno" style="display:none; margin-bottom:24px;">
        <form method="post" enctype="multipart/form-data" class="admin-subsection">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="category" value="aluno">
            <h3 style="margin:0 0 16px;">Novo Manual — Aluno</h3>
            <div class="admin-form-grid admin-form-grid--2">
                <div class="admin-field"><label>Título *</label><input type="text" name="title" required placeholder="Ex: Manual do Aluno Digital"></div>
                <div class="admin-field"><label>Descrição</label><textarea name="description" rows="3" placeholder="Breve descrição do manual"></textarea></div>
                <div class="admin-field">
                    <label>Imagem de capa</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
                    <small style="opacity:.6;">JPG, PNG, WebP ou GIF — salvo no banco de dados</small>
                </div>
                <div class="admin-field">
                    <label>Tipo de mídia</label>
                    <select name="media_type" onchange="toggleMediaFields(this, 'add-aluno')">
                        <option value="pdf">PDF / Link externo</option>
                        <option value="video">Vídeo embutido</option>
                    </select>
                </div>
                <div class="admin-field add-aluno-pdf">
                    <label>Upload do PDF <small style="font-weight:400; opacity:.7;">(salvo no banco)</small></label>
                    <input type="file" name="pdf" accept="application/pdf,.pdf">
                    <small style="opacity:.6;">Ou informe uma URL externa abaixo</small>
                </div>
                <div class="admin-field add-aluno-pdf"><label>URL externa do PDF <small style="font-weight:400; opacity:.7;">(opcional se fez upload)</small></label><input type="text" name="pdf_url" placeholder="https://..."></div>
                <div class="admin-field add-aluno-video" style="display:none;"><label>URL do Vídeo (YouTube/Vimeo)</label><input type="text" name="video_url" placeholder="https://youtube.com/watch?v=..."></div>
            </div>
            <div class="admin-actions-row" style="margin-top:16px;">
                <button type="submit" class="admin-btn admin-btn--primary">Salvar manual</button>
                <button type="button" class="admin-btn" onclick="toggleAddForm('add-aluno')">Cancelar</button>
            </div>
        </form>
    </div>

    <!-- Existing manuals list -->
    <?php $manuaisAluno = $manualsByCategory['aluno'] ?? []; ?>
    <?php if (empty($manuaisAluno)): ?>
        <p style="opacity:.6; padding:12px 0;">Nenhum manual cadastrado ainda.</p>
    <?php else: ?>
        <div class="admin-manuals-list">
            <?php foreach ($manuaisAluno as $manual): ?>
                <?php $isEditing = ($action === 'edit' && ($editManual['id'] ?? 0) === (int)$manual['id']); ?>
                <div class="admin-manual-item">
                    <div class="admin-manual-item__thumb">
                        <?php if ($manual['image_mime']): ?>
                            <img src="../image.php?id=<?= (int)$manual['id'] ?>&type=manual" alt="<?= e($manual['title']) ?>">
                        <?php else: ?>
                            <span class="no-img">📄</span>
                        <?php endif; ?>
                    </div>
                    <div class="admin-manual-item__info">
                        <strong><?= e($manual['title']) ?></strong>
                        <span><?= e($manual['description']) ?></span>
                        <small><?= $manual['media_type'] === 'video' ? 'Vídeo embutido' : 'PDF/Link' ?> • <?= e(date('d/m/Y', strtotime($manual['created_at']))) ?></small>
                    </div>
                    <div class="admin-manual-item__actions">
                        <a class="admin-btn admin-btn--sm" href="manuals.php?action=edit&id=<?= (int)$manual['id'] ?>">Editar</a>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Remover este manual?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$manual['id'] ?>">
                            <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">Remover</button>
                        </form>
                    </div>
                </div>

                <?php if ($isEditing && $editManual): ?>
                    <div class="admin-subsection" style="margin:8px 0 16px; padding:20px;">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= (int)$editManual['id'] ?>">
                            <h3 style="margin:0 0 16px;">Editando: <?= e($editManual['title']) ?></h3>
                            <div class="admin-form-grid admin-form-grid--2">
                                <div class="admin-field"><label>Título</label><input type="text" name="title" value="<?= e($editManual['title']) ?>" required></div>
                                <div class="admin-field"><label>Descrição</label><textarea name="description"><?= e($editManual['description']) ?></textarea></div>
                                <div class="admin-field">
                                    <label>Nova imagem de capa (opcional — deixe vazio para manter atual)</label>
                                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
                                    <?php if ($editManual['image_mime']): ?>
                                        <div style="margin-top:8px;"><img src="../image.php?id=<?= (int)$editManual['id'] ?>&type=manual" style="max-height:80px; border-radius:6px;"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="admin-field">
                                    <label>Tipo de mídia</label>
                                    <select name="media_type" onchange="toggleMediaFields(this, 'edit-<?= (int)$editManual['id'] ?>')">
                                        <option value="pdf" <?= $editManual['media_type'] === 'pdf' ? 'selected' : '' ?>>PDF / Link externo</option>
                                        <option value="video" <?= $editManual['media_type'] === 'video' ? 'selected' : '' ?>>Vídeo embutido</option>
                                    </select>
                                </div>
                                <div class="admin-field edit-<?= (int)$editManual['id'] ?>-pdf" <?= $editManual['media_type'] === 'video' ? 'style="display:none"' : '' ?>>
                                    <label>Upload novo PDF <small style="font-weight:400; opacity:.7;">(salvo no banco — substitui o atual)</small></label>
                                    <input type="file" name="pdf" accept="application/pdf,.pdf">
                                    <?php if ($manualModel->hasPdf((int)$editManual['id'])): ?>
                                        <small style="color:#185a2b;">✔ PDF já salvo no banco — <a href="../file.php?id=<?= (int)$editManual['id'] ?>" target="_blank">visualizar</a></small>
                                    <?php endif; ?>
                                </div>
                                <div class="admin-field edit-<?= (int)$editManual['id'] ?>-pdf" <?= $editManual['media_type'] === 'video' ? 'style="display:none"' : '' ?>>
                                    <label>URL externa do PDF <small style="font-weight:400; opacity:.7;">(use se não fizer upload)</small></label>
                                    <input type="text" name="pdf_url" value="<?= e($editManual['pdf_url']) ?>" placeholder="https://...">
                                </div>
                                <div class="admin-field edit-<?= (int)$editManual['id'] ?>-video" <?= $editManual['media_type'] !== 'video' ? 'style="display:none"' : '' ?>>
                                    <label>URL do Vídeo</label><input type="text" name="video_url" value="<?= e($editManual['video_url']) ?>">
                                </div>
                                <div class="admin-field"><label>Ordem</label><input type="number" name="sort_order" value="<?= (int)$editManual['sort_order'] ?>" min="0"></div>
                            </div>
                            <div class="admin-actions-row" style="margin-top:16px;">
                                <button type="submit" class="admin-btn admin-btn--primary">Salvar alterações</button>
                                <a href="manuals.php" class="admin-btn">Cancelar</a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- ── Seção Manuais do Professor ──────────────────────────────────── -->
<section class="admin-panel" style="margin-top:32px;">
    <div class="admin-panel__header" style="display:flex; align-items:center; justify-content:space-between;">
        <h2>Manuais do Professor</h2>
        <button type="button" class="admin-btn admin-btn--primary" onclick="toggleAddForm('add-professor')">+ Lançar manual para professores</button>
    </div>

    <!-- Add form (hidden by default) -->
    <div id="add-professor" style="display:none; margin-bottom:24px;">
        <form method="post" enctype="multipart/form-data" class="admin-subsection">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="category" value="professor">
            <h3 style="margin:0 0 16px;">Novo Manual — Professor</h3>
            <div class="admin-form-grid admin-form-grid--2">
                <div class="admin-field"><label>Título *</label><input type="text" name="title" required placeholder="Ex: Manual do Professor Digital"></div>
                <div class="admin-field"><label>Descrição</label><textarea name="description" rows="3" placeholder="Breve descrição do manual"></textarea></div>
                <div class="admin-field">
                    <label>Imagem de capa</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
                    <small style="opacity:.6;">JPG, PNG, WebP ou GIF — salvo no banco de dados</small>
                </div>
                <div class="admin-field">
                    <label>Tipo de mídia</label>
                    <select name="media_type" onchange="toggleMediaFields(this, 'add-professor')">
                        <option value="pdf">PDF / Link externo</option>
                        <option value="video">Vídeo embutido</option>
                    </select>
                </div>
                <div class="admin-field add-professor-pdf">
                    <label>Upload do PDF <small style="font-weight:400; opacity:.7;">(salvo no banco)</small></label>
                    <input type="file" name="pdf" accept="application/pdf,.pdf">
                    <small style="opacity:.6;">Ou informe uma URL externa abaixo</small>
                </div>
                <div class="admin-field add-professor-pdf"><label>URL externa do PDF <small style="font-weight:400; opacity:.7;">(opcional se fez upload)</small></label><input type="text" name="pdf_url" placeholder="https://..."></div>
                <div class="admin-field add-professor-video" style="display:none;"><label>URL do Vídeo (YouTube/Vimeo)</label><input type="text" name="video_url" placeholder="https://youtube.com/watch?v=..."></div>
            </div>
            <div class="admin-actions-row" style="margin-top:16px;">
                <button type="submit" class="admin-btn admin-btn--primary">Salvar manual</button>
                <button type="button" class="admin-btn" onclick="toggleAddForm('add-professor')">Cancelar</button>
            </div>
        </form>
    </div>

    <!-- Existing manuals list -->
    <?php $manuaisProfessor = $manualsByCategory['professor'] ?? []; ?>
    <?php if (empty($manuaisProfessor)): ?>
        <p style="opacity:.6; padding:12px 0;">Nenhum manual cadastrado ainda.</p>
    <?php else: ?>
        <div class="admin-manuals-list">
            <?php foreach ($manuaisProfessor as $manual): ?>
                <?php $isEditing = ($action === 'edit' && ($editManual['id'] ?? 0) === (int)$manual['id']); ?>
                <div class="admin-manual-item">
                    <div class="admin-manual-item__thumb">
                        <?php if ($manual['image_mime']): ?>
                            <img src="../image.php?id=<?= (int)$manual['id'] ?>&type=manual" alt="<?= e($manual['title']) ?>">
                        <?php else: ?>
                            <span class="no-img">📄</span>
                        <?php endif; ?>
                    </div>
                    <div class="admin-manual-item__info">
                        <strong><?= e($manual['title']) ?></strong>
                        <span><?= e($manual['description']) ?></span>
                        <small><?= $manual['media_type'] === 'video' ? 'Vídeo embutido' : 'PDF/Link' ?> • <?= e(date('d/m/Y', strtotime($manual['created_at']))) ?></small>
                    </div>
                    <div class="admin-manual-item__actions">
                        <a class="admin-btn admin-btn--sm" href="manuals.php?action=edit&id=<?= (int)$manual['id'] ?>">Editar</a>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Remover este manual?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$manual['id'] ?>">
                            <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">Remover</button>
                        </form>
                    </div>
                </div>

                <?php if ($isEditing && $editManual): ?>
                    <div class="admin-subsection" style="margin:8px 0 16px; padding:20px;">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= (int)$editManual['id'] ?>">
                            <h3 style="margin:0 0 16px;">Editando: <?= e($editManual['title']) ?></h3>
                            <div class="admin-form-grid admin-form-grid--2">
                                <div class="admin-field"><label>Título</label><input type="text" name="title" value="<?= e($editManual['title']) ?>" required></div>
                                <div class="admin-field"><label>Descrição</label><textarea name="description"><?= e($editManual['description']) ?></textarea></div>
                                <div class="admin-field">
                                    <label>Nova imagem de capa (opcional)</label>
                                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
                                    <?php if ($editManual['image_mime']): ?>
                                        <div style="margin-top:8px;"><img src="../image.php?id=<?= (int)$editManual['id'] ?>&type=manual" style="max-height:80px; border-radius:6px;"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="admin-field">
                                    <label>Tipo de mídia</label>
                                    <select name="media_type" onchange="toggleMediaFields(this, 'edit-<?= (int)$editManual['id'] ?>')">
                                        <option value="pdf" <?= $editManual['media_type'] === 'pdf' ? 'selected' : '' ?>>PDF / Link externo</option>
                                        <option value="video" <?= $editManual['media_type'] === 'video' ? 'selected' : '' ?>>Vídeo embutido</option>
                                    </select>
                                </div>
                                <div class="admin-field edit-<?= (int)$editManual['id'] ?>-pdf" <?= $editManual['media_type'] === 'video' ? 'style="display:none"' : '' ?>>
                                    <label>Upload novo PDF <small style="font-weight:400; opacity:.7;">(salvo no banco — substitui o atual)</small></label>
                                    <input type="file" name="pdf" accept="application/pdf,.pdf">
                                    <?php if ($manualModel->hasPdf((int)$editManual['id'])): ?>
                                        <small style="color:#185a2b;">✔ PDF já salvo no banco — <a href="../file.php?id=<?= (int)$editManual['id'] ?>" target="_blank">visualizar</a></small>
                                    <?php endif; ?>
                                </div>
                                <div class="admin-field edit-<?= (int)$editManual['id'] ?>-pdf" <?= $editManual['media_type'] === 'video' ? 'style="display:none"' : '' ?>>
                                    <label>URL externa do PDF <small style="font-weight:400; opacity:.7;">(use se não fizer upload)</small></label>
                                    <input type="text" name="pdf_url" value="<?= e($editManual['pdf_url']) ?>" placeholder="https://...">
                                </div>
                                <div class="admin-field edit-<?= (int)$editManual['id'] ?>-video" <?= $editManual['media_type'] !== 'video' ? 'style="display:none"' : '' ?>>
                                    <label>URL do Vídeo</label><input type="text" name="video_url" value="<?= e($editManual['video_url']) ?>">
                                </div>
                                <div class="admin-field"><label>Ordem</label><input type="number" name="sort_order" value="<?= (int)$editManual['sort_order'] ?>" min="0"></div>
                            </div>
                            <div class="admin-actions-row" style="margin-top:16px;">
                                <button type="submit" class="admin-btn admin-btn--primary">Salvar alterações</button>
                                <a href="manuals.php" class="admin-btn">Cancelar</a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<style>
.admin-manuals-list { display: flex; flex-direction: column; gap: 8px; padding: 8px 0; }
.admin-manual-item { display: grid; grid-template-columns: 72px 1fr auto; gap: 14px; align-items: center; background: #f9f9ff; border: 1px solid var(--line); border-radius: 14px; padding: 12px; }
.admin-manual-item__thumb { width: 72px; height: 72px; border-radius: 8px; overflow: hidden; background: #eee; display: flex; align-items: center; justify-content: center; }
.admin-manual-item__thumb img { width: 100%; height: 100%; object-fit: cover; }
.admin-manual-item__thumb .no-img { font-size: 2rem; }
.admin-manual-item__info { display: flex; flex-direction: column; gap: 3px; }
.admin-manual-item__info strong { font-size: .95rem; }
.admin-manual-item__info span { font-size: .85rem; opacity: .7; }
.admin-manual-item__info small { font-size: .78rem; opacity: .5; }
.admin-manual-item__actions { display: flex; gap: 6px; }
.admin-btn--sm { padding: 6px 12px; font-size: .82rem; }
.admin-btn--danger { background: #c0392b; color: #fff; }
.admin-btn--danger:hover { background: #a93226; }
</style>

<script>
function toggleAddForm(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

function toggleMediaFields(select, prefix) {
    const isPdf   = select.value === 'pdf';
    const pdfEl   = document.querySelector('.' + prefix + '-pdf');
    const videoEl = document.querySelector('.' + prefix + '-video');
    if (pdfEl)   pdfEl.style.display   = isPdf ? '' : 'none';
    if (videoEl) videoEl.style.display = isPdf ? 'none' : '';
}
</script>

<?php require BASE_PATH . '/app/Views/admin/partials/footer.php'; ?>
