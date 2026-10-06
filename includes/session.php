<?php
/**
 * Session bootstrap + auth helpers. Require this before using
 * zaiqa_current_user() anywhere — includes/header.php already
 * does this for every page; API files that need to know who's
 * logged in (favorites, my-taste, food-passport, taste, order)
 * require it directly. Pages that require login outright should
 * call zaiqa_require_login() before any other output.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** The logged-in user's {id, name, email}, or null if a guest. */
function zaiqa_current_user(): ?array
{
    return $_SESSION['zaiqa_user'] ?? null;
}

function zaiqa_login_user(array $user): void
{
    $_SESSION['zaiqa_user'] = [
        'id' => (int)$user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
    ];
    session_regenerate_id(true);
}

function zaiqa_logout_user(): void
{
    unset($_SESSION['zaiqa_user']);
    session_regenerate_id(true);
}

/**
 * Redirects to the login page (with a return link back to the
 * current page) if not authenticated. Call at the very top of a
 * page — before includes/header.php or any other output — since
 * header() calls fail once anything has been echoed.
 */
function zaiqa_require_login(): void
{
    require_once __DIR__ . '/base_url.php';
    if (zaiqa_current_user() === null) {
        $returnTo = urlencode($_SERVER['REQUEST_URI'] ?? '');
        header('Location: ' . BASE_URL . '/pages/login.php?return=' . $returnTo);
        exit;
    }
}