  <footer class="site-footer">
    <div class="site-footer__top">
      <div class="site-footer__brand">
        <span class="logo-urdu">ذائقہ</span>
        <p class="site-footer__tagline">A Taste Worth Remembering.</p>
        <p class="site-footer__blurb">A personalized digital dining experience — where your mood chooses the first bite.</p>
        <div class="site-footer__social">
          <a href="mailto:hello@zaiqa.pk" aria-label="Email ذائقہ" title="Email us">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 6 10-6"/></svg>
          </a>
          <a href="tel:+923000000000" aria-label="Call ذائقہ" title="Call us">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          </a>
          <a href="https://www.google.com/maps/search/?api=1&query=Faisalabad+Pakistan" target="_blank" rel="noopener" aria-label="Find ذائقہ on the map" title="Find us">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          </a>
        </div>
      </div>

      <nav class="site-footer__col" aria-label="Explore">
        <p class="site-footer__col-title">Explore</p>
        <a href="<?= BASE_URL ?>/">Experience</a>
        <a href="<?= BASE_URL ?>/pages/discover.php">Discover</a>
        <a href="<?= BASE_URL ?>/pages/menu.php">Menu</a>
        <a href="<?= BASE_URL ?>/pages/kitchen.php">Our Kitchen</a>
        <a href="<?= BASE_URL ?>/pages/reserve.php">Reserve</a>
      </nav>

      <nav class="site-footer__col" aria-label="Account">
        <p class="site-footer__col-title">Account</p>
        <?php if ($currentUser ?? null): ?>
          <a href="<?= BASE_URL ?>/pages/my-taste.php">My Taste</a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/pages/login.php">Log In</a>
          <a href="<?= BASE_URL ?>/pages/register.php">Create an Account</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/pages/contact.php">Contact</a>
        <a href="<?= BASE_URL ?>/pages/privacy.php">Privacy Policy</a>
        <a href="<?= BASE_URL ?>/pages/terms.php">Terms</a>
      </nav>

      <div class="site-footer__col site-footer__visit">
        <p class="site-footer__col-title">Visit Us</p>
        <p><strong>Hours</strong><br>Daily · 12:00 PM – 1:00 AM</p>
        <p><strong>Location</strong><br>Faisalabad, Pakistan</p>
        <p><strong>Contact</strong><br>+92 300 0000000<br>hello@zaiqa.pk</p>
      </div>
    </div>

    <div class="site-footer__bottom">
      <p class="site-footer__copy">© <?= date('Y') ?> ذائقہ. All rights reserved.</p>
      <p class="site-footer__made">Faisalabad, Pakistan</p>
    </div>
  </footer>

  <script src="<?= BASE_URL ?>/assets/js/cart.js" defer></script>
  <script src="<?= BASE_URL ?>/assets/js/main.js" defer></script>
</body>
</html>
