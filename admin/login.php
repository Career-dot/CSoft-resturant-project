<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/admin-session.php';

if (zaiqa_current_admin() !== null) {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}
if (zaiqa_admin_table_empty()) {
    header('Location: ' . BASE_URL . '/admin/setup.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $pdo = zaiqa_db();

    if ($pdo === null) {
        $error = 'Database not connected — see README.md setup steps.';
    } else {
        $stmt = $pdo->prepare('SELECT id, name, email, password_hash FROM admins WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            zaiqa_login_admin($admin);
            header('Location: ' . BASE_URL . '/admin/dashboard.php');
            exit;
        }
        $error = 'Incorrect email or password.';
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | ذائقہ</title>
<meta name="robots" content="noindex, nofollow">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/base.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body>
<div class="admin-auth-page">
  <div class="admin-auth-card">
    <h1>Admin Login</h1>
    <p class="sub">ذائقہ staff only.</p>
    <?php if ($error): ?><div class="admin-flash admin-flash--error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post" class="admin-form" style="box-shadow:none;padding:0">
      <label>Email <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></label>
      <label>Password <input type="password" name="password" required></label>
      <button type="submit" class="admin-btn admin-btn--primary" style="width:100%;padding:0.75rem">Log In</button>
    </form>
  </div>
</div>
</body>
</html>
