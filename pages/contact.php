<?php
$pageTitle = 'Contact | ذائقہ — ZAIQA';
$pageDescription = 'Get in touch with ذائقہ — hours, location, phone, email, and a contact form.';
require_once __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/static-pages.css">
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="static-page">
  <section class="static-hero">
    <div class="container">
      <p class="static-hero__eyebrow">Get in Touch</p>
      <h1>We'd love to hear from you.</h1>
    </div>
  </section>

  <section class="contact-content">
    <div class="container contact-grid">
      <div class="contact-info">
        <div class="contact-info__block">
          <p class="contact-info__label">Hours</p>
          <p>Daily · 12:00 PM – 1:00 AM</p>
        </div>
        <div class="contact-info__block">
          <p class="contact-info__label">Location</p>
          <p>Faisalabad, Pakistan</p>
          <a href="https://www.google.com/maps/search/?api=1&query=Faisalabad+Pakistan" target="_blank" rel="noopener" class="contact-info__map-link">View on map →</a>
        </div>
        <div class="contact-info__block">
          <p class="contact-info__label">Phone</p>
          <a href="tel:+923000000000">+92 300 0000000</a>
        </div>
        <div class="contact-info__block">
          <p class="contact-info__label">Email</p>
          <a href="mailto:hello@zaiqa.pk">hello@zaiqa.pk</a>
        </div>
      </div>

      <form id="contactForm" class="contact-form" novalidate>
        <h2>Send a Message</h2>
        <label>Name
          <input type="text" name="name" required>
          <span class="field-error" data-error-for="name"></span>
        </label>
        <label>Email
          <input type="email" name="email" required>
          <span class="field-error" data-error-for="email"></span>
        </label>
        <label>Message
          <textarea name="message" rows="5" required></textarea>
          <span class="field-error" data-error-for="message"></span>
        </label>
        <button type="submit" class="btn btn--primary" id="contactSubmitBtn">Send Message</button>
        <p class="contact-form__status" id="contactStatus"></p>
      </form>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/contact.js" defer></script>
