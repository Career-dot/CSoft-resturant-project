<?php
/**
 * POST /api/login.php
 * Body: { email, password }
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
$email = trim((string)($body['email'] ?? ''));
$password = (string)($body['password'] ?? '');

if ($email === '' || $password === '') {
    http_response_code(422);
    echo json_encode(['error' => 'Email and password are required.']);
    exit;
}

$pdo = zaiqa_db();
if ($pdo === null) {
    http_response_code(503);
    echo json_encode(['error' => 'Accounts need the database connected — see README.md setup steps.']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, name, email, password_hash FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    error_log('[zaiqa] login.php query failed (has database/step6_auth.sql been imported?): ' . $e->getMessage());
    http_response_code(503);
    echo json_encode(['error' => 'Accounts aren’t set up yet — see README.md setup steps (database/step6_auth.sql).', 'debug' => $e->getMessage()]);
    exit;
}

if (!$user || !password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Incorrect email or password.']);
    exit;
}

$publicUser = ['id' => (int)$user['id'], 'name' => $user['name'], 'email' => $user['email']];
zaiqa_login_user($publicUser);

echo json_encode($publicUser);
