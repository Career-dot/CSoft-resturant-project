<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/admin-session.php';
zaiqa_require_admin();

$pdo = zaiqa_db();
$flash = '';
$statuses = ['received', 'preparing', 'cooking', 'ready', 'served', 'cancelled'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    if (in_array($status, $statuses, true)) {
        $pdo->prepare('UPDATE orders SET status = :status WHERE id = :id')->execute(['status' => $status, 'id' => $id]);
        $flash = 'Order status updated.';
    }
}

$orders = [];
if ($pdo !== null) {
    $orders = $pdo->query(
        'SELECT id, reference_code, customer_name, customer_phone, total, status, placed_at
         FROM orders ORDER BY placed_at DESC LIMIT 100'
    )->fetchAll();
}

$pageTitle = 'Orders | Admin';
$activeNav = 'orders';
require_once __DIR__ . '/includes/admin-chrome-open.php';
?>
<h1>Orders</h1>
<p class="admin-main__sub">Most recent 100 orders. Updating status here is what powers the customer-facing tracking page.</p>

<?php if ($pdo === null): ?>
  <div class="admin-flash admin-flash--error">Database not connected — see README.md setup steps.</div>
<?php elseif ($flash): ?>
  <div class="admin-flash"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th>Reference</th><th>Customer</th><th>Phone</th><th>Total</th><th>Placed</th><th>Status</th></tr></thead>
    <tbody>
      <?php foreach ($orders as $o): ?>
      <tr>
        <td><?= htmlspecialchars($o['reference_code']) ?></td>
        <td><?= htmlspecialchars($o['customer_name']) ?></td>
        <td><?= htmlspecialchars($o['customer_phone'] ?? '—') ?></td>
        <td>Rs. <?= htmlspecialchars((string)$o['total']) ?></td>
        <td><?= htmlspecialchars($o['placed_at']) ?></td>
        <td>
          <form method="post" style="display:flex;gap:0.4rem;align-items:center">
            <input type="hidden" name="id" value="<?= $o['id'] ?>">
            <select name="status" onchange="this.form.submit()">
              <?php foreach ($statuses as $s): ?>
                <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
              <?php endforeach; ?>
            </select>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$orders): ?><tr><td colspan="6">No orders yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/admin-chrome-close.php'; ?>
