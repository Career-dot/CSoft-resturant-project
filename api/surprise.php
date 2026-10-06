<?php
/**
 * GET /api/surprise.php[?mood=spicy]
 *
 * Returns one randomly selected available dish — the "🎲 Surprise
 * Me" feature. Optionally weighted toward a mood if provided
 * (falls back to any dish if nothing matches that mood).
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/seed_dishes.php';

$mood = trim((string)($_GET['mood'] ?? ''));

$dish = pick_surprise_dish($mood);

if ($dish === null) {
    http_response_code(404);
    echo json_encode(['error' => 'No dishes available right now.']);
    exit;
}

echo json_encode($dish);

function pick_surprise_dish(string $mood): ?array
{
    $pdo = zaiqa_db();

    if ($pdo !== null) {
        try {
            $sql = 'SELECT d.id, c.name AS category, d.name, d.description, d.price, d.image_path AS image, d.spice_level
                    FROM dishes d JOIN categories c ON c.id = d.category_id
                    WHERE d.is_available = 1 AND d.is_secret = 0';
            $params = [];
            if ($mood !== '') {
                $sql .= ' AND FIND_IN_SET(:mood, d.mood_tags)';
                $params['mood'] = $mood;
            }
            $sql .= ' ORDER BY RAND() LIMIT 1';

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch();
            if ($row) {
                return $row;
            }
            // No match for that mood — fall through to any random dish.
            $stmt = $pdo->query('SELECT d.id, c.name AS category, d.name, d.description, d.price, d.image_path AS image, d.spice_level
                                  FROM dishes d JOIN categories c ON c.id = d.category_id
                                  WHERE d.is_available = 1 AND d.is_secret = 0 ORDER BY RAND() LIMIT 1');
            $row = $stmt->fetch();
            if ($row) {
                return $row;
            }
        } catch (PDOException $e) {
            error_log('[zaiqa] surprise.php query failed (has database/zaiqa.sql been imported?): ' . $e->getMessage());
        }
    }

    $dishes = zaiqa_seed_dishes();
    $dishes = array_values(array_filter($dishes, fn($d) => empty($d['is_secret'])));
    if ($mood !== '') {
        $matching = array_values(array_filter($dishes, fn($d) => in_array($mood, $d['mood_tags'] ?? [], true)));
        if ($matching) {
            $dishes = $matching;
        }
    }
    if (!$dishes) {
        return null;
    }
    $pick = $dishes[array_rand($dishes)];
    return [
        'id' => $pick['id'], 'category' => $pick['category'], 'name' => $pick['name'],
        'description' => $pick['description'], 'price' => $pick['price'],
        'image' => $pick['image'], 'spice_level' => $pick['spice_level'],
    ];
}
