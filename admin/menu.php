<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/admin-session.php';
zaiqa_require_admin();

$pdo = zaiqa_db();
$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'toggle_available') {
        $pdo->prepare('UPDATE dishes SET is_available = NOT is_available WHERE id = :id')->execute(['id' => $id]);
        $flash = 'Availability updated.';
    } elseif ($action === 'toggle_secret') {
        $pdo->prepare('UPDATE dishes SET is_secret = NOT is_secret WHERE id = :id')->execute(['id' => $id]);
        $flash = 'Secret-menu status updated.';
    } elseif ($action === 'delete') {
        $pdo->prepare('DELETE FROM dishes WHERE id = :id')->execute(['id' => $id]);
        $flash = 'Dish deleted.';
    }
}

$dishes = [];
if ($pdo !== null) {
    $dishes = $pdo->query(
        'SELECT d.id, d.name, c.name AS category, d.price, d.is_available, d.is_secret, d.image_path
         FROM dishes d JOIN categories c ON c.id = d.category_id
         ORDER BY c.sort_order, d.name'
    )->fetchAll();
}

$pageTitle = 'Menu Management | Admin';
$activeNav = 'menu';
require_once __DIR__ . '/includes/admin-chrome-open.php';
?>
<div class="admin-toolbar">
  <div>
    <h1>Menu Management</h1>
    <p class="admin-main__sub">Add, edit, hide, or remove dishes.</p>
  </div>
  <a href="<?= BASE_URL ?>/admin/dish-form.php" class="admin-btn admin-btn--primary">+ Add Dish</a>
</div>

<?php if ($pdo === null): ?>
  <div class="admin-flash admin-flash--error">Database not connected — see README.md setup steps.</div>
<?php elseif ($flash): ?>
  <div class="admin-flash"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th></th><th>Name</th><th>Category</th><th>Price</th><th>Available</th><th>Secret</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($dishes as $d): ?>
      <tr>
        <td><img src="<?= htmlspecialchars($d['image_path']) ?>" alt="" loading="lazy"></td>
        <td><?= htmlspecialchars($d['name']) ?></td>
        <td><?= htmlspecialchars($d['category']) ?></td>
        <td>Rs. <?= htmlspecialchars((string)$d['price']) ?></td>
        <td>
          <form method="post" style="display:inline">
            <input type="hidden" name="action" value="toggle_available">
            <input type="hidden" name="id" value="<?= $d['id'] ?>">
            <button type="submit" class="badge badge--<?= $d['is_available'] ? 'available' : 'unavailable' ?>" style="border:none;cursor:pointer">
              <?= $d['is_available'] ? 'Available' : 'Hidden' ?>
            </button>
          </form>
        </td>
        <td>
          <form method="post" style="display:inline">
            <input type="hidden" name="action" value="toggle_secret">
            <input type="hidden" name="id" value="<?= $d['id'] ?>">
            <button type="submit" class="badge badge--<?= $d['is_secret'] ? 'reserved' : 'available' ?>" style="border:none;cursor:pointer">
              <?= $d['is_secret'] ? 'Secret' : 'Public' ?>
            </button>
          </form>
        </td>
        <td class="admin-actions">
          <a href="<?= BASE_URL ?>/admin/dish-form.php?id=<?= $d['id'] ?>" class="admin-btn">Edit</a>
          <form method="post" onsubmit="return confirm('Delete this dish? This can\'t be undone.');" style="display:inline">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $d['id'] ?>">
            <button type="submit" class="admin-btn admin-btn--danger">Delete</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$dishes): ?><tr><td colspan="7">No dishes found.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/admin-chrome-close.php'; ?>
