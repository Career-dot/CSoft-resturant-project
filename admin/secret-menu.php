<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/admin-session.php';
zaiqa_require_admin();

$pdo = zaiqa_db();
$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'remove_from_secret') {
        $pdo->prepare('UPDATE dishes SET is_secret = 0 WHERE id = :id')->execute(['id' => $id]);
        $flash = 'Moved back to the regular menu.';
    } elseif ($action === 'delete') {
        $pdo->prepare('DELETE FROM dishes WHERE id = :id')->execute(['id' => $id]);
        $flash = 'Dish deleted.';
    }
}

$secretDishes = [];
if ($pdo !== null) {
    $secretDishes = $pdo->query(
        "SELECT d.id, d.name, c.name AS category, d.price, d.is_available, d.image_path
         FROM dishes d JOIN categories c ON c.id = d.category_id
         WHERE d.is_secret = 1 ORDER BY d.name"
    )->fetchAll();
}

$pageTitle = 'Secret Menu | Admin';
$activeNav = 'secret-menu';
require_once __DIR__ . '/includes/admin-chrome-open.php';
?>
<div class="admin-toolbar">
  <div>
    <h1>Secret Menu</h1>
    <p class="admin-main__sub">"The Hidden Table" — dishes flagged secret. Add a new one from Menu Management (check "Secret menu only"), or flip an existing dish's flag there too.</p>
  </div>
  <a href="<?= BASE_URL ?>/admin/dish-form.php" class="admin-btn admin-btn--primary">+ Add Secret Dish</a>
</div>

<?php if ($pdo === null): ?>
  <div class="admin-flash admin-flash--error">Database not connected — see README.md setup steps.</div>
<?php elseif ($flash): ?>
  <div class="admin-flash"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th></th><th>Name</th><th>Category</th><th>Price</th><th>Available</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($secretDishes as $d): ?>
      <tr>
        <td><img src="<?= htmlspecialchars($d['image_path']) ?>" alt="" loading="lazy"></td>
        <td><?= htmlspecialchars($d['name']) ?></td>
        <td><?= htmlspecialchars($d['category']) ?></td>
        <td>Rs. <?= htmlspecialchars((string)$d['price']) ?></td>
        <td><span class="badge badge--<?= $d['is_available'] ? 'available' : 'unavailable' ?>"><?= $d['is_available'] ? 'Available' : 'Hidden' ?></span></td>
        <td class="admin-actions">
          <a href="<?= BASE_URL ?>/admin/dish-form.php?id=<?= $d['id'] ?>" class="admin-btn">Edit</a>
          <form method="post" style="display:inline">
            <input type="hidden" name="action" value="remove_from_secret">
            <input type="hidden" name="id" value="<?= $d['id'] ?>">
            <button type="submit" class="admin-btn">Move to Regular Menu</button>
          </form>
          <form method="post" onsubmit="return confirm('Delete this dish? This can\'t be undone.');" style="display:inline">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $d['id'] ?>">
            <button type="submit" class="admin-btn admin-btn--danger">Delete</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$secretDishes): ?><tr><td colspan="6">No secret dishes right now.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/includes/admin-chrome-close.php'; ?>
