<?php
/**
 * ذائقہ (ZAIQA) — Database connection
 * PDO with prepared statements throughout the project.
 */

declare(strict_types=1);

define('DB_HOST', getenv('ZAIQA_DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('ZAIQA_DB_NAME') ?: 'zaiqa');
define('DB_USER', getenv('ZAIQA_DB_USER') ?: 'root');
define('DB_PASS', getenv('ZAIQA_DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a shared PDO connection, or null if the database is
 * unreachable. Callers must handle the null case gracefully —
 * e.g. api/recommendations.php falls back to seed data so the
 * mood selector still works during local frontend development
 * before MySQL is wired up.
 */
function zaiqa_db(): ?PDO
{
    static $pdo = null;
    static $attempted = false;

    if ($pdo !== null || $attempted) {
        return $pdo;
    }
    $attempted = true;

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        error_log('[zaiqa] DB connection failed: ' . $e->getMessage());
        $pdo = null;
    }

    return $pdo;
}
