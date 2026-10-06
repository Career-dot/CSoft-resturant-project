<?php
$pageTitle = 'Privacy Policy | ذائقہ — ZAIQA';
$pageDescription = 'How ذائقہ collects, uses, and protects your information.';
require_once __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/static-pages.css">
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="static-page">
  <section class="static-hero">
    <div class="container">
      <p class="static-hero__eyebrow">Legal</p>
      <h1>Privacy Policy</h1>
    </div>
  </section>

  <section class="legal-content">
    <div class="container legal-prose">
      <p class="legal-updated">Last updated: <?= date('F Y') ?></p>

      <h2>What We Collect</h2>
      <p>When you create an account, make a reservation, or place an order at ذائقہ, we collect the information you provide directly — your name, email, phone number, and any preferences you share, such as your taste personality quiz results or favorite dishes.</p>

      <h2>How We Use It</h2>
      <p>We use your information to process reservations and orders, personalize recommendations, maintain your Food Passport, and communicate with you about your account. We don't sell your information to third parties.</p>

      <h2>Account Data</h2>
      <p>Your password is stored as a one-way hash — we never store or have access to your actual password. Your taste personality, favorites, and order history are tied to your account and visible only to you and our staff.</p>

      <h2>Cookies & Local Storage</h2>
      <p>We use your browser's local storage to remember items in your cart between visits. We use session cookies to keep you logged in. Neither is shared with third parties.</p>

      <h2>Your Choices</h2>
      <p>You can update or delete your account information by contacting us. You may also browse, view the menu, and use most features of the site without creating an account.</p>

      <h2>Questions</h2>
      <p>If you have questions about this policy, reach out via our <a href="<?= BASE_URL ?>/pages/contact.php">contact page</a>.</p>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
