<?php
/**
 * POST /api/reserve.php
 * Body: { name, email, phone, date, time, guests, seating_area_slug,
 *         table_id, special_request }
 *
 * Validates the submission, generates a unique reference code, and
 * inserts into `reservations`. If MySQL isn't connected yet, still
 * returns a reference code so the frontend flow is demoable, but
 * flags `persisted: false` — this is a dev-time convenience, not a
 * substitute for the real database before going live.
 *
 * Note: table availability here is a simple status flag on the
 * `tables` row, not a date/time-scoped booking calendar. A real
 * deployment needs to check reservations for that specific date +
 * time window rather than a single global status — flagging that
 * as a known simplification for this step.
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
$errors = validate_reservation($body);

if ($errors) {
    http_response_code(422);
    echo json_encode(['errors' => $errors]);
    exit;
}

$referenceCode = generate_reference_code();
$pdo = zaiqa_db();
$persisted = false;

if ($pdo !== null) {
    try {
        $user = zaiqa_current_user();

        $seatingAreaId = null;
        $slug = trim((string)($body['seating_area_slug'] ?? ''));
        if ($slug !== '') {
            $stmt = $pdo->prepare('SELECT id FROM seating_areas WHERE slug = :slug');
            $stmt->execute(['slug' => $slug]);
            $seatingAreaId = $stmt->fetchColumn() ?: null;
        }

        $tableId = isset($body['table_id']) ? (int)$body['table_id'] : null;

        $insert = $pdo->prepare(
            'INSERT INTO reservations
                (user_id, reference_code, name, email, phone, reservation_date, reservation_time,
                 guests, seating_area_id, table_id, special_request, status)
             VALUES
                (:user_id, :ref, :name, :email, :phone, :date, :time,
                 :guests, :area_id, :table_id, :note, "pending")'
        );
        $insert->execute([
            'user_id' => $user['id'] ?? null,
            'ref' => $referenceCode,
            'name' => trim((string)$body['name']),
            'email' => trim((string)$body['email']),
            'phone' => trim((string)$body['phone']),
            'date' => $body['date'],
            'time' => $body['time'],
            'guests' => (int)$body['guests'],
            'area_id' => $seatingAreaId,
            'table_id' => $tableId,
            'note' => trim((string)($body['special_request'] ?? '')) ?: null,
        ]);

        if ($tableId) {
            $pdo->prepare('UPDATE tables SET status = "reserved" WHERE id = :id AND status = "available"')
                ->execute(['id' => $tableId]);
        }

        $persisted = true;
    } catch (PDOException $e) {
        error_log('[zaiqa] reservation insert failed: ' . $e->getMessage());
        $persisted = false;
    }
}

echo json_encode([
    'reference_code' => $referenceCode,
    'persisted' => $persisted,
    'name' => $body['name'],
    'date' => $body['date'],
    'time' => $body['time'],
    'guests' => (int)$body['guests'],
]);

function validate_reservation(array $body): array
{
    $errors = [];

    if (empty(trim((string)($body['name'] ?? '')))) {
        $errors['name'] = 'Name is required.';
    }
    if (empty($body['email']) || !filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'A valid email is required.';
    }
    if (empty(trim((string)($body['phone'] ?? '')))) {
        $errors['phone'] = 'Phone number is required.';
    }
    if (empty($body['date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$body['date'])) {
        $errors['date'] = 'A valid date is required.';
    } elseif (strtotime($body['date']) < strtotime(date('Y-m-d'))) {
        $errors['date'] = 'Date cannot be in the past.';
    }
    if (empty($body['time']) || !preg_match('/^\d{2}:\d{2}$/', (string)$body['time'])) {
        $errors['time'] = 'A valid time is required.';
    }
    $guests = (int)($body['guests'] ?? 0);
    if ($guests < 1 || $guests > 20) {
        $errors['guests'] = 'Guests must be between 1 and 20.';
    }

    return $errors;
}

function generate_reference_code(): string
{
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // no ambiguous O/0/I/1
    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return 'ZQ-' . $code;
}
