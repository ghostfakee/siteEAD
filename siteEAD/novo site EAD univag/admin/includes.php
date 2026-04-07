<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

function admin_require_login(): void
{
    require_login();
}

function admin_flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function admin_consume_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}

function admin_header(string $title, string $active = 'dashboard'): void
{
    $user = $_SESSION['admin_user'] ?? 'admin';
    $items = [
        'dashboard' => ['Dashboard', 'index.php'],
        'banners' => ['Banners', 'banners.php'],
        'upload' => ['Novo Banner', 'upload.php'],
        'content' => ['Conteudo do Site', 'content.php'],
        'manuals' => ['Manuais', 'manuals.php'],
        'settings' => ['Configuracoes', 'settings.php'],
    ];
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= e($title) ?> - CMS UNIVAG</title>
        <link rel="stylesheet" href="../assets/css/site.css">
        <link rel="stylesheet" href="css/admin.css">
    </head>
    <body class="admin-shell">
        <header class="admin-topbar">
            <div class="admin-topbar__title">
                <strong>CMS UNIVAG</strong>
                <span><?= e($title) ?></span>
            </div>
            <div class="admin-topbar__user">
                <span>Ola, <?= e($user) ?></span>
                <a class="admin-btn admin-btn--danger" href="logout.php">Sair</a>
            </div>
        </header>
        <aside class="admin-sidebar">
            <nav>
                <?php foreach ($items as $key => [$label, $url]): ?>
                    <a href="<?= e($url) ?>" class="<?= $active === $key ? 'is-active' : '' ?>"><?= e($label) ?></a>
                <?php endforeach; ?>
                <a href="../index.php" target="_blank" rel="noopener">Ver Site</a>
            </nav>
        </aside>
        <main class="admin-main">
    <?php
}

function admin_footer(): void
{
    ?>
        </main>
    </body>
    </html>
    <?php
}

function admin_sorted_slides(array $content): array
{
    $slides = $content['hero']['slides'] ?? [];
    usort($slides, static fn(array $a, array $b): int => ((int) ($a['order'] ?? 0)) <=> ((int) ($b['order'] ?? 0)));
    return $slides;
}

function admin_find_slide(array $content, string $id): ?array
{
    foreach (($content['hero']['slides'] ?? []) as $slide) {
        if (($slide['id'] ?? '') === $id) {
            return $slide;
        }
    }
    return null;
}

function admin_replace_slide(array &$content, string $id, array $newSlide): bool
{
    foreach (($content['hero']['slides'] ?? []) as $index => $slide) {
        if (($slide['id'] ?? '') === $id) {
            $content['hero']['slides'][$index] = $newSlide;
            return true;
        }
    }
    return false;
}

function admin_delete_slide(array &$content, string $id): bool
{
    $slides = $content['hero']['slides'] ?? [];
    foreach ($slides as $index => $slide) {
        if (($slide['id'] ?? '') === $id) {
            array_splice($slides, $index, 1);
            $content['hero']['slides'] = $slides;
            return true;
        }
    }
    return false;
}

function admin_generate_id(): string
{
    return bin2hex(random_bytes(6));
}

function admin_upload_image(array $file, string $folder = 'slides'): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return '';
    }

    $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed, true)) {
        throw new RuntimeException('Formato de imagem invalido.');
    }

    $targetDir = MEDIA_PATH . '/' . $folder;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $filename = admin_generate_id() . '.' . $ext;
    $target = $targetDir . '/' . $filename;
    if (!move_uploaded_file((string) $file['tmp_name'], $target)) {
        throw new RuntimeException('Falha ao salvar a imagem enviada.');
    }

    return 'media/' . $folder . '/' . $filename;
}

function admin_save_password(string $username, string $newPassword): void
{
    $users = users_data();
    $users[$username] = password_hash($newPassword, PASSWORD_DEFAULT);

    $php = "<?php\n\nreturn " . var_export($users, true) . ";\n";
    file_put_contents(USERS_FILE, $php);
}

function admin_normalize_slide(array $data, ?array $existing = null): array
{
    $slide = $existing ?? [];
    $slide['id'] = $existing['id'] ?? admin_generate_id();
    $slide['title'] = trim((string) ($data['title'] ?? ''));
    $slide['subtitle'] = trim((string) ($data['subtitle'] ?? ''));
    $slide['badge'] = trim((string) ($data['badge'] ?? ''));
    $slide['image'] = trim((string) ($data['image'] ?? ($existing['image'] ?? '')));
    $slide['cta_label'] = trim((string) ($data['cta_label'] ?? ''));
    $slide['cta_url'] = trim((string) ($data['cta_url'] ?? ''));
    $slide['secondary_cta_label'] = trim((string) ($data['secondary_cta_label'] ?? ''));
    $slide['secondary_cta_url'] = trim((string) ($data['secondary_cta_url'] ?? ''));
    $slide['info_title'] = trim((string) ($data['info_title'] ?? ''));
    $slide['info_lines'] = array_values(array_filter(array_map('trim', explode("\n", (string) ($data['info_lines'] ?? '')))));
    $slide['active'] = !empty($data['active']);
    $slide['order'] = (int) ($data['order'] ?? 0);
    return $slide;
}
