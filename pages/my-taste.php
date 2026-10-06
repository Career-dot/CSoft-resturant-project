<?php
require_once __DIR__ . '/../config/session.php';
zaiqa_require_login(); // redirects to login.php if not authenticated — must run before any output

$user = zaiqa_current_user();
$pageTitle = 'My Taste | ذائقہ — ZAIQA';
$pageNoindex = true;
require_once __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/auth.css">
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="my-taste-page">
  <section class="my-taste-hero">
    <div class="container">
      <p class="my-taste-hero__eyebrow">My Taste</p>
      <h1>Welcome back, <?= htmlspecialchars($user['name']) ?>.</h1>
      <button type="button" class="btn btn--ghost" id="logoutBtn">Log Out</button>
    </div>
  </section>

  <section class="my-taste-content">
    <div class="container my-taste-grid">

      <div class="my-taste-panel" id="tastePanel">
        <h2>Your Taste Personality</h2>
        <div id="tasteContent"><p class="panel-loading">Loading…</p></div>
      </div>

      <div class="my-taste-panel" id="passportPanel">
        <h2>Food Passport</h2>
        <div id="passportContent"><p class="panel-loading">Loading…</p></div>
      </div>

      <div class="my-taste-panel my-taste-panel--wide" id="favoritesPanel">
        <h2>Favorites</h2>
        <div id="favoritesContent"><p class="panel-loading">Loading…</p></div>
      </div>

    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/my-taste.js" defer></script>
