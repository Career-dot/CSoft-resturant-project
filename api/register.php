<?php
/**
 * POST /api/register.php
 * Body: { name, email, phone, password, confirm_password }
 *
 * Accounts genuinely need MySQL — unlike the menu/mood endpoints,
 * there's no meaningful seed fallback for "create a user", so this
 * returns a clear error if the database isn't connected rather
 * than pretending to succeed.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Use POST.']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true) ?? [];
$errors = [];

$name = trim((string)($body['name'] ?? ''));
$email = trim((string)($body['email'] ?? ''));
$phone = trim((string)($body['phone'] ?? ''));
$password = (string)($body['password'] ?? '');
$confirm = (string)($body['confirm_password'] ?? '');

if ($name === '') {
    $errors['name'] = 'Name is required.';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'A valid email is required.';
}
if (strlen($password) < 6) {
    $errors['password'] = 'Password must be at least 6 characters.';
}
if ($password !== $confirm) {
    $errors['confirm_password'] = 'Passwords do not match.';
}

$pdo = zaiqa_db();
if ($pdo === null) {
    http_response_code(503);
    echo json_encode(['error' => 'Accounts need the database connected — see README.md setup steps.']);
    exit;
}

try {
    if (!$errors) {
        $check = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $check->execute(['email' => $email]);
        if ($check->fetch()) {
            $errors['email'] = 'An account with this email already exists.';
        }
    }

    if ($errors) {
        http_response_code(422);
        echo json_encode(['errors' => $errors]);
        exit;
    }

    $insert = $pdo->prepare('INSERT INTO users (name, email, phone, password_hash) VALUES (:name, :email, :phone, :hash)');
    $insert->execute([
        'name' => $name,
        'email' => $email,
        'phone' => $phone ?: null,
        'hash' => password_hash($password, PASSWORD_DEFAULT),
    ]);

    $user = ['id' => (int)$pdo->lastInsertId(), 'name' => $name, 'email' => $email];
    zaiqa_login_user($user);

    echo json_encode($user);
} catch (PDOException $e) {
    error_log('[zaiqa] register.php query failed (has database/step6_auth.sql been imported?): ' . $e->getMessage());
    http_response_code(503);
    echo json_encode(['error' => 'Accounts aren’t set up yet — see README.md setup steps (database/step6_auth.sql).', 'debug' => $e->getMessage()]);
}
