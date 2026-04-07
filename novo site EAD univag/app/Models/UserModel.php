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
        return isset($users[$username]) && password_verify($password, $users[$username]);
    }

    public function savePassword(string $username, string $password): void
    {
        $users = $this->all();
        $users[$username] = password_hash($password, PASSWORD_DEFAULT);
        $php = "<?php\n\nreturn " . var_export($users, true) . ";\n";
        file_put_contents(USERS_FILE, $php);
    }
}
