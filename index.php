<?php
$pageTitle = 'ذائقہ | ZAIQA — A Taste Worth Remembering';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main>
  <section class="hero">
    <div class="hero__inner">
      <p class="hero__eyebrow">A Personalized Dining Experience</p>
      <h1 class="hero__logo">ذائقہ</h1>
      <p class="hero__tagline">A Taste Worth Remembering.</p>
      <p class="hero__sub">Discover food based on your mood, taste, occasion, and cravings — then let ذائقہ guide the rest of the evening.</p>

      <div class="hero__actions">
        <a href="<?= BASE_URL ?>/pages/discover.php" class="btn btn--primary">Discover Your Taste</a>
        <a href="<?= BASE_URL ?>/pages/reserve.php" class="btn btn--ghost">Reserve a Table</a>
      </div>

      <div class="mood-section">
        <h2 class="mood-section__title">How are you feeling today?</h2>

        <div class="mood-grid" id="moodGrid" role="group" aria-label="Choose your mood">
          <button class="mood-chip" data-mood="comfort" aria-pressed="false">
            <span class="mood-chip__emoji" aria-hidden="true"></span> Comfort
          </button>
          <button class="mood-chip" data-mood="spicy" aria-pressed="false">
            <span class="mood-chip__emoji" aria-hidden="true"></span> Spicy
          </button>
          <button class="mood-chip" data-mood="fancy" aria-pressed="false">
            <span class="mood-chip__emoji" aria-hidden="true"></span> Fancy
          </button>
          <button class="mood-chip" data-mood="fresh" aria-pressed="false">
            <span class="mood-chip__emoji" aria-hidden="true"></span> Fresh
          </button>
          <button class="mood-chip" data-mood="sweet" aria-pressed="false">
            <span class="mood-chip__emoji" aria-hidden="true"></span> Sweet
          </button>
          <button class="mood-chip" data-mood="late_night" aria-pressed="false">
            <span class="mood-chip__emoji" aria-hidden="true"></span> Late Night Craving
          </button>
        </div>

        <div class="mood-results" id="moodResults" hidden>
          <p class="mood-results__label" id="moodResultsLabel"></p>
          <div class="mood-results__grid" id="moodResultsGrid"></div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
<script src="<?= BASE_URL ?>/assets/js/mood-selector.js" defer></script>
