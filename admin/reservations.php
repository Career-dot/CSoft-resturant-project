<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/admin-session.php';
zaiqa_require_admin();

$pdo = zaiqa_db();
$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    if (in_array($status, ['pending', 'confirmed', 'cancelled'], true)) {
        $pdo->prepare('UPDATE reservations SET status = :status WHERE id = :id')->execute(['status' => $status, 'id' => $id]);
        $flash = 'Reservation updated.';
    }
}

$reservations = [];
if ($pdo !== null) {
    $reservations = $pdo->query(
        "SELECT r.id, r.reference_code, r.name, r.phone, r.reservation_date, r.reservation_time,
                r.guests, r.status, t.table_number, sa.name AS seating_area
         FROM reservations r
         LEFT JOIN tables t ON t.id = r.table_id
         LEFT JOIN seating_areas sa ON sa.id = r.seating_area_id
         ORDER BY r.reservation_date DESC, r.reservation_time DESC LIMIT 100"
    )->fetchAll();
}

$pageTitle = 'Reservations | Admin';
$activeNav = 'reservations';
require_once __DIR__ . '/includes/admin-chrome-open.php';
?>
<h1>Reservations</h1>
<p class="admin-main__sub">Most recent 100 reservations.</p>

<?php if ($pdo === null): ?>
  <div class="admin-flash admin-flash--error">Database not connected — see README.md setup steps.</div>
<?php elseif ($flash): ?>
  <div class="admin-flash"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th>Reference</th><th>Name</th><th>Phone</th><th>Date</th><th>Time</th><th>Guests</th><th>Table</th><th>Status</th></tr></thead>
    <tbody>
      <?php foreach ($reservations as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['reference_code']) ?></td>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td><?= htmlspecialchars($r['phone']) ?></td>
        <td><?= htmlspecialchars($r['reservation_date']) ?></td>
        <td><?= htmlspecialchars($r['reservation_time']) ?></td>
        <td><?= htmlspecialchars((string)$r['guests']) ?></td>
        <td><?= htmlspecialchars(($r['table_number'] ?? '—') . ' · ' . ($r['seating_area'] ?? '')) ?></td>
        <td>
          <form method="post" style="display:flex;gap:0.4rem;align-items:center">
            <input type="hidden" name="id" value="<?= $r['id'] ?>">
            <select name="status" onchange="this.form.submit()">
              <option value="pending" <?= $r['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
              <option value="confirmed" <?= $r['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
              <option value="cancelled" <?= $r['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$reservations): ?><tr><td colspan="8">No reservations yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/admin-chrome-close.php'; ?>
