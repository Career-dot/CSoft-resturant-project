<?php
/**
 * POST /api/order.php
 * Body: { name, email, phone, notes, items: [{ dish_id, quantity, customization }] }
 *
 * Prices are NOT trusted from the client — each line is
 * recomputed here from the dish's base price (DB or seed) plus
 * any selected customization option deltas
 * (config/customization_options.php), so a tampered client-side
 * total can't be submitted. Generates a reference code and
 * persists to `orders`/`order_items`; falls back to a
 * non-persisted confirmation if MySQL isn't connected yet, same
 * pattern as api/reserve.php. When the customer is logged in,
 * each ordered dish is also stamped into their Food Passport
 * (see database/step6_auth.sql, api/food-passport.php).
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/seed_dishes.php';
require_once __DIR__ . '/../config/customization_options.php';
require_once __DIR__ . '/../config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Use POST.']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true) ?? [];
$items = is_array($body['items'] ?? null) ? $body['items'] : [];
$name = trim((string)($body['name'] ?? ''));

if ($name === '') {
    http_response_code(422);
    echo json_encode(['errors' => ['name' => 'Name is required.']]);
    exit;
}
if (empty($items)) {
    http_response_code(422);
    echo json_encode(['errors' => ['items' => 'Your order is empty.']]);
    exit;
}

$priced = [];
$subtotal = 0;

foreach ($items as $item) {
    $dishId = (int)($item['dish_id'] ?? 0);
    $quantity = max(1, (int)($item['quantity'] ?? 1));
    $customization = is_array($item['customization'] ?? null) ? $item['customization'] : null;

    $dish = zaiqa_find_dish_by_id($dishId);
    if ($dish === null) {
        continue; // skip unknown/removed dishes rather than fail the whole order
    }

    $unitPrice = $dish['price'] + zaiqa_customization_delta($dishId, $customization);
    $lineTotal = $unitPrice * $quantity;
    $subtotal += $lineTotal;

    $priced[] = [
        'dish_id' => $dishId,
        'name' => $dish['name'],
        'quantity' => $quantity,
        'unit_price' => $unitPrice,
        'customization' => $customization,
        'line_total' => $lineTotal,
    ];
}

if (empty($priced)) {
    http_response_code(422);
    echo json_encode(['errors' => ['items' => 'None of the submitted items could be found.']]);
    exit;
}

$total = $subtotal; // no delivery/service fee modeled yet
$referenceCode = zaiqa_generate_order_reference();
$pdo = zaiqa_db();
$persisted = false;
$user = zaiqa_current_user(); // null for guest checkout — orders.user_id stays NULL

if ($pdo !== null) {
    try {
        $pdo->beginTransaction();

        $insertOrder = $pdo->prepare(
            'INSERT INTO orders (user_id, reference_code, customer_name, customer_email, customer_phone, notes, subtotal, total, status)
             VALUES (:user_id, :ref, :name, :email, :phone, :notes, :subtotal, :total, "received")'
        );
        $insertOrder->execute([
            'user_id' => $user['id'] ?? null,
            'ref' => $referenceCode,
            'name' => $name,
            'email' => trim((string)($body['email'] ?? '')) ?: null,
            'phone' => trim((string)($body['phone'] ?? '')) ?: null,
            'notes' => trim((string)($body['notes'] ?? '')) ?: null,
            'subtotal' => $subtotal,
            'total' => $total,
        ]);
        $orderId = (int)$pdo->lastInsertId();

        $insertItem = $pdo->prepare(
            'INSERT INTO order_items (order_id, dish_id, dish_name, quantity, unit_price, customization_json, line_total)
             VALUES (:order_id, :dish_id, :dish_name, :quantity, :unit_price, :custom, :line_total)'
        );
        foreach ($priced as $line) {
            $insertItem->execute([
                'order_id' => $orderId,
                'dish_id' => $line['dish_id'],
                'dish_name' => $line['name'],
                'quantity' => $line['quantity'],
                'unit_price' => $line['unit_price'],
                'custom' => $line['customization'] ? json_encode($line['customization']) : null,
                'line_total' => $line['line_total'],
            ]);
        }

        // Stamp each ordered dish into the logged-in user's Food
        // Passport (first time only — PRIMARY KEY(user_id, dish_id)
        // makes this a natural de-dupe via INSERT IGNORE).
        if ($user !== null) {
            $stampPassport = $pdo->prepare('INSERT IGNORE INTO food_passport (user_id, dish_id) VALUES (:uid, :did)');
            $stampedDishIds = [];
            foreach ($priced as $line) {
                if (!in_array($line['dish_id'], $stampedDishIds, true)) {
                    $stampPassport->execute(['uid' => $user['id'], 'did' => $line['dish_id']]);
                    $stampedDishIds[] = $line['dish_id'];
                }
            }
        }

        $pdo->commit();
        $persisted = true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log('[zaiqa] order insert failed: ' . $e->getMessage());
        $persisted = false;
    }
}

echo json_encode([
    'reference_code' => $referenceCode,
    'persisted' => $persisted,
    'items' => $priced,
    'subtotal' => $subtotal,
    'total' => $total,
]);

/** Look up a dish's current name + price from DB or seed data. */
function zaiqa_find_dish_by_id(int $id): ?array
{
    $pdo = zaiqa_db();

    if ($pdo !== null) {
        $stmt = $pdo->prepare('SELECT id, name, price FROM dishes WHERE id = :id AND is_available = 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        if ($row) {
            return $row;
        }
    }

    foreach (zaiqa_seed_dishes() as $d) {
        if ($d['id'] === $id) {
            return ['id' => $d['id'], 'name' => $d['name'], 'price' => $d['price']];
        }
    }
    return null;
}

/** Recompute a customization price delta server-side against the trusted option list. */
function zaiqa_customization_delta(int $dishId, ?array $customization): int
{
    if (!$customization) {
        return 0;
    }
    $allGroups = zaiqa_customization_options();
    $groups = $allGroups[$dishId]['groups'] ?? [];
    if (!$groups) {
        return 0;
    }

    $delta = 0;
    foreach ($groups as $group) {
        $selected = $customization[$group['id']] ?? null;
        if ($selected === null) {
            continue;
        }
        $selectedIds = is_array($selected) ? $selected : [$selected];
        foreach ($group['options'] as $option) {
            if (in_array($option['id'], $selectedIds, true)) {
                $delta += (int)$option['price_delta'];
            }
        }
    }
    return $delta;
}

function zaiqa_generate_order_reference(): string
{
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return 'ZO-' . $code;
}
