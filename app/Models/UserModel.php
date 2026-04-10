<?php

declare(strict_types=1);

namespace App\Models;

final class UserModel
{
    // ── Read ────────────────────────────────────────────────────────────

    public function all(): array
    {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->query("SELECT id, username, role, active, created_at, updated_at FROM cms_users ORDER BY id ASC");
            return $stmt->fetchAll() ?: [];
        } catch (\Throwable) {
            // Fallback: wrap file-based users as rows
            $rows = [];
            foreach (array_keys(users_data()) as $username) {
                $rows[] = ['id' => 0, 'username' => $username, 'role' => 'admin', 'active' => 1, 'created_at' => '', 'updated_at' => ''];
            }
            return $rows;
        }
    }

    public function find(int $id): ?array
    {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare("SELECT id, username, role, active, created_at, updated_at FROM cms_users WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function existsByUsername(string $username, int $excludeId = 0): bool
    {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM cms_users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $excludeId]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    // ── Auth ────────────────────────────────────────────────────────────

    public function verifyByUsername(string $username, string $password): bool
    {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare("SELECT password_hash FROM cms_users WHERE username = ? AND active = 1");
            $stmt->execute([$username]);
            $hash = $stmt->fetchColumn();
            if (!$hash) return false;

            if (!password_verify($password, (string) $hash)) return false;

            // Rehash if needed
            if (password_needs_rehash((string) $hash, PASSWORD_ARGON2ID, argon2_options())) {
                $this->updateHash($username, hash_password($password));
            }
            return true;
        } catch (\Throwable) {
            // Fallback to file-based users
            return $this->verifyFileFallback($username, $password);
        }
    }

    // ── Write ───────────────────────────────────────────────────────────

    /**
     * Create a new CMS user.
     * @throws \RuntimeException
     */
    public function create(string $username, string $password, string $role = 'admin'): void
    {
        if ($username === '') throw new \RuntimeException('Nome de usuário obrigatório.');
        if (strlen($password) < 8) throw new \RuntimeException('Senha precisa ter no mínimo 8 caracteres.');
        if (!in_array($role, ['admin', 'editor'], true)) throw new \RuntimeException('Role inválida.');
        if ($this->existsByUsername($username)) throw new \RuntimeException("Usuário '{$username}' já existe.");

        $pdo  = get_pdo();
        $stmt = $pdo->prepare("INSERT INTO cms_users (username, password_hash, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, hash_password($password), $role]);

        audit_log('user_created', 'info', $_SESSION['admin_user'] ?? null,
            "Usuário '{$username}' criado com role '{$role}'");
    }

    /**
     * Update username and/or role of an existing user.
     * @throws \RuntimeException
     */
    public function update(int $id, string $username, string $role, bool $active): void
    {
        if ($username === '') throw new \RuntimeException('Nome de usuário obrigatório.');
        if (!in_array($role, ['admin', 'editor'], true)) throw new \RuntimeException('Role inválida.');
        if ($this->existsByUsername($username, $id)) throw new \RuntimeException("Usuário '{$username}' já existe.");

        $pdo  = get_pdo();
        $stmt = $pdo->prepare("UPDATE cms_users SET username = ?, role = ?, active = ? WHERE id = ?");
        $stmt->execute([$username, $role, $active ? 1 : 0, $id]);

        audit_log('user_updated', 'info', $_SESSION['admin_user'] ?? null,
            "Usuário ID {$id} atualizado para '{$username}'");
    }

    /**
     * Reset password for a user.
     * @throws \RuntimeException
     */
    public function savePassword(string $username, string $password): void
    {
        if (strlen($password) < 8) throw new \RuntimeException('Senha precisa ter no mínimo 8 caracteres.');

        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare("UPDATE cms_users SET password_hash = ? WHERE username = ?");
            $stmt->execute([hash_password($password), $username]);
            // Also update file-based fallback
            $users = users_data();
            if (isset($users[$username])) {
                $users[$username] = hash_password($password);
                save_users($users);
            }
        } catch (\Throwable) {
            // Fallback to file only
            $users = users_data();
            $users[$username] = hash_password($password);
            save_users($users);
        }

        audit_log('password_changed', 'info', $username, 'Senha alterada');
    }

    /**
     * Admin resets another user's password.
     * @throws \RuntimeException
     */
    public function resetPassword(int $id, string $newPassword): void
    {
        if (strlen($newPassword) < 8) throw new \RuntimeException('Senha precisa ter no mínimo 8 caracteres.');

        $pdo  = get_pdo();
        $stmt = $pdo->prepare("UPDATE cms_users SET password_hash = ? WHERE id = ?");
        $stmt->execute([hash_password($newPassword), $id]);

        audit_log('password_reset', 'warning', $_SESSION['admin_user'] ?? null,
            "Senha resetada para usuário ID {$id}");
    }

    public function delete(int $id): void
    {
        $pdo  = get_pdo();
        // Prevent deleting the last active admin
        $count = (int) $pdo->query("SELECT COUNT(*) FROM cms_users WHERE active = 1 AND role = 'admin'")->fetchColumn();
        $row   = $this->find($id);
        if ($count <= 1 && $row && $row['role'] === 'admin' && $row['active']) {
            throw new \RuntimeException('Não é possível excluir o único administrador ativo.');
        }
        $pdo->prepare("DELETE FROM cms_users WHERE id = ?")->execute([$id]);
        audit_log('user_deleted', 'warning', $_SESSION['admin_user'] ?? null,
            "Usuário ID {$id} excluído");
    }

    // ── Private helpers ─────────────────────────────────────────────────

    private function updateHash(string $username, string $hash): void
    {
        try {
            $pdo  = get_pdo();
            $pdo->prepare("UPDATE cms_users SET password_hash = ? WHERE username = ?")->execute([$hash, $username]);
        } catch (\Throwable) { /* silent */ }
    }

    private function verifyFileFallback(string $username, string $password): bool
    {
        $users = users_data();
        if (!isset($users[$username])) return false;
        return password_verify($password, $users[$username]);
    }
}
