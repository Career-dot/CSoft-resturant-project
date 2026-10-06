<?php
$pageTitle = 'Terms | ذائقہ — ZAIQA';
$pageDescription = 'Terms of service for using the ذائقہ website, ordering, and reservations.';
require_once __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/static-pages.css">
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="static-page">
  <section class="static-hero">
    <div class="container">
      <p class="static-hero__eyebrow">Legal</p>
      <h1>Terms of Service</h1>
    </div>
  </section>

  <section class="legal-content">
    <div class="container legal-prose">
      <p class="legal-updated">Last updated: <?= date('F Y') ?></p>

      <h2>Using This Site</h2>
      <p>By using ذائقہ's website, you agree to provide accurate information when creating an account, making a reservation, or placing an order, and to use the site for its intended purpose.</p>

      <h2>Reservations & Orders</h2>
      <p>Reservations are held based on availability and are subject to change. Order prices are calculated at checkout and reflect the dish's current price plus any customizations selected. We reserve the right to cancel an order or reservation if details appear fraudulent or cannot be fulfilled.</p>

      <h2>Accounts</h2>
      <p>You're responsible for keeping your account password secure. Let us know right away if you believe your account has been accessed without your permission.</p>

      <h2>Content</h2>
      <p>Dish descriptions, images, and pricing are provided for informational purposes and may change without notice. Prices are listed in Pakistani Rupees (Rs.) unless stated otherwise.</p>

      <h2>Changes to These Terms</h2>
      <p>We may update these terms from time to time. Continued use of the site after changes are posted means you accept the updated terms.</p>

      <h2>Questions</h2>
      <p>Reach out via our <a href="<?= BASE_URL ?>/pages/contact.php">contact page</a> if anything here is unclear.</p>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
