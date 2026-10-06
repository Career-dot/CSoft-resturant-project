<?php
require_once __DIR__ . '/../config/session.php';
if (zaiqa_current_user() !== null) {
    header('Location: ' . BASE_URL . '/pages/my-taste.php');
    exit;
}
$pageTitle = 'Create an Account | ذائقہ — ZAIQA';
require_once __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/auth.css">
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="auth-page">
  <section class="auth-card">
    <p class="auth-card__eyebrow">Join ذائقہ</p>
    <h1>Create an Account</h1>
    <p class="auth-card__sub">Save your taste personality, favorites, and build your Food Passport.</p>

    <form id="registerForm" class="auth-form" novalidate>
      <label>Full Name
        <input type="text" name="name" required>
        <span class="field-error" data-error-for="name"></span>
      </label>
      <label>Email
        <input type="email" name="email" required>
        <span class="field-error" data-error-for="email"></span>
      </label>
      <label>Phone <span class="optional">(optional)</span>
        <input type="tel" name="phone">
      </label>
      <label>Password
        <input type="password" name="password" required>
        <span class="field-error" data-error-for="password"></span>
      </label>
      <label>Confirm Password
        <input type="password" name="confirm_password" required>
        <span class="field-error" data-error-for="confirm_password"></span>
      </label>
      <button type="submit" class="btn btn--primary" id="registerBtn">Create Account</button>
    </form>

    <p class="auth-card__switch">Already have an account? <a href="<?= BASE_URL ?>/pages/login.php">Log in</a></p>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/auth.js" defer></script>