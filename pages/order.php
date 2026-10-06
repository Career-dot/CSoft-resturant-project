<?php
$pageTitle = 'Your Order | ذائقہ — ZAIQA';
$pageDescription = 'Build your order at ذائقہ — customize your dish, try Surprise Me, or let Budget Mode build a full meal around what you want to spend.';
require_once __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/order.css">
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="order-page">
  <section class="order-hero">
    <div class="container">
      <p class="order-hero__eyebrow">Your Order</p>
      <h1>Not sure yet? Let us decide.</h1>
      <p class="order-hero__sub">Build your own plate, roll the dice, or tell us your budget.</p>
    </div>
  </section>

  <section class="order-tools">
    <div class="container order-tools__grid">

      <!-- Surprise Me -->
      <div class="tool-card" id="surpriseCard">
        <h2 class="tool-card__title">🎲 Surprise Me</h2>
        <p class="tool-card__sub">You didn't choose the dish. Your taste did.</p>
        <button type="button" class="btn btn--primary" id="surpriseBtn">Surprise Me</button>
        <div class="surprise-result" id="surpriseResult" hidden></div>
      </div>

      <!-- Budget Mode -->
      <div class="tool-card" id="budgetCard">
        <h2 class="tool-card__title">Build My Meal</h2>
        <p class="tool-card__sub">Tell us your budget — we'll build a full plate around it.</p>
        <div class="budget-input">
          <label for="budgetAmount">Rs.</label>
          <input type="number" id="budgetAmount" min="200" step="50" value="1500">
          <button type="button" class="btn btn--primary" id="budgetBtn">Build My Meal</button>
        </div>
        <div class="budget-result" id="budgetResult" hidden></div>
      </div>

    </div>
  </section>

  <section class="order-cart">
    <div class="container">
      <h2 class="order-cart__title">Your Cart</h2>
      <div class="cart-list" id="cartList"></div>

      <div class="cart-summary" id="cartSummary" hidden>
        <div class="cart-summary__row cart-summary__row--total">
          <span>Total</span>
          <span id="cartTotal">Rs. 0</span>
        </div>
      </div>

      <form id="checkoutForm" class="checkout-form" hidden>
        <h3>Checkout Details</h3>
        <div class="checkout-form__row">
          <label>Name
            <input type="text" name="name" required>
          </label>
          <label>Phone
            <input type="tel" name="phone" required>
          </label>
        </div>
        <label>Email <span class="optional">(optional)</span>
          <input type="email" name="email">
        </label>
        <label>Notes <span class="optional">(optional)</span>
          <textarea name="notes" rows="2" placeholder="Table number, delivery note, allergy info…"></textarea>
        </label>
        <button type="submit" class="btn btn--primary" id="placeOrderBtn">Place Order</button>
      </form>

      <div class="order-confirmation" id="orderConfirmation" hidden></div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/order.js" defer></script>
