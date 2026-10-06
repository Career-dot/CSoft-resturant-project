<?php
/**
 * Computes BASE_URL so the site works whether served from the
 * domain root or a subfolder (e.g. XAMPP's htdocs/zaiqa/). Both
 * includes/header.php and includes/auth.php require this — auth
 * needs it before header.php runs, since a login redirect has to
 * happen before any HTML is output.
 */

declare(strict_types=1);

if (!defined('BASE_URL')) {
    $projectFsRoot = str_replace('\\', '/', dirname(__DIR__)); // config/ -> project root
    $docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
    $baseUrl = '';
    if ($docRoot !== '' && str_starts_with($projectFsRoot, $docRoot)) {
        $baseUrl = substr($projectFsRoot, strlen($docRoot));
    }
    define('BASE_URL', rtrim($baseUrl, '/')); // '' at the root, '/zaiqa' in a subfolder
}
