<?php
$pageTitle = 'Menu | ذائقہ — ZAIQA';
$pageDescription = 'Explore the full ذائقہ menu — starters, mains, burgers, pasta, desserts, and drinks. Filter by spice level, search by ingredient, and preview each dish\'s flavor map before you order.';
require_once __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/menu.css">
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="menu-page">
  <section class="menu-hero">
    <div class="container">
      <p class="menu-hero__eyebrow">The Full Menu</p>
      <h1>Every dish has a story.</h1>
      <p class="menu-hero__sub">Search, filter, and open any dish to taste it before you order.</p>
    </div>
  </section>

  <section class="menu-toolbar">
    <div class="container menu-toolbar__inner">
      <div class="menu-search">
        <label for="menuSearch" class="visually-hidden">Search the menu</label>
        <input type="search" id="menuSearch" placeholder="Search dish, ingredient, or taste…" autocomplete="off">
      </div>

      <div class="menu-filters" id="categoryFilters" role="group" aria-label="Filter by category">
        <button class="chip" data-category="all" aria-pressed="true">All</button>
        <button class="chip" data-category="starters" aria-pressed="false">Starters</button>
        <button class="chip" data-category="main-course" aria-pressed="false">Main Course</button>
        <button class="chip" data-category="burgers" aria-pressed="false">Burgers</button>
        <button class="chip" data-category="pasta" aria-pressed="false">Pasta</button>
        <button class="chip" data-category="desserts" aria-pressed="false">Desserts</button>
        <button class="chip" data-category="chef-specials" aria-pressed="false">Chef Specials</button>
        <button class="chip" data-category="drinks" aria-pressed="false">Drinks</button>
      </div>

      <div class="menu-spice">
        <label for="spiceMax">Max spice</label>
        <input type="range" id="spiceMax" min="0" max="5" value="5">
        <span id="spiceMaxValue">🌶️×5</span>
      </div>
    </div>
  </section>

  <section class="menu-grid-section">
    <div class="container">
      <div class="menu-grid" id="menuGrid" aria-live="polite">
        <p class="menu-grid__loading">Loading the menu…</p>
      </div>
    </div>
  </section>
</main>

<!-- Dish preview modal: "Taste Before You Order" -->
<div class="dish-modal" id="dishModal" role="dialog" aria-modal="true" aria-labelledby="dishModalName" hidden>
  <div class="dish-modal__backdrop" id="dishModalBackdrop"></div>
  <div class="dish-modal__panel">
    <button class="dish-modal__close" id="dishModalClose" aria-label="Close">&times;</button>
    <div class="dish-modal__content" id="dishModalContent">
      <!-- Populated by menu.js -->
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/menu.js" defer></script>
