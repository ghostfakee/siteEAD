<?php

declare(strict_types=1);

// ── Security Headers ────────────────────────────────────────────────────────

function send_security_headers(): void
{
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; frame-src https://www.youtube.com https://player.vimeo.com; font-src 'self' data:");
}

// ── CSRF ────────────────────────────────────────────────────────────────────

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $token = trim((string) ($_POST['_csrf'] ?? ''));
    $expected = (string) ($_SESSION['csrf_token'] ?? '');

    if ($expected === '' || !hash_equals($expected, $token)) {
        audit_log('csrf_failure', 'critical', null, 'Token CSRF inválido ou ausente');
        http_response_code(403);
        exit('Ação proibida: token CSRF inválido.');
    }

    // Rotate token after use
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── Rate Limiting (MySQL-backed) ────────────────────────────────────────────

function check_rate_limit(string $username, string $ip, int $maxAttempts = 5, int $windowSeconds = 900): bool
{
    try {
        $pdo = get_pdo();

        // Clean old entries
        $pdo->prepare(
            "DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL ? SECOND)"
        )->execute([$windowSeconds]);

        // Count recent attempts
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM login_attempts
             WHERE (username = ? OR ip = ?) AND attempted_at >= DATE_SUB(NOW(), INTERVAL ? SECOND)"
        );
        $stmt->execute([$username, $ip, $windowSeconds]);
        $count = (int) $stmt->fetchColumn();

        if ($count >= $maxAttempts) {
            audit_log('rate_limit_hit', 'warning', $username, "Bloqueado após {$count} tentativas", ['ip' => $ip]);
            return false;
        }

        // Record this attempt
        $pdo->prepare(
            "INSERT INTO login_attempts (username, ip) VALUES (?, ?)"
        )->execute([$username, $ip]);

        return true;
    } catch (\Throwable) {
        return true; // fail open if DB unavailable
    }
}

function clear_rate_limit(string $username, string $ip): void
{
    try {
        $pdo = get_pdo();
        $pdo->prepare(
            "DELETE FROM login_attempts WHERE username = ? OR ip = ?"
        )->execute([$username, $ip]);
    } catch (\Throwable) { /* silent */ }
}

// ── Audit Log (MySQL-backed) ─────────────────────────────────────────────────

function audit_log(
    string $eventType,
    string $severity = 'info',
    ?string $user = null,
    string $description = '',
    array $context = []
): void {
    $user      = $user ?? ($_SESSION['admin_user'] ?? null);
    $ip        = get_client_ip();
    $userAgent = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500);

    try {
        $pdo = get_pdo();
        $pdo->prepare(
            "INSERT INTO audit_log (event_type, severity, user, ip, user_agent, description, context)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        )->execute([
            $eventType,
            $severity,
            $user,
            $ip,
            $userAgent,
            $description,
            $context !== [] ? json_encode($context, JSON_UNESCAPED_UNICODE) : null,
        ]);
    } catch (\Throwable) {
        // Fallback to error_log
        error_log("[AUDIT] [{$severity}] {$eventType} | user={$user} ip={$ip} | {$description}");
    }
}

// ── DB Observability ─────────────────────────────────────────────────────────

function db_observe(callable $queryFn, string $querySample, string $calledFrom = ''): mixed
{
    $start = microtime(true);
    $result = $queryFn();
    $elapsed = (microtime(true) - $start) * 1000;

    // Only log slow queries (> 100ms) or all in debug mode
    $slowThreshold = 100;
    if ($elapsed > $slowThreshold) {
        try {
            $pdo = get_pdo();
            $pdo->prepare(
                "INSERT INTO db_metrics (query_hash, query_sample, exec_time_ms, called_from)
                 VALUES (?, ?, ?, ?)"
            )->execute([
                md5($querySample),
                substr($querySample, 0, 500),
                round($elapsed, 2),
                $calledFrom,
            ]);
        } catch (\Throwable) { /* silent */ }

        audit_log('slow_query', 'warning', null,
            "Query lenta: {$elapsed}ms",
            ['query' => substr($querySample, 0, 200), 'from' => $calledFrom]
        );
    }

    return $result;
}

// ── Input validation helpers ─────────────────────────────────────────────────

function get_client_ip(): string
{
    $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = trim(explode(',', (string) $_SERVER[$header])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return '0.0.0.0';
}

function validate_slug(string $slug): bool
{
    return (bool) preg_match('/^[a-z0-9\-]+$/', $slug);
}

function sanitize_url(string $url): string
{
    $url = trim($url);
    if ($url === '') {
        return '';
    }
    // Allow only http/https and relative paths
    if (!preg_match('~^(https?://|/)~i', $url) && $url !== '#') {
        return '#';
    }
    return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
}
