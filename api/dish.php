<?php
/**
 * GET /api/dish.php?id=1
 *
 * Returns full detail for one dish — ingredients, flavor profile,
 * the 5-stage flavor journey, and the recommended pairing — for
 * the "Taste Before You Order" preview modal. Also attaches
 * `customization` (build-your-own groups) from
 * config/customization_options.php when the dish has any, same
 * PHP-config-as-source-of-truth pattern as the taste quiz.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/seed_dishes.php';
require_once __DIR__ . '/../config/customization_options.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid dish id.']);
    exit;
}

$dish = fetch_dish($id);

if ($dish === null) {
    http_response_code(404);
    echo json_encode(['error' => 'Dish not found.']);
    exit;
}

$customization = zaiqa_customization_options();
$dish['customization'] = $customization[$id] ?? null;

echo json_encode($dish);

function fetch_dish(int $id): ?array
{
    $pdo = zaiqa_db();

    if ($pdo !== null) {
        try {
            $stmt = $pdo->prepare(
                'SELECT d.id, c.name AS category, d.name, d.name_urdu, d.description,
                        d.price, d.prep_time_min, d.spice_level, d.image_path AS image,
                        d.mood_tags, d.dietary_tags, d.pairing_note AS pairing,
                        f.spice AS f_spice, f.saltiness, f.tanginess, f.richness,
                        f.journey_stage_1, f.journey_stage_2, f.journey_stage_3,
                        f.journey_stage_4, f.journey_stage_5
                 FROM dishes d
                 JOIN categories c ON c.id = d.category_id
                 LEFT JOIN flavor_profiles f ON f.dish_id = d.id
                 WHERE d.id = :id AND d.is_available = 1'
            );
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch();

            if ($row) {
                $ingStmt = $pdo->prepare(
                    'SELECT i.name FROM ingredients i
                     JOIN dish_ingredients di ON di.ingredient_id = i.id
                     WHERE di.dish_id = :id'
                );
                $ingStmt->execute(['id' => $id]);
                $ingredients = array_column($ingStmt->fetchAll(), 'name');

                return [
                    'id' => $row['id'], 'category' => $row['category'],
                    'name' => $row['name'], 'name_urdu' => $row['name_urdu'],
                    'description' => $row['description'], 'price' => $row['price'],
                    'prep_time_min' => $row['prep_time_min'], 'spice_level' => $row['spice_level'],
                    'image' => $row['image'],
                    'mood_tags' => array_filter(explode(',', (string)$row['mood_tags'])),
                    'dietary_tags' => array_filter(explode(',', (string)$row['dietary_tags'])),
                    'pairing' => $row['pairing'],
                    'ingredients' => $ingredients,
                    'flavor' => [
                        'spice' => (int)$row['f_spice'], 'saltiness' => (int)$row['saltiness'],
                        'tanginess' => (int)$row['tanginess'], 'richness' => (int)$row['richness'],
                    ],
                    'journey' => array_filter([
                        $row['journey_stage_1'], $row['journey_stage_2'], $row['journey_stage_3'],
                        $row['journey_stage_4'], $row['journey_stage_5'],
                    ]),
                ];
            }
        } catch (PDOException $e) {
            // Most likely: flavor_profiles/ingredients/dish_ingredients
            // don't exist yet (database/step2_menu.sql not imported).
            error_log('[zaiqa] dish.php query failed (has database/step2_menu.sql been imported?): ' . $e->getMessage());
        }
    }

    foreach (zaiqa_seed_dishes() as $d) {
        if ($d['id'] === $id) {
            return $d;
        }
    }
    return null;
}
