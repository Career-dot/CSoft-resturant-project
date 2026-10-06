<?php
/**
 * Admin auth. Deliberately separate from config/session.php
 * (customer sessions) — a different session key, so being logged
 * in as a customer never implies admin access or vice versa.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/base_url.php';
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** @return array{id:int, name:string, email:string}|null */
function zaiqa_current_admin(): ?array
{
    return $_SESSION['zaiqa_admin'] ?? null;
}

function zaiqa_login_admin(array $admin): void
{
    session_regenerate_id(true);
    $_SESSION['zaiqa_admin'] = ['id' => $admin['id'], 'name' => $admin['name'], 'email' => $admin['email']];
}

function zaiqa_logout_admin(): void
{
    unset($_SESSION['zaiqa_admin']);
    session_regenerate_id(true);
}

/** Call at the very top of a protected admin page, before any output. */
function zaiqa_require_admin(): void
{
    if (zaiqa_current_admin() === null) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

/** True if no admin account exists yet — gates admin/setup.php. */
function zaiqa_admin_table_empty(): bool
{
    $pdo = zaiqa_db();
    if ($pdo === null) {
        return false; // can't confirm emptiness without a DB — fail closed
    }
    return (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn() === 0;
}
