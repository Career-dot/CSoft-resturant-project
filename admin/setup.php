<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/admin-session.php';

if (!zaiqa_admin_table_empty()) {
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');

    if ($name === '') $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $pdo = zaiqa_db();
        if ($pdo === null) {
            $errors[] = 'Database not connected — see README.md setup steps.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO admins (name, email, password_hash) VALUES (:name, :email, :hash)');
            $stmt->execute(['name' => $name, 'email' => $email, 'hash' => password_hash($password, PASSWORD_DEFAULT)]);
            $id = (int)$pdo->lastInsertId();
            zaiqa_login_admin(['id' => $id, 'name' => $name, 'email' => $email]);
            header('Location: ' . BASE_URL . '/admin/dashboard.php');
            exit;
        }
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Setup | ذائقہ</title>
<meta name="robots" content="noindex, nofollow">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/base.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body>
<div class="admin-auth-page">
  <div class="admin-auth-card">
    <h1>Create the First Admin</h1>
    <p class="sub">This page only works while no admin account exists, and locks itself right after.</p>
    <?php foreach ($errors as $e): ?>
      <div class="admin-flash admin-flash--error"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>
    <form method="post" class="admin-form" style="box-shadow:none;padding:0">
      <label>Name <input type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"></label>
      <label>Email <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></label>
      <label>Password <input type="password" name="password" required></label>
      <label>Confirm Password <input type="password" name="confirm_password" required></label>
      <button type="submit" class="admin-btn admin-btn--primary" style="width:100%;padding:0.75rem">Create Admin Account</button>
    </form>
  </div>
</div>
</body>
</html>
