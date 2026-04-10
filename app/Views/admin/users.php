<?php require BASE_PATH . '/app/Views/admin/partials/header.php'; ?>
<?php if ($flash): ?><div class="admin-alert admin-alert--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>

<h1 class="admin-page-title">Usuários do CMS</h1>

<!-- ── Tabela de usuários ──────────────────────────────────────────── -->
<section class="admin-panel">
    <div class="admin-panel__header">
        <h2>Usuários cadastrados</h2>
        <button class="admin-btn admin-btn--success" onclick="document.getElementById('form-new-user').style.display=document.getElementById('form-new-user').style.display==='none'?'block':'none'">+ Novo usuário</button>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usuário</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= (int) $u['id'] ?></td>
                        <td><strong><?= e($u['username']) ?></strong></td>
                        <td>
                            <span class="admin-badge <?= $u['role'] === 'admin' ? 'admin-badge--active' : '' ?>" style="text-transform:uppercase; font-size:.72rem;">
                                <?= e($u['role']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="admin-badge <?= $u['active'] ? 'admin-badge--active' : 'admin-badge--inactive' ?>">
                                <?= $u['active'] ? 'Ativo' : 'Inativo' ?>
                            </span>
                        </td>
                        <td><small><?= e($u['created_at'] ?: '—') ?></small></td>
                        <td class="admin-actions-row" style="gap:6px; flex-wrap:wrap;">
                            <!-- Edit inline toggle -->
                            <button class="admin-btn admin-btn--primary" style="font-size:.75rem;"
                                onclick="toggleEdit(<?= (int) $u['id'] ?>)">Editar</button>

                            <!-- Reset password inline toggle -->
                            <button class="admin-btn admin-btn--warning" style="font-size:.75rem;"
                                onclick="toggleReset(<?= (int) $u['id'] ?>)">Reset senha</button>

                            <!-- Delete (protect yourself) -->
                            <?php if ((string) $u['username'] !== (string) ($_SESSION['admin_user'] ?? '')): ?>
                                <form method="post" onsubmit="return confirm('Excluir usuário <?= e($u['username']) ?>?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                                    <button class="admin-btn admin-btn--danger" style="font-size:.75rem;" type="submit">Excluir</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <!-- Inline edit form -->
                    <tr id="edit-<?= (int) $u['id'] ?>" style="display:none;">
                        <td colspan="6" style="padding:16px; background:#f9f9ff; border-radius:0 0 12px 12px;">
                            <form method="post" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                                <div class="admin-field" style="min-width:160px;">
                                    <label>Usuário</label>
                                    <input type="text" name="username" value="<?= e($u['username']) ?>" required>
                                </div>
                                <div class="admin-field" style="min-width:120px;">
                                    <label>Role</label>
                                    <select name="role">
                                        <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        <option value="editor" <?= $u['role'] === 'editor' ? 'selected' : '' ?>>Editor</option>
                                    </select>
                                </div>
                                <div class="admin-field" style="min-width:100px;">
                                    <label>Status</label>
                                    <select name="active">
                                        <option value="1" <?= $u['active'] ? 'selected' : '' ?>>Ativo</option>
                                        <option value="0" <?= !$u['active'] ? 'selected' : '' ?>>Inativo</option>
                                    </select>
                                </div>
                                <button type="submit" class="admin-btn admin-btn--primary">Salvar</button>
                                <button type="button" class="admin-btn admin-btn--secondary" onclick="toggleEdit(<?= (int) $u['id'] ?>)">Cancelar</button>
                            </form>
                        </td>
                    </tr>
                    <!-- Inline reset password form -->
                    <tr id="reset-<?= (int) $u['id'] ?>" style="display:none;">
                        <td colspan="6" style="padding:16px; background:#fff8f0; border-radius:0 0 12px 12px;">
                            <form method="post" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
                                <input type="hidden" name="action" value="reset_password">
                                <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                                <div class="admin-field" style="min-width:200px;">
                                    <label>Nova senha <small style="opacity:.6;">(mín. 8 caracteres)</small></label>
                                    <input type="password" name="new_password" required minlength="8" autocomplete="new-password">
                                </div>
                                <div class="admin-field" style="min-width:200px;">
                                    <label>Confirmar senha</label>
                                    <input type="password" name="confirm_password" required minlength="8" autocomplete="new-password">
                                </div>
                                <div style="display:flex; align-items:center; gap:4px; background:#fef2db; border-radius:8px; padding:8px 12px; font-size:.8rem;">
                                    🔒 Argon2id · 64 MB · 4 iterações
                                </div>
                                <button type="submit" class="admin-btn admin-btn--warning">Resetar senha</button>
                                <button type="button" class="admin-btn admin-btn--secondary" onclick="toggleReset(<?= (int) $u['id'] ?>)">Cancelar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- ── Criar novo usuário ──────────────────────────────────────────── -->
<section class="admin-panel" id="form-new-user" style="display:none;">
    <div class="admin-panel__header"><h2>Criar novo usuário</h2></div>
    <form method="post" class="admin-form-grid admin-form-grid--2">
        <input type="hidden" name="action" value="create">
        <div class="admin-field">
            <label>Usuário</label>
            <input type="text" name="username" required placeholder="ex: editor1" autocomplete="off">
        </div>
        <div class="admin-field">
            <label>Role</label>
            <select name="role">
                <option value="admin">Admin</option>
                <option value="editor">Editor</option>
            </select>
        </div>
        <div class="admin-field">
            <label>Senha <small style="opacity:.6;">(mín. 8 caracteres)</small></label>
            <input type="password" name="password" required minlength="8" autocomplete="new-password">
        </div>
        <div class="admin-field">
            <label>Confirmar senha</label>
            <input type="password" name="confirm_password" required minlength="8" autocomplete="new-password">
        </div>
        <div class="admin-actions-row" style="grid-column:1/-1;">
            <button type="submit" class="admin-btn admin-btn--success">Criar usuário</button>
        </div>
    </form>
    <div style="margin-top:12px; padding:12px 16px; background:#eef2ff; border-radius:10px; font-size:.82rem; color:#3730a3;">
        🔒 Senhas armazenadas com <strong>Argon2id</strong> — 64 MB de memória, 4 iterações, 2 threads. Nunca reversível.
    </div>
</section>

<script>
function toggleEdit(id) {
    const row = document.getElementById('edit-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
function toggleReset(id) {
    const row = document.getElementById('reset-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
</script>

<?php require BASE_PATH . '/app/Views/admin/partials/footer.php'; ?>
