<?php
/**
 * Shared <head>. Include after setting optional $pageTitle / $pageDescription.
 *
 * Also computes BASE_URL so the site works whether it's served
 * from the domain root or a subfolder (e.g. XAMPP's htdocs/zaiqa/).
 * Every internal link/asset/API call in this project is written
 * relative to BASE_URL rather than hardcoded to "/", specifically
 * so CSS/JS/API paths don't 404 when the project isn't at the
 * server root. See README.md "If your CSS/pages don't load".
 */
$pageTitle       = $pageTitle ?? 'ذائقہ | ZAIQA — A Taste Worth Remembering';
$pageDescription = $pageDescription ?? 'A personalized digital dining experience. Discover food based on your mood, taste, and cravings — then reserve your table at ذائقہ.';

require_once __DIR__ . '/../config/base_url.php';
?><!DOCTYPE html>
<html lang="ur" dir="ltr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?></title>
<meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
<?php if (!empty($pageNoindex)): ?><meta name="robots" content="noindex, nofollow"><?php endif; ?>

<!-- Open Graph -->
<meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
<meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
<meta property="og:type" content="website">

<!-- Restaurant structured data -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Restaurant",
  "name": "ذائقہ (ZAIQA)",
  "servesCuisine": "Pakistani, Contemporary",
  "priceRange": "$$"
}
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;600;700&family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;1,9..144,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/base.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/components.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍽️</text></svg>">
<script>window.APP_BASE = <?= json_encode(BASE_URL) ?>;</script>
<?php
require_once __DIR__ . '/../config/session.php';
$currentUser = zaiqa_current_user();
?>
<script>window.APP_USER = <?= json_encode($currentUser) ?>;</script>
</head>
<body>
