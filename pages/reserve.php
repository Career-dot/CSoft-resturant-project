<?php
$pageTitle = 'Reserve a Table | ذائقہ — ZAIQA';
$pageDescription = 'Reserve your table at ذائقہ — choose your seating vibe, pick your table on our interactive map, and get instant confirmation.';
require_once __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reserve.css">
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="reserve-page">
  <section class="reserve-hero">
    <div class="container">
      <p class="reserve-hero__eyebrow">Reserve a Table</p>
      <h1>Some evenings deserve a plan.</h1>
      <p class="reserve-hero__sub">Choose your vibe, pick your table, and we'll hold it for you.</p>
    </div>
  </section>

  <section class="reserve-wizard">
    <div class="container">

      <ol class="reserve-wizard__tracker" id="wizardTracker">
        <li data-step="1" class="is-active">Vibe</li>
        <li data-step="2">Table</li>
        <li data-step="3">Details</li>
        <li data-step="4">Confirmed</li>
      </ol>

      <!-- Step 1: Vibe selector -->
      <div class="wizard-step" data-step="1">
        <h2 class="wizard-step__title">Where would you like to sit?</h2>
        <div class="vibe-grid" id="vibeGrid">
          <p class="wizard-step__loading">Loading seating options…</p>
        </div>
      </div>

      <!-- Step 2: Table map -->
      <div class="wizard-step" data-step="2" hidden>
        <h2 class="wizard-step__title">Pick your table</h2>
        <p class="wizard-step__sub" id="tableMapSubtitle"></p>
        <div class="table-map" id="tableMap"></div>
        <div class="table-legend">
          <span><i class="dot dot--available"></i> Available</span>
          <span><i class="dot dot--selected"></i> Selected</span>
          <span><i class="dot dot--reserved"></i> Reserved</span>
          <span><i class="dot dot--unavailable"></i> Unavailable</span>
        </div>
        <div class="wizard-nav">
          <button type="button" class="btn btn--ghost" id="backToVibe">Back</button>
          <button type="button" class="btn btn--primary" id="toDetails" disabled>Continue</button>
        </div>
      </div>

      <!-- Step 3: Reservation form -->
      <div class="wizard-step" data-step="3" hidden>
        <h2 class="wizard-step__title">Your details</h2>
        <form id="reservationForm" class="reserve-form" novalidate>
          <div class="reserve-form__row">
            <label>Full Name
              <input type="text" name="name" required>
              <span class="field-error" data-error-for="name"></span>
            </label>
            <label>Phone
              <input type="tel" name="phone" required>
              <span class="field-error" data-error-for="phone"></span>
            </label>
          </div>
          <label>Email
            <input type="email" name="email" required>
            <span class="field-error" data-error-for="email"></span>
          </label>
          <div class="reserve-form__row">
            <label>Date
              <input type="date" name="date" required>
              <span class="field-error" data-error-for="date"></span>
            </label>
            <label>Time
              <input type="time" name="time" required>
              <span class="field-error" data-error-for="time"></span>
            </label>
            <label>Guests
              <input type="number" name="guests" min="1" max="20" value="2" required>
              <span class="field-error" data-error-for="guests"></span>
            </label>
          </div>
          <label>Special Request <span class="optional">(optional)</span>
            <textarea name="special_request" rows="3" placeholder="Anniversary, allergy note, seating preference…"></textarea>
          </label>

          <div class="reserve-form__summary" id="reserveSummary"></div>

          <div class="wizard-nav">
            <button type="button" class="btn btn--ghost" id="backToTable">Back</button>
            <button type="submit" class="btn btn--primary" id="submitReservation">Confirm Reservation</button>
          </div>
        </form>
      </div>

      <!-- Step 4: Confirmation -->
      <div class="wizard-step" data-step="4" hidden>
        <div class="reserve-confirmation" id="reserveConfirmation"></div>
      </div>

    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/reserve.js" defer></script>
