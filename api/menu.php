<?php
/**
 * GET /api/menu.php?search=&category=&spice_max=&mood=
 *
 * Returns the filtered dish list for the menu grid. Reads from
 * MySQL when available; falls back to config/seed_dishes.php
 * during development (see config/database.php for the pattern).
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/seed_dishes.php';

$search    = trim((string)($_GET['search'] ?? ''));
$category  = trim((string)($_GET['category'] ?? ''));
$spiceMax  = isset($_GET['spice_max']) ? (int)$_GET['spice_max'] : null;

$dishes = fetch_menu($search, $category, $spiceMax);

echo json_encode(['dishes' => $dishes]);

function fetch_menu(string $search, string $category, ?int $spiceMax): array
{
    $pdo = zaiqa_db();

    if ($pdo !== null) {
        try {
            $sql = 'SELECT d.id, c.name AS category, c.slug AS category_slug,
                           d.name, d.name_urdu, d.description, d.price,
                           d.prep_time_min, d.spice_level, d.image_path AS image,
                           d.mood_tags, d.dietary_tags
                    FROM dishes d
                    JOIN categories c ON c.id = d.category_id
                    WHERE d.is_available = 1 AND d.is_secret = 0';
            $params = [];

            if ($search !== '') {
                $sql .= ' AND (d.name LIKE :search OR d.description LIKE :search2
                           OR EXISTS (SELECT 1 FROM dish_ingredients di
                                      JOIN ingredients i ON i.id = di.ingredient_id
                                      WHERE di.dish_id = d.id AND i.name LIKE :search3))';
                $params['search']  = "%$search%";
                $params['search2'] = "%$search%";
                $params['search3'] = "%$search%";
            }
            if ($category !== '' && $category !== 'all') {
                $sql .= ' AND c.slug = :category';
                $params['category'] = $category;
            }
            if ($spiceMax !== null) {
                $sql .= ' AND d.spice_level <= :spice_max';
                $params['spice_max'] = $spiceMax;
            }
            $sql .= ' ORDER BY c.sort_order, d.name';

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll();
            if ($rows) {
                foreach ($rows as &$row) {
                    $row['mood_tags']    = array_filter(explode(',', (string)$row['mood_tags']));
                    $row['dietary_tags'] = array_filter(explode(',', (string)$row['dietary_tags']));
                }
                return $rows;
            }
        } catch (PDOException $e) {
            // DB connects, but dishes/categories (or the search's
            // dish_ingredients join) aren't there yet — fall back
            // to seed data instead of a hard 500.
            error_log('[zaiqa] menu.php query failed (has database/zaiqa.sql been imported?): ' . $e->getMessage());
        }
    }

    return filter_seed_dishes(zaiqa_seed_dishes(), $search, $category, $spiceMax);
}

function filter_seed_dishes(array $dishes, string $search, string $category, ?int $spiceMax): array
{
    return array_values(array_filter($dishes, function ($d) use ($search, $category, $spiceMax) {
        if (!empty($d['is_secret'])) {
            return false; // secret dishes never appear in the regular menu — see api/secret-menu.php
        }
        if ($search !== '') {
            $haystack = strtolower($d['name'] . ' ' . $d['description'] . ' ' . implode(' ', $d['ingredients']));
            if (!str_contains($haystack, strtolower($search))) {
                return false;
            }
        }
        if ($category !== '' && $category !== 'all' && $d['category_slug'] !== $category) {
            return false;
        }
        if ($spiceMax !== null && $d['spice_level'] > $spiceMax) {
            return false;
        }
        return true;
    }));
}
