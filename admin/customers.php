<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/admin-session.php';
zaiqa_require_admin();

$pdo = zaiqa_db();
$customers = [];
if ($pdo !== null) {
    $customers = $pdo->query(
        'SELECT u.id, u.name, u.email, u.phone, u.created_at, tp.personality_name
         FROM users u
         LEFT JOIN taste_profiles tp ON tp.user_id = u.id
         ORDER BY u.created_at DESC LIMIT 200'
    )->fetchAll();
}

$pageTitle = 'Customers | Admin';
$activeNav = 'customers';
require_once __DIR__ . '/includes/admin-chrome-open.php';
?>
<h1>Customers</h1>
<p class="admin-main__sub">Read-only — <?= count($customers) ?> registered customer<?= count($customers) === 1 ? '' : 's' ?>.</p>

<?php if ($pdo === null): ?>
  <div class="admin-flash admin-flash--error">Database not connected — see README.md setup steps.</div>
<?php endif; ?>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Taste Personality</th><th>Joined</th></tr></thead>
    <tbody>
      <?php foreach ($customers as $c): ?>
      <tr>
        <td><?= htmlspecialchars($c['name']) ?></td>
        <td><?= htmlspecialchars($c['email']) ?></td>
        <td><?= htmlspecialchars($c['phone'] ?? '—') ?></td>
        <td><?= htmlspecialchars($c['personality_name'] ?? '—') ?></td>
        <td><?= htmlspecialchars($c['created_at']) ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$customers): ?><tr><td colspan="5">No customers yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/admin-chrome-close.php'; ?>
