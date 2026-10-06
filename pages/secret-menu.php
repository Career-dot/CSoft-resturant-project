<?php
$pageTitle = 'The Hidden Table | ذائقہ — ZAIQA';
$pageDescription = 'A short, off-menu list at ذائقہ — not printed, not announced.';
require_once __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/kitchen.css">
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="secret-page">
  <section class="secret-hero">
    <div class="container">
      <p class="secret-hero__eyebrow">🔐 Unlocked</p>
      <h1>The Hidden Table</h1>
      <p class="secret-hero__sub">You found it. A short, off-menu list — not printed, not announced.</p>
    </div>
  </section>

  <section class="secret-grid-section">
    <div class="container">
      <div class="secret-grid" id="secretGrid">
        <p class="panel-loading">Unlocking the hidden table…</p>
      </div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/secret-menu.js" defer></script>
