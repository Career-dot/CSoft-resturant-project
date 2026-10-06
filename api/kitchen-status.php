<?php
/**
 * GET /api/kitchen-status.php
 *
 * Returns a "what's happening in the kitchen right now" snapshot.
 * This is explicitly SIMULATED, not real telemetry — there's no
 * admin dashboard yet to write real numbers into the
 * `kitchen_status` table (database/step7_secret_kitchen.sql), so
 * generating a plausible-looking snapshot here is what the brief
 * calls for until that exists. Swap this for a real query against
 * `kitchen_status` once an admin panel can update it.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$activities = [
    ['label' => 'Grilling', 'emoji' => '🔥', 'range' => [1, 6]],
    ['label' => 'Cooking', 'emoji' => '🥘', 'range' => [2, 8]],
    ['label' => 'Baking', 'emoji' => '🍰', 'range' => [1, 4]],
    ['label' => 'Preparing Drinks', 'emoji' => '🥤', 'range' => [2, 7]],
];

$snapshot = array_map(function ($a) {
    return [
        'label' => $a['label'],
        'emoji' => $a['emoji'],
        'count' => random_int($a['range'][0], $a['range'][1]),
    ];
}, $activities);

echo json_encode(['activity' => $snapshot, 'simulated' => true, 'generated_at' => date('c')]);
