<?php
/**
 * POST /api/logout.php
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/session.php';

zaiqa_logout_user();

echo json_encode(['ok' => true]);
