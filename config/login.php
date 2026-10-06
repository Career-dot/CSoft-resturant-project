<?php
require_once __DIR__ . '/../config/session.php';
if (zaiqa_current_user() !== null) {
    header('Location: ' . BASE_URL . '/pages/my-taste.php');
    exit;
}
$pageTitle = 'Log In | ذائقہ — ZAIQA';
require_once __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/auth.css">
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="auth-page">
  <section class="auth-card">
    <p class="auth-card__eyebrow">Welcome Back</p>
    <h1>Log In</h1>
    <p class="auth-card__sub">Your taste, your favorites, your passport — right where you left them.</p>

    <form id="loginForm" class="auth-form" novalidate>
      <label>Email
        <input type="email" name="email" required>
      </label>
      <label>Password
        <input type="password" name="password" required>
      </label>
      <p class="auth-form__error" id="loginError"></p>
      <button type="submit" class="btn btn--primary" id="loginBtn">Log In</button>
    </form>

    <p class="auth-card__switch">New to ذائقہ? <a href="<?= BASE_URL ?>/pages/register.php">Create an account</a></p>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/auth.js" defer></script>