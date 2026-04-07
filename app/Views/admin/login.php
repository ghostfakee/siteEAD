<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CMS UNIVAG</title>
    <link rel="stylesheet" href="../assets/css/site.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="admin-login">
    <form method="post" class="admin-login__card">
        <?= csrf_field() ?>
        <h1>CMS UNIVAG</h1>
        <?php if ($error !== ''): ?>
            <div class="admin-alert admin-alert--error"><?= e($error) ?></div>
        <?php endif; ?>
        <div class="admin-field"><label>Usuário</label><input type="text" name="username" required autocomplete="username"></div>
        <div class="admin-field"><label>Senha</label><input type="password" name="password" required autocomplete="current-password"></div>
        <div class="admin-actions-row">
            <button type="submit" class="admin-btn admin-btn--primary">Entrar</button>
            <a href="../index.php" class="admin-btn admin-btn--secondary">Voltar ao site</a>
        </div>
    </form>
</body>
</html>
