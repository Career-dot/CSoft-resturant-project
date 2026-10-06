<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/admin-session.php';
zaiqa_require_admin();

$pdo = zaiqa_db();
$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    if (in_array($status, ['available', 'reserved', 'unavailable'], true)) {
        $pdo->prepare('UPDATE tables SET status = :status WHERE id = :id')->execute(['status' => $status, 'id' => $id]);
        $flash = 'Table updated.';
    }
}

$tables = [];
if ($pdo !== null) {
    $tables = $pdo->query(
        'SELECT t.id, t.table_number, t.capacity, t.status, sa.name AS seating_area
         FROM tables t JOIN seating_areas sa ON sa.id = t.seating_area_id
         ORDER BY sa.id, t.table_number'
    )->fetchAll();
}

$pageTitle = 'Tables | Admin';
$activeNav = 'tables';
require_once __DIR__ . '/includes/admin-chrome-open.php';
?>
<h1>Tables</h1>
<p class="admin-main__sub">Manual status overrides for the reservation table map. Reservations also set this automatically when booked.</p>

<?php if ($pdo === null): ?>
  <div class="admin-flash admin-flash--error">Database not connected — see README.md setup steps.</div>
<?php elseif ($flash): ?>
  <div class="admin-flash"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th>Table</th><th>Seating Area</th><th>Capacity</th><th>Status</th></tr></thead>
    <tbody>
      <?php foreach ($tables as $t): ?>
      <tr>
        <td><?= htmlspecialchars($t['table_number']) ?></td>
        <td><?= htmlspecialchars($t['seating_area']) ?></td>
        <td><?= htmlspecialchars((string)$t['capacity']) ?></td>
        <td>
          <form method="post" style="display:flex;gap:0.4rem;align-items:center">
            <input type="hidden" name="id" value="<?= $t['id'] ?>">
            <select name="status" onchange="this.form.submit()">
              <option value="available" <?= $t['status'] === 'available' ? 'selected' : '' ?>>Available</option>
              <option value="reserved" <?= $t['status'] === 'reserved' ? 'selected' : '' ?>>Reserved</option>
              <option value="unavailable" <?= $t['status'] === 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
            </select>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$tables): ?><tr><td colspan="4">No tables found.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/admin-chrome-close.php'; ?>
