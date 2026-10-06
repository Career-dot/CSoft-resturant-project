<?php
/**
 * GET  /api/favorites.php          -> list the logged-in user's favorite dishes
 * POST /api/favorites.php {dish_id} -> toggle that dish on/off, returns {favorited: bool}
 *
 * Requires login (401 if not). Favorites genuinely need MySQL —
 * there's no meaningful seed fallback for "this specific person's
 * saved dishes" — so this returns a clear error if the database
 * isn't connected.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

$user = zaiqa_current_user();
if ($user === null) {
    http_response_code(401);
    echo json_encode(['error' => 'Log in to save favorites.']);
    exit;
}

$pdo = zaiqa_db();
if ($pdo === null) {
    http_response_code(503);
    echo json_encode(['error' => 'Favorites need the database connected — see README.md setup steps.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare(
        'SELECT d.id, d.name, d.price, d.image_path AS image, c.name AS category
         FROM favorites f
         JOIN dishes d ON d.id = f.dish_id
         JOIN categories c ON c.id = d.category_id
         WHERE f.user_id = :user_id
         ORDER BY f.created_at DESC'
    );
    $stmt->execute(['user_id' => $user['id']]);
    echo json_encode(['favorites' => $stmt->fetchAll()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $dishId = (int)($body['dish_id'] ?? 0);

    if ($dishId <= 0) {
        http_response_code(422);
        echo json_encode(['error' => 'Missing dish_id.']);
        exit;
    }

    $check = $pdo->prepare('SELECT 1 FROM favorites WHERE user_id = :uid AND dish_id = :did');
    $check->execute(['uid' => $user['id'], 'did' => $dishId]);

    if ($check->fetch()) {
        $pdo->prepare('DELETE FROM favorites WHERE user_id = :uid AND dish_id = :did')
            ->execute(['uid' => $user['id'], 'did' => $dishId]);
        echo json_encode(['favorited' => false]);
    } else {
        $pdo->prepare('INSERT INTO favorites (user_id, dish_id) VALUES (:uid, :did)')
            ->execute(['uid' => $user['id'], 'did' => $dishId]);
        echo json_encode(['favorited' => true]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Use GET or POST.']);
