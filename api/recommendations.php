<?php
/**
 * GET /api/recommendations.php?mood=comfort
 *
 * Returns up to 4 dishes tagged with the requested mood.
 * Reads from MySQL (dishes.mood_tags) when available; falls back
 * to the seed set below only if the DB connection isn't
 * configured yet, so the frontend stays usable during early
 * development. Swap this file's fallback for a hard failure once
 * the database is live in production.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$allowedMoods = ['comfort', 'spicy', 'fancy', 'fresh', 'sweet', 'late_night'];
$mood = $_GET['mood'] ?? '';

if (!in_array($mood, $allowedMoods, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing mood parameter.']);
    exit;
}

$dishes = fetch_dishes_for_mood($mood);

echo json_encode(['mood' => $mood, 'dishes' => $dishes]);

/**
 * @return array<int, array{name:string, description:string, price:int, image:string}>
 */
function fetch_dishes_for_mood(string $mood): array
{
    $pdo = zaiqa_db();

    if ($pdo !== null) {
      try {
        $stmt = $pdo->prepare(
            'SELECT name, description, price, image_path AS image
             FROM dishes
             WHERE is_available = 1
               AND FIND_IN_SET(:mood, mood_tags)
             ORDER BY RAND()
             LIMIT 4'
        );
        $stmt->execute(['mood' => $mood]);
        $rows = $stmt->fetchAll();
        if ($rows) {
            return $rows;
        }
      } catch (PDOException $e) {
          error_log('[zaiqa] recommendations.php query failed (has database/zaiqa.sql been imported?): ' . $e->getMessage());
        }
    }

    return seed_dishes_for_mood($mood);
}

function seed_dishes_for_mood(string $mood): array
{
    $seed = [
        'comfort' => [
            ['name' => 'Chicken Karahi', 'description' => 'Slow-cooked in tomato and ginger, finished with green chili.', 'price' => 1450, 'image' => 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?auto=format&fit=crop&w=600&q=80'],
            ['name' => 'Creamy Alfredo Pasta', 'description' => 'Fettuccine in a slow-reduced garlic cream sauce.', 'price' => 1100, 'image' => 'https://images.unsplash.com/photo-1645112411341-6c4fd023714a?auto=format&fit=crop&w=600&q=80'],
            ['name' => 'Loaded Mac & Cheese', 'description' => 'Three-cheese blend, toasted breadcrumb crust.', 'price' => 950, 'image' => 'https://images.unsplash.com/photo-1543339308-43e59d6b73a6?auto=format&fit=crop&w=600&q=80'],
        ],
        'spicy' => [
            ['name' => 'Signature Fire Steak', 'description' => 'Charred sirloin, house chili oil, smoked paprika crust.', 'price' => 1950, 'image' => 'https://images.unsplash.com/photo-1600891964599-f61ba0e24092?auto=format&fit=crop&w=600&q=80'],
            ['name' => 'Peri Peri Wings', 'description' => 'Double-fried, tossed in a hand-blended peri peri glaze.', 'price' => 890, 'image' => 'https://images.unsplash.com/photo-1608039755401-742074f0548d?auto=format&fit=crop&w=600&q=80'],
            ['name' => 'Nihari Bowl', 'description' => 'Slow-braised beef shank, warming spice blend.', 'price' => 1350, 'image' => 'https://images.unsplash.com/photo-1631515243349-e0cb75fb8d3a?auto=format&fit=crop&w=600&q=80'],
        ],
        'fancy' => [
            ['name' => 'Truffle Risotto', 'description' => 'Arborio rice, wild mushroom, shaved black truffle.', 'price' => 2200, 'image' => 'https://images.unsplash.com/photo-1476124369491-e7addf5db371?auto=format&fit=crop&w=600&q=80'],
            ['name' => 'Pan-Seared Salmon', 'description' => 'Citrus beurre blanc, charred asparagus.', 'price' => 2450, 'image' => 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?auto=format&fit=crop&w=600&q=80'],
        ],
        'fresh' => [
            ['name' => 'Citrus Burrata Salad', 'description' => 'Heirloom tomato, basil oil, creamy burrata.', 'price' => 1200, 'image' => 'https://images.unsplash.com/photo-1505253716362-afaea1d3d1af?auto=format&fit=crop&w=600&q=80'],
            ['name' => 'Grilled Lemon Chicken', 'description' => 'Herb-marinated, char-grilled, mint yogurt.', 'price' => 1350, 'image' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=600&q=80'],
        ],
        'sweet' => [
            ['name' => 'Molten Lava Cake', 'description' => 'Dark chocolate centre, vanilla bean ice cream.', 'price' => 650, 'image' => 'https://images.unsplash.com/photo-1624353365286-3f8d62daad51?auto=format&fit=crop&w=600&q=80'],
            ['name' => 'Kunafa Cheesecake', 'description' => 'Crisp kunafa base, saffron syrup.', 'price' => 750, 'image' => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?auto=format&fit=crop&w=600&q=80'],
        ],
        'late_night' => [
            ['name' => 'Loaded Cheese Fries', 'description' => 'Beef strips, jalapeño, molten cheddar.', 'price' => 850, 'image' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=600&q=80'],
            ['name' => 'Zaiqa Smash Burger', 'description' => 'Double patty, caramelized onion, secret sauce.', 'price' => 1050, 'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=600&q=80'],
        ],
    ];

    return $seed[$mood] ?? [];
}
