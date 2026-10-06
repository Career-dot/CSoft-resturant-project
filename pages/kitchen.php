<?php
$pageTitle = 'Our Kitchen | ذائقہ — ZAIQA';
$pageDescription = 'Step inside the ذائقہ kitchen — our cooking philosophy, what\'s happening right now, and a few words from our chef.';
require_once __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/kitchen.css">
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="kitchen-page">
  <section class="kitchen-hero">
    <div class="container">
      <p class="kitchen-hero__eyebrow">Inside Our Kitchen</p>
      <h1>Every dish has a story. This is where it starts.</h1>
      <p class="kitchen-hero__sub">Charcoal, patience, and a few things we don't write down.</p>
    </div>
  </section>

  <section class="kitchen-philosophy">
    <div class="container kitchen-philosophy__grid">
      <div>
        <h2>Our Philosophy</h2>
        <p>We cook the way we'd want to be cooked for: nothing rushed, nothing hidden behind a sauce that's doing the work a good ingredient should. Spice is a language here, not a warning label — every dish is built in layers, so the first bite and the last one tell different parts of the same story.</p>
        <p>Most of what leaves this kitchen started as something simple. Ginger, char, citrus, time. The rest is just paying attention.</p>
      </div>
      <div class="kitchen-status-card">
        <h3>What's Happening Right Now</h3>
        <p class="kitchen-status-card__note">A live-feeling snapshot of tonight's kitchen — simulated for now, until our admin dashboard can report the real thing.</p>
        <div class="kitchen-status-grid" id="kitchenStatusGrid">
          <p class="panel-loading">Checking the kitchen…</p>
        </div>
      </div>
    </div>
  </section>

  <section class="chef-section">
    <div class="container">
      <p class="chef-section__eyebrow">Meet Our Chef</p>
      <h2>Chef Imran Baig</h2>
      <div class="chef-grid">
        <div class="chef-card">
          <p class="chef-card__label">Signature Dish</p>
          <p class="chef-card__value">Signature Fire Steak</p>
        </div>
        <div class="chef-card">
          <p class="chef-card__label">Favorite Ingredient</p>
          <p class="chef-card__value">Smoked paprika</p>
        </div>
        <div class="chef-card">
          <p class="chef-card__label">Favorite Spice</p>
          <p class="chef-card__value">Green cardamom</p>
        </div>
        <div class="chef-card">
          <p class="chef-card__label">Favorite Drink</p>
          <p class="chef-card__value">Spiced Ginger Mocktail</p>
        </div>
      </div>
      <p class="chef-story">Started in his grandmother's kitchen in Faisalabad, trained across Lahore and Dubai, and came back to build ذائقہ around one idea: food should taste like it remembers where it's from, even when it's doing something new.</p>
    </div>
  </section>

  <section class="ask-chef">
    <div class="container">
      <h2>Ask the Chef</h2>
      <p class="ask-chef__sub">A few things people ask often. Tap a question.</p>

      <div class="faq-list" id="faqList">
        <button class="faq-item" data-answer="Yes — tell your server about any allergy and we'll adjust the dish or point you toward something safer on the menu.">
          Can dishes be made allergy-friendly?
        </button>
        <button class="faq-item" data-answer="The Signature Fire Steak. It's the dish that made me want to open ذائقہ in the first place.">
          What's your personal favorite on the menu?
        </button>
        <button class="faq-item" data-answer="Smoked paprika, hands down. It does the work of three ingredients at once — color, warmth, and a little smoke.">
          What ingredient could you not cook without?
        </button>
        <button class="faq-item" data-answer="Taste as you go, and salt in layers, not all at once at the end.">
          Any advice for home cooks?
        </button>
      </div>
      <div class="faq-answer" id="faqAnswer" hidden></div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/kitchen.js" defer></script>
