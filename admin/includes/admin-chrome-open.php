<?php
/**
 * Shared admin <head> + sidebar. Include after zaiqa_require_admin()
 * and after setting $pageTitle + $activeNav (one of: dashboard,
 * menu, orders, reservations, customers, tables, secret-menu).
 */
$admin = zaiqa_current_admin();
$pageTitle = $pageTitle ?? 'Admin | ذائقہ';
$activeNav = $activeNav ?? '';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?></title>
<meta name="robots" content="noindex, nofollow">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,600;1,9..144,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/base.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-shell">
  <aside class="admin-sidebar">
    <div class="admin-sidebar__brand">
      <span class="logo-urdu" style="font-size:1.4rem">ذائقہ</span>
      <span class="admin-sidebar__tag">Admin</span>
    </div>
    <nav class="admin-sidebar__nav">
      <a href="<?= BASE_URL ?>/admin/dashboard.php" class="<?= $activeNav === 'dashboard' ? 'is-active' : '' ?>">Dashboard</a>
      <a href="<?= BASE_URL ?>/admin/menu.php" class="<?= $activeNav === 'menu' ? 'is-active' : '' ?>">Menu</a>
      <a href="<?= BASE_URL ?>/admin/orders.php" class="<?= $activeNav === 'orders' ? 'is-active' : '' ?>">Orders</a>
      <a href="<?= BASE_URL ?>/admin/reservations.php" class="<?= $activeNav === 'reservations' ? 'is-active' : '' ?>">Reservations</a>
      <a href="<?= BASE_URL ?>/admin/customers.php" class="<?= $activeNav === 'customers' ? 'is-active' : '' ?>">Customers</a>
      <a href="<?= BASE_URL ?>/admin/tables.php" class="<?= $activeNav === 'tables' ? 'is-active' : '' ?>">Tables</a>
      <a href="<?= BASE_URL ?>/admin/secret-menu.php" class="<?= $activeNav === 'secret-menu' ? 'is-active' : '' ?>">Secret Menu</a>
    </nav>
    <div class="admin-sidebar__footer">
      <p><?= htmlspecialchars($admin['name']) ?></p>
      <form method="post" action="<?= BASE_URL ?>/admin/logout.php"><button type="submit" class="admin-logout-btn">Log Out</button></form>
    </div>
  </aside>
  <main class="admin-main">
