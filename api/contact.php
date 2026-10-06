<?php
/**
 * POST /api/contact.php
 * Body: { name, email, message }
 *
 * Stores a contact message. Same graceful-degrade pattern as
 * reserve.php/order.php: if MySQL isn't connected, still confirms
 * receipt but flags persisted: false rather than pretending to
 * fail outright.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Use POST.']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true) ?? [];
$name = trim((string)($body['name'] ?? ''));
$email = trim((string)($body['email'] ?? ''));
$message = trim((string)($body['message'] ?? ''));

$errors = [];
if ($name === '') $errors['name'] = 'Name is required.';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'A valid email is required.';
if ($message === '') $errors['message'] = 'Message is required.';

if ($errors) {
    http_response_code(422);
    echo json_encode(['errors' => $errors]);
    exit;
}

$pdo = zaiqa_db();
$persisted = false;

if ($pdo !== null) {
    try {
        $pdo->prepare('INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)')
            ->execute(['name' => $name, 'email' => $email, 'message' => $message]);
        $persisted = true;
    } catch (PDOException $e) {
        error_log('[zaiqa] contact.php insert failed (has database/step9_contact.sql been imported?): ' . $e->getMessage());
    }
}

echo json_encode(['ok' => true, 'persisted' => $persisted]);
