<?php

declare(strict_types=1);

namespace App\Models;

final class UserModel
{
    public function all(): array
    {
        return users_data();
    }

    public function verify(string $username, string $password): bool
    {
        $users = $this->all();
        if (!isset($users[$username])) {
            return false;
        }
        return password_verify($password, $users[$username]);
    }

    public function savePassword(string $username, string $password): void
    {
        $users = $this->all();
        // Always hash with Argon2id
        $users[$username] = hash_password($password);
        save_users($users);
        audit_log('password_changed', 'info', $username, 'Senha alterada pelo usuário');
    }
}
