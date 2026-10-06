<?php
/**
 * GET /api/food-passport.php
 *
 * Returns which dishes the logged-in user has tried (stamped
 * automatically the first time a dish appears in one of their
 * placed orders — see api/order.php), how many are still
 * unexplored, and a badge tier computed from the count.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

$user = zaiqa_current_user();
if ($user === null) {
    http_response_code(401);
    echo json_encode(['error' => 'Log in to see your Food Passport.']);
    exit;
}

$pdo = zaiqa_db();
if ($pdo === null) {
    http_response_code(503);
    echo json_encode(['error' => 'Food Passport needs the database connected — see README.md setup steps.']);
    exit;
}

try {
    $triedStmt = $pdo->prepare(
        'SELECT d.id, d.name, d.image_path AS image, c.name AS category, fp.tried_at
         FROM food_passport fp
         JOIN dishes d ON d.id = fp.dish_id
         JOIN categories c ON c.id = d.category_id
         WHERE fp.user_id = :uid
         ORDER BY fp.tried_at DESC'
    );
    $triedStmt->execute(['uid' => $user['id']]);
    $tried = $triedStmt->fetchAll();

    $totalStmt = $pdo->query('SELECT COUNT(*) FROM dishes WHERE is_available = 1 AND is_secret = 0');
    $total = (int)$totalStmt->fetchColumn();
} catch (PDOException $e) {
    error_log('[zaiqa] food-passport.php query failed (has database/step6_auth.sql been imported?): ' . $e->getMessage());
    http_response_code(503);
    echo json_encode(['error' => 'Food Passport isn’t set up yet — see README.md setup steps (database/step6_auth.sql).', 'debug' => $e->getMessage()]);
    exit;
}

echo json_encode([
    'tried' => $tried,
    'tried_count' => count($tried),
    'total_dishes' => $total,
    'badge' => compute_badge(count($tried)),
]);

function compute_badge(int $count): array
{
    if ($count >= 10) {
        return ['name' => 'Zaiqa Insider', 'description' => '10+ dishes explored — you basically live here.'];
    }
    if ($count >= 5) {
        return ['name' => 'Regular', 'description' => '5+ dishes explored.'];
    }
    if ($count >= 1) {
        return ['name' => 'Explorer', 'description' => 'Your journey has begun.'];
    }
    return ['name' => 'Newcomer', 'description' => 'Order something to start your passport.'];
}
