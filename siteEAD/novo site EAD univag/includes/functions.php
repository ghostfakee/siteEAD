<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
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

function admin_attempt_login(string $username, string $password): bool
{
    $users = users_data();
    if (!isset($users[$username])) {
        return false;
    }

    if (!password_verify($password, $users[$username])) {
        return false;
    }

    $_SESSION['admin_user'] = $username;
    return true;
}

function admin_logout(): void
{
    unset($_SESSION['admin_user']);
    session_regenerate_id(true);
}

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
            'url' => trim((string) ($item['url'] ?? '')),
            'tags' => array_values(array_filter(array_map('trim', explode(',', (string) ($item['tags'] ?? ''))))),
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

function youtube_embed_url(string $url): string
{
    $trimmed = trim($url);
    if ($trimmed === '') {
        return '';
    }

    if (str_contains($trimmed, 'youtube.com/embed/')) {
        return $trimmed;
    }

    if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([^&?/]+)~', $trimmed, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }

    if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $trimmed, $matches)) {
        return 'https://player.vimeo.com/video/' . $matches[1];
    }

    return $trimmed;
}
