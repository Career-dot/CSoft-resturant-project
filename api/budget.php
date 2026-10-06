<?php
/**
 * GET /api/budget.php?budget=1500
 *
 * "Build My Meal" — assembles the best-fitting combo (main + side
 * + drink + dessert, dropping categories if the budget is tight)
 * from currently available dishes. Simple greedy strategy: for
 * each main course, try pairing it with the cheapest side/drink/
 * dessert and keep the combination that uses the most of the
 * budget without going over. Small dish counts make this fast
 * enough without needing a fancier optimizer.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/seed_dishes.php';

$budget = isset($_GET['budget']) ? (int)$_GET['budget'] : 0;

if ($budget < 200) {
    http_response_code(422);
    echo json_encode(['error' => 'Budget must be at least Rs. 200.']);
    exit;
}

$pools = load_dish_pools();
$result = build_meal($budget, $pools);

echo json_encode($result);

/**
 * Groups available dishes into main / side / drink / dessert pools.
 */
function load_dish_pools(): array
{
    $pdo = zaiqa_db();
    $dishes = [];

    if ($pdo !== null) {
        try {
            $stmt = $pdo->query(
                'SELECT d.id, c.slug AS category_slug, d.name, d.price, d.image_path AS image
                 FROM dishes d JOIN categories c ON c.id = d.category_id
                 WHERE d.is_available = 1 AND d.is_secret = 0'
            );
            $dishes = $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('[zaiqa] budget.php query failed (has database/zaiqa.sql been imported?): ' . $e->getMessage());
        }
    }

    if (!$dishes) {
        $dishes = array_values(array_map(fn($d) => [
            'id' => $d['id'], 'category_slug' => $d['category_slug'],
            'name' => $d['name'], 'price' => $d['price'], 'image' => $d['image'],
        ], array_filter(zaiqa_seed_dishes(), fn($d) => empty($d['is_secret']))));
    }

    $mainSlugs = ['main-course', 'burgers', 'pasta', 'chef-specials'];
    $pools = ['main' => [], 'side' => [], 'drink' => [], 'dessert' => []];

    foreach ($dishes as $d) {
        if (in_array($d['category_slug'], $mainSlugs, true)) {
            $pools['main'][] = $d;
        } elseif ($d['category_slug'] === 'starters') {
            $pools['side'][] = $d;
        } elseif ($d['category_slug'] === 'drinks') {
            $pools['drink'][] = $d;
        } elseif ($d['category_slug'] === 'desserts') {
            $pools['dessert'][] = $d;
        }
    }

    foreach ($pools as &$pool) {
        usort($pool, fn($a, $b) => $a['price'] <=> $b['price']);
    }
    return $pools;
}

function build_meal(int $budget, array $pools): array
{
    if (empty($pools['main'])) {
        return ['items' => [], 'total' => 0, 'budget' => $budget, 'complete' => false];
    }

    $best = null;

    // Try mains from most to least expensive so we favor using more of the budget.
    $mainsDesc = $pools['main'];
    usort($mainsDesc, fn($a, $b) => $b['price'] <=> $a['price']);

    foreach ($mainsDesc as $main) {
        // Try full combo (main + side + drink + dessert), then drop
        // dessert, then side, then drink — keeping whichever fits
        // and spends the most without exceeding budget.
        $attempts = [
            ['side', 'drink', 'dessert'],
            ['side', 'drink'],
            ['drink'],
            [],
        ];

        foreach ($attempts as $extras) {
            $combo = [$main];
            $total = $main['price'];
            $ok = true;

            foreach ($extras as $slot) {
                $cheapest = $pools[$slot][0] ?? null;
                if ($cheapest === null) {
                    continue;
                }
                if ($total + $cheapest['price'] > $budget) {
                    $ok = false;
                    break;
                }
                $combo[] = $cheapest;
                $total += $cheapest['price'];
            }

            if ($ok && $total <= $budget) {
                if ($best === null || $total > $best['total']) {
                    $best = ['items' => $combo, 'total' => $total];
                }
                break; // best (fullest) combo found for this main; move to next main
            }
        }
    }

    if ($best === null) {
        // Even the cheapest single main doesn't fit.
        return ['items' => [], 'total' => 0, 'budget' => $budget, 'complete' => false];
    }

    return [
        'items' => $best['items'],
        'total' => $best['total'],
        'budget' => $budget,
        'remaining' => $budget - $best['total'],
        'complete' => count($best['items']) >= 3,
    ];
}
