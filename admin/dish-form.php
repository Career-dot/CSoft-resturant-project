<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/admin-session.php';
zaiqa_require_admin();

$pdo = zaiqa_db();
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
$isEdit = $id > 0;
$errors = [];

$dish = [
    'name' => '', 'name_urdu' => '', 'category_id' => '', 'description' => '',
    'price' => '', 'prep_time_min' => '', 'spice_level' => 0, 'image_path' => '',
    'mood_tags' => '', 'dietary_tags' => '', 'pairing_note' => '',
    'is_available' => 1, 'is_secret' => 0,
];

$categories = $pdo !== null ? $pdo->query('SELECT id, name FROM categories ORDER BY sort_order')->fetchAll() : [];

if ($isEdit && $pdo !== null && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $pdo->prepare('SELECT * FROM dishes WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    if ($row) {
        $dish = array_merge($dish, $row);
    } else {
        $isEdit = false;
        $id = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dish = array_merge($dish, $_POST);
    $dish['is_available'] = isset($_POST['is_available']) ? 1 : 0;
    $dish['is_secret'] = isset($_POST['is_secret']) ? 1 : 0;

    if (trim($dish['name']) === '') $errors[] = 'Name is required.';
    if (!is_numeric($dish['price']) || (float)$dish['price'] <= 0) $errors[] = 'Price must be a positive number.';
    if ($dish['category_id'] === '') $errors[] = 'Category is required.';
    if (trim($dish['image_path']) === '') $errors[] = 'Image URL is required.';

    if (!$errors && $pdo !== null) {
        $params = [
            'category_id' => (int)$dish['category_id'],
            'name' => trim($dish['name']),
            'name_urdu' => trim((string)($dish['name_urdu'] ?? '')) ?: null,
            'description' => trim((string)$dish['description']),
            'price' => (int)$dish['price'],
            'prep_time_min' => $dish['prep_time_min'] !== '' ? (int)$dish['prep_time_min'] : null,
            'spice_level' => (int)$dish['spice_level'],
            'image_path' => trim($dish['image_path']),
            'mood_tags' => trim((string)($dish['mood_tags'] ?? '')),
            'dietary_tags' => trim((string)($dish['dietary_tags'] ?? '')),
            'pairing_note' => trim((string)($dish['pairing_note'] ?? '')) ?: null,
            'is_available' => $dish['is_available'],
            'is_secret' => $dish['is_secret'],
        ];

        if ($isEdit) {
            $params['id'] = $id;
            $pdo->prepare(
                'UPDATE dishes SET category_id=:category_id, name=:name, name_urdu=:name_urdu, description=:description,
                 price=:price, prep_time_min=:prep_time_min, spice_level=:spice_level, image_path=:image_path,
                 mood_tags=:mood_tags, dietary_tags=:dietary_tags, pairing_note=:pairing_note,
                 is_available=:is_available, is_secret=:is_secret WHERE id=:id'
            )->execute($params);
        } else {
            $pdo->prepare(
                'INSERT INTO dishes (category_id, name, name_urdu, description, price, prep_time_min, spice_level,
                 image_path, mood_tags, dietary_tags, pairing_note, is_available, is_secret)
                 VALUES (:category_id, :name, :name_urdu, :description, :price, :prep_time_min, :spice_level,
                 :image_path, :mood_tags, :dietary_tags, :pairing_note, :is_available, :is_secret)'
            )->execute($params);
        }
        header('Location: ' . BASE_URL . '/admin/menu.php');
        exit;
    }
}

$pageTitle = ($isEdit ? 'Edit Dish' : 'Add Dish') . ' | Admin';
$activeNav = 'menu';
require_once __DIR__ . '/includes/admin-chrome-open.php';
?>
<h1><?= $isEdit ? 'Edit Dish' : 'Add Dish' ?></h1>
<p class="admin-main__sub">Flavor map, ingredients, and journey stages aren't editable here yet — add those directly via SQL (database/step2_menu.sql shows the pattern) until that's wired into this form.</p>

<?php if (!$categories): ?>
  <div class="admin-flash admin-flash--error">Database not connected or no categories found — see README.md setup steps.</div>
<?php endif; ?>
<?php foreach ($errors as $e): ?><div class="admin-flash admin-flash--error"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>

<form method="post" class="admin-form">
  <input type="hidden" name="id" value="<?= $id ?>">
  <div class="admin-form__row">
    <div><label>Name <input type="text" name="name" required value="<?= htmlspecialchars($dish['name']) ?>"></label></div>
    <div><label>Name (Urdu) <input type="text" name="name_urdu" value="<?= htmlspecialchars($dish['name_urdu'] ?? '') ?>"></label></div>
  </div>
  <label>Description <textarea name="description" rows="2" required><?= htmlspecialchars($dish['description']) ?></textarea></label>
  <div class="admin-form__row">
    <div><label>Category
      <select name="category_id" required>
        <option value="">Select…</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= (string)$c['id'] === (string)$dish['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label></div>
    <div><label>Price (Rs.) <input type="number" name="price" min="1" required value="<?= htmlspecialchars((string)$dish['price']) ?>"></label></div>
    <div><label>Spice Level (0-5) <input type="number" name="spice_level" min="0" max="5" value="<?= htmlspecialchars((string)$dish['spice_level']) ?>"></label></div>
    <div><label>Prep Time (min) <input type="number" name="prep_time_min" min="0" value="<?= htmlspecialchars((string)($dish['prep_time_min'] ?? '')) ?>"></label></div>
  </div>
  <label>Image URL <input type="url" name="image_path" required value="<?= htmlspecialchars($dish['image_path']) ?>"></label>
  <div class="admin-form__row">
    <div><label>Mood Tags <small>(comma-separated, e.g. comfort,spicy)</small> <input type="text" name="mood_tags" value="<?= htmlspecialchars($dish['mood_tags'] ?? '') ?>"></label></div>
    <div><label>Dietary Tags <small>(comma-separated)</small> <input type="text" name="dietary_tags" value="<?= htmlspecialchars($dish['dietary_tags'] ?? '') ?>"></label></div>
  </div>
  <label>Pairing Note <input type="text" name="pairing_note" value="<?= htmlspecialchars($dish['pairing_note'] ?? '') ?>"></label>
  <label class="admin-form__checkbox"><input type="checkbox" name="is_available" <?= $dish['is_available'] ? 'checked' : '' ?>> Available on the menu</label>
  <label class="admin-form__checkbox"><input type="checkbox" name="is_secret" <?= $dish['is_secret'] ? 'checked' : '' ?>> Secret menu only</label>

  <div class="admin-actions" style="margin-top:1rem">
    <button type="submit" class="admin-btn admin-btn--primary"><?= $isEdit ? 'Save Changes' : 'Add Dish' ?></button>
    <a href="<?= BASE_URL ?>/admin/menu.php" class="admin-btn">Cancel</a>
  </div>
</form>

<?php require_once __DIR__ . '/includes/admin-chrome-close.php'; ?>
