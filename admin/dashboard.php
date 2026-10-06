<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/admin-session.php';
zaiqa_require_admin();

$pdo = zaiqa_db();
$stats = ['today_orders' => 0, 'upcoming_reservations' => 0, 'total_customers' => 0, 'popular_dish' => '—'];

if ($pdo !== null) {
    $stats['today_orders'] = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(placed_at) = CURDATE()")->fetchColumn();
    $stats['upcoming_reservations'] = (int)$pdo->query("SELECT COUNT(*) FROM reservations WHERE reservation_date >= CURDATE() AND status != 'cancelled'")->fetchColumn();
    $stats['total_customers'] = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $popular = $pdo->query(
        "SELECT dish_name, SUM(quantity) AS qty FROM order_items GROUP BY dish_name ORDER BY qty DESC LIMIT 1"
    )->fetch();
    if ($popular) {
        $stats['popular_dish'] = $popular['dish_name'] . ' (' . $popular['qty'] . ' ordered)';
    }
}

$pageTitle = 'Dashboard | Admin';
$activeNav = 'dashboard';
require_once __DIR__ . '/includes/admin-chrome-open.php';
?>
<h1>Dashboard</h1>
<p class="admin-main__sub">A quick snapshot of what's happening right now.</p>

<?php if ($pdo === null): ?>
  <div class="admin-flash admin-flash--error">Database not connected — these numbers will stay at zero until MySQL is configured. See README.md.</div>
<?php endif; ?>

<div class="stat-grid">
  <div class="stat-card"><p class="stat-card__label">Today's Orders</p><p class="stat-card__value"><?= $stats['today_orders'] ?></p></div>
  <div class="stat-card"><p class="stat-card__label">Upcoming Reservations</p><p class="stat-card__value"><?= $stats['upcoming_reservations'] ?></p></div>
  <div class="stat-card"><p class="stat-card__label">Total Customers</p><p class="stat-card__value"><?= $stats['total_customers'] ?></p></div>
  <div class="stat-card"><p class="stat-card__label">Most Popular Dish</p><p class="stat-card__value" style="font-size:1.1rem"><?= htmlspecialchars($stats['popular_dish']) ?></p></div>
</div>

<p class="admin-main__sub">Use the sidebar to manage the menu, orders, reservations, customers, tables, and the secret menu.</p>

<?php require_once __DIR__ . '/includes/admin-chrome-close.php'; ?>
