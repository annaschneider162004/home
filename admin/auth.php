<?php
/**
 * Admin authentication guard
 * Include at the top of every protected admin page
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_name(SESSION_NAME);
    session_start();
}

function isLoggedIn(): bool {
    return !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_user']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . ADMIN_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
    // Regenerate session ID periodically to prevent fixation
    if (empty($_SESSION['last_regen']) || time() - $_SESSION['last_regen'] > 300) {
        session_regenerate_id(true);
        $_SESSION['last_regen'] = time();
    }
}

function adminUser(): array {
    return [
        'id'   => $_SESSION['admin_id'] ?? 0,
        'user' => $_SESSION['admin_user'] ?? '',
        'name' => $_SESSION['admin_name'] ?? 'Admin',
        'role' => $_SESSION['admin_role'] ?? 'admin',
    ];
}
