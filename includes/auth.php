<?php
/**
 * Login state for customers and admins (kept separate in the session).
 */

function current_user(): ?array
{
    static $user = false;
    if ($user !== false) {
        return $user;
    }
    $id = $_SESSION['user_id'] ?? null;
    $user = $id ? (db_query('SELECT id, email, birthday FROM users WHERE id = ?', [$id])->fetch() ?: null) : null;
    return $user;
}

function login_user(int $id): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $id;
}

function require_user(): array
{
    $user = current_user();
    if (!$user) {
        $_SESSION['after_login'] = $_SERVER['REQUEST_URI'] ?? null;
        flash('info', 'Please log in first.');
        redirect('login.php');
    }
    return $user;
}

function current_admin(): ?array
{
    static $admin = false;
    if ($admin !== false) {
        return $admin;
    }
    $id = $_SESSION['admin_id'] ?? null;
    $admin = $id ? (db_query('SELECT id, username FROM admins WHERE id = ?', [$id])->fetch() ?: null) : null;
    return $admin;
}

function login_admin(int $id): void
{
    session_regenerate_id(true);
    $_SESSION['admin_id'] = $id;
}

function require_admin(): array
{
    $admin = current_admin();
    if (!$admin) {
        redirect('admin/login.php');
    }
    return $admin;
}

/**
 * Very small brute-force brake: after 5 failed logins in 10 minutes
 * (per session + form), refuse further attempts for a while.
 */
function login_throttled(string $form): bool
{
    $attempts = array_filter($_SESSION['login_fail'][$form] ?? [], fn ($t) => $t > time() - 600);
    $_SESSION['login_fail'][$form] = $attempts;
    return count($attempts) >= 5;
}

function login_failed(string $form): void
{
    $_SESSION['login_fail'][$form][] = time();
}

function login_reset(string $form): void
{
    unset($_SESSION['login_fail'][$form]);
}
