<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/admin-session.php';
zaiqa_logout_admin();
header('Location: ' . BASE_URL . '/admin/login.php');
exit;
