<?php
/**
 * GET /api/secret-menu.php
 *
 * Returns the currently secret (is_secret = 1) dishes — "The
 * Hidden Table". Not access-controlled server-side; the
 * "secrecy" here is a discovery mechanic (the logo unlock in
 * assets/js/main.js), not a security boundary — matching the
 * brief's "elegant but not literally locked-down" Easter egg.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/seed_dishes.php';

echo json_encode(['dishes' => fetch_secret_dishes()]);

function fetch_secret_dishes(): array
{
    $pdo = zaiqa_db();

    if ($pdo !== null) {
        try {
            $stmt = $pdo->query(
                'SELECT d.id, c.name AS category, d.name, d.description, d.price, d.image_path AS image, d.spice_level
                 FROM dishes d JOIN categories c ON c.id = d.category_id
                 WHERE d.is_secret = 1 AND d.is_available = 1'
            );
            $rows = $stmt->fetchAll();
            if ($rows) {
                return $rows;
            }
        } catch (PDOException $e) {
            error_log('[zaiqa] secret-menu.php query failed (has database/step7_secret_kitchen.sql been imported?): ' . $e->getMessage());
        }
    }

    $secret = array_values(array_filter(zaiqa_seed_dishes(), fn($d) => !empty($d['is_secret'])));
    return array_map(fn($d) => [
        'id' => $d['id'], 'category' => $d['category'], 'name' => $d['name'],
        'description' => $d['description'], 'price' => $d['price'],
        'image' => $d['image'], 'spice_level' => $d['spice_level'],
    ], $secret);
}
