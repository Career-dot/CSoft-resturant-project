<?php
/**
 * GET /api/order-status.php?ref=ZO-XXXXXX
 *
 * Returns the order's current status for the tracking stepper.
 * Orders need the real database (no meaningful seed fallback for
 * "look up this specific order").
 *
 * Simulation note: `orders.status` defaults to "received" and
 * there's no admin dashboard yet to advance it, so — same spirit
 * as api/kitchen-status.php — if the stored status is still at
 * its default we SIMULATE progress from elapsed time since
 * placed_at, purely so the tracker feels alive during a demo. The
 * moment something explicitly sets a different status (a future
 * admin panel), that stored value wins outright and the
 * simulation stops. This is flagged in the response as
 * `simulated: true/false` so the frontend can be honest about it.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$ref = trim((string)($_GET['ref'] ?? ''));
if ($ref === '') {
    http_response_code(422);
    echo json_encode(['error' => 'Missing order reference.']);
    exit;
}

$pdo = zaiqa_db();
if ($pdo === null) {
    http_response_code(503);
    echo json_encode(['error' => 'Order tracking needs the database connected — see README.md setup steps.']);
    exit;
}

$stmt = $pdo->prepare('SELECT reference_code, customer_name, status, total, placed_at FROM orders WHERE reference_code = :ref');
$stmt->execute(['ref' => $ref]);
$order = $stmt->fetch();

if (!$order) {
    http_response_code(404);
    echo json_encode(['error' => 'No order found for that reference code.']);
    exit;
}

$stages = ['received', 'preparing', 'cooking', 'ready', 'served'];
$simulated = false;
$status = $order['status'];

if ($status === 'received') {
    $elapsedMinutes = (strtotime('now') - strtotime($order['placed_at'])) / 60;
    $simulated = true;
    if ($elapsedMinutes >= 9) {
        $status = 'served';
    } elseif ($elapsedMinutes >= 6) {
        $status = 'ready';
    } elseif ($elapsedMinutes >= 3) {
        $status = 'cooking';
    } elseif ($elapsedMinutes >= 1) {
        $status = 'preparing';
    } else {
        $status = 'received';
    }
}

echo json_encode([
    'reference_code' => $order['reference_code'],
    'name' => $order['customer_name'],
    'total' => (int)$order['total'],
    'status' => $status,
    'stage_index' => array_search($status, $stages, true),
    'stages' => $stages,
    'simulated' => $simulated,
]);
