<header class="site-nav" id="siteNav">
  <div class="site-nav__inner">
    <a href="<?= BASE_URL ?>/" class="site-nav__logo" aria-label="ذائقہ — Home">
      <span class="logo-urdu">ذائقہ</span>
      <span class="logo-en">ZAIQA</span>
    </a>

    <nav class="site-nav__links" id="siteNavLinks" aria-label="Primary">
      <a href="<?= BASE_URL ?>/">Experience</a>
      <a href="<?= BASE_URL ?>/pages/discover.php">Discover</a>
      <a href="<?= BASE_URL ?>/pages/menu.php">Menu</a>
      <a href="<?= BASE_URL ?>/pages/kitchen.php">Our Kitchen</a>
      <?php if ($currentUser ?? null): ?>
        <a href="<?= BASE_URL ?>/pages/my-taste.php">Hi, <?= htmlspecialchars(explode(' ', $currentUser['name'])[0]) ?></a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>/pages/login.php">Log In</a>
      <?php endif; ?>
    </nav>

    <div class="site-nav__actions">
      <a href="<?= BASE_URL ?>/pages/order.php" class="site-nav__cart" aria-label="View order">
        🛒 <span class="cart-badge" id="cartBadge" hidden>0</span>
      </a>
      <a href="<?= BASE_URL ?>/pages/reserve.php" class="btn btn--primary btn--sm">Reserve a Table</a>
      <button class="site-nav__toggle" id="navToggle" aria-expanded="false" aria-controls="siteNavLinks" aria-label="Open menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>
