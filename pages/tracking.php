<?php
$pageTitle = 'Track Your Order | ذائقہ — ZAIQA';
$pageNoindex = true;
require_once __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/tracking.css">
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="tracking-page">
  <section class="tracking-hero">
    <div class="container">
      <p class="tracking-hero__eyebrow">Order Status</p>
      <h1 id="trackingHeading">Tracking your order</h1>
    </div>
  </section>

  <section class="tracking-content">
    <div class="container">
      <div id="trackingLookup" class="tracking-lookup">
        <label for="refInput">Order Reference</label>
        <div class="tracking-lookup__row">
          <input type="text" id="refInput" placeholder="ZO-XXXXXX" autocomplete="off">
          <button type="button" class="btn btn--primary" id="trackBtn">Track</button>
        </div>
        <p class="tracking-lookup__error" id="trackingError"></p>
      </div>

      <div id="trackingResult" class="tracking-result" hidden>
        <div class="tracking-stepper" id="trackingStepper"></div>
        <p class="tracking-note" id="trackingNote"></p>
        <div class="tracking-meta" id="trackingMeta"></div>
      </div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/tracking.js" defer></script>
