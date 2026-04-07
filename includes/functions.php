<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function is_logged_in(): bool
{
    return isset($_SESSION['admin_user']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

// ── Argon2 + timing-safe login ───────────────────────────────────────────────

function admin_attempt_login(string $username, string $password): bool
{
    $ip = get_client_ip();

    // Rate limiting — blocks after 5 failures per 15 min per user/IP
    if (!check_rate_limit($username, $ip)) {
        audit_log('login_blocked', 'warning', $username, 'Rate limit atingido');
        return false;
    }

    $users = users_data();

    // Always run password_verify (timing-safe: prevents user enumeration)
    $storedHash = $users[$username] ?? password_hash('__dummy__', PASSWORD_ARGON2ID);
    $valid      = isset($users[$username]) && password_verify($password, $storedHash);

    if (!$valid) {
        audit_log('login_failure', 'warning', $username, 'Credenciais inválidas', ['ip' => $ip]);
        return false;
    }

    // Rehash to Argon2id if hash is outdated (e.g. bcrypt from old version)
    if (password_needs_rehash($storedHash, PASSWORD_ARGON2ID, argon2_options())) {
        $users[$username] = password_hash($password, PASSWORD_ARGON2ID, argon2_options());
        save_users($users);
    }

    // Session fixation protection
    session_regenerate_id(true);
    $_SESSION['admin_user']    = $username;
    $_SESSION['admin_login_at'] = time();
    $_SESSION['admin_ip']      = $ip;

    clear_rate_limit($username, $ip);
    audit_log('login_success', 'info', $username, 'Login realizado com sucesso', ['ip' => $ip]);

    return true;
}

function admin_logout(): void
{
    $user = $_SESSION['admin_user'] ?? null;
    audit_log('logout', 'info', $user, 'Logout realizado');

    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

// Argon2id options — secure defaults
function argon2_options(): array
{
    return [
        'memory_cost' => 65536,   // 64 MB
        'time_cost'   => 4,
        'threads'     => 2,
    ];
}

// ── User management with Argon2id ────────────────────────────────────────────

function hash_password(string $password): string
{
    return password_hash($password, PASSWORD_ARGON2ID, argon2_options());
}

function save_users(array $users): void
{
    ensure_storage();
    $php = "<?php\n\nreturn " . var_export($users, true) . ";\n";
    file_put_contents(USERS_FILE, $php);

    // Also persist to MySQL audit
    audit_log('users_updated', 'info', null, 'Tabela de usuários atualizada');
}

// ── List helpers ─────────────────────────────────────────────────────────────

function post_list(string $prefix, array $fields): array
{
    $items = $_POST[$prefix] ?? [];
    if (!is_array($items)) {
        return [];
    }

    $result = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }

        $normalized = [];
        foreach ($fields as $field) {
            $normalized[$field] = trim((string) ($item[$field] ?? ''));
        }

        if (implode('', $normalized) !== '') {
            $result[] = $normalized;
        }
    }

    return $result;
}

function post_tags_list(string $prefix): array
{
    $items = $_POST[$prefix] ?? [];
    if (!is_array($items)) {
        return [];
    }

    $result = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }

        $title = trim((string) ($item['title'] ?? ''));
        if ($title === '') {
            continue;
        }

        $result[] = [
            'title' => $title,
            'image' => trim((string) ($item['image'] ?? '')),
            'url'   => trim((string) ($item['url'] ?? '')),
            'tags'  => array_values(array_filter(array_map('trim', explode(',', (string) ($item['tags'] ?? ''))))),
        ];
    }

    return $result;
}

function nl2html(string $value): string
{
    return nl2br(e($value));
}

function format_date_br(?string $date): string
{
    if (!$date) {
        return '';
    }
    $timestamp = strtotime($date);
    return $timestamp ? date('d/m/Y', $timestamp) : $date;
}

// ── YouTube/Vimeo sanitized embed URL ────────────────────────────────────────

function youtube_embed_url(string $url): string
{
    $trimmed = trim($url);
    if ($trimmed === '') {
        return '';
    }

    // YouTube watch or short URL — extract and validate ID
    if (preg_match('~(?:youtube\.com/watch\?(?:.*&)?v=|youtu\.be/)([A-Za-z0-9_-]{5,20})~', $trimmed, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }

    // YouTube embed URL already — validate it
    if (preg_match('~youtube\.com/embed/([A-Za-z0-9_-]{5,20})~', $trimmed, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }

    // Vimeo — only numeric IDs
    if (preg_match('~vimeo\.com/(?:video/)?(\d{5,12})~', $trimmed, $m)) {
        return 'https://player.vimeo.com/video/' . $m[1];
    }

    // Unknown URL — reject (do not pass through)
    return '';
}
