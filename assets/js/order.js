/**
 * Order page: renders the cart (from window.ZaiqaCart), wires
 * Surprise Me (/api/surprise.php) and Budget Mode (/api/budget.php),
 * and submits checkout to /api/order.php.
 */
(function () {
  const cartList = document.getElementById('cartList');
  const cartSummary = document.getElementById('cartSummary');
  const cartTotal = document.getElementById('cartTotal');
  const checkoutForm = document.getElementById('checkoutForm');
  const confirmationBox = document.getElementById('orderConfirmation');

  if (!cartList || !window.ZaiqaCart) return;

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }

  function formatCustomization(c) {
    if (!c) return '';
    const parts = [];
    Object.entries(c).forEach(([group, val]) => {
      const label = Array.isArray(val) ? val.join(', ') : val;
      if (label) parts.push(label);
    });
    return parts.join(' · ');
  }

  /* ---------- Cart rendering ---------- */

  function renderCart() {
    const items = window.ZaiqaCart.getItems();

    if (!items.length) {
      cartList.innerHTML = '<p class="cart-list__empty">Your cart is empty — browse the menu or try Surprise Me above.</p>';
      cartSummary.hidden = true;
      checkoutForm.hidden = true;
      return;
    }

    cartList.innerHTML = items
      .map(
        (item) => `
      <div class="cart-item" data-key="${escapeHtml(item.key)}">
        <img class="cart-item__image" src="${escapeHtml(item.image || '')}" alt="${escapeHtml(item.name)}" loading="lazy">
        <div class="cart-item__body">
          <p class="cart-item__name">${escapeHtml(item.name)}</p>
          ${item.customization ? `<p class="cart-item__custom">${escapeHtml(formatCustomization(item.customization))}</p>` : ''}
        </div>
        <div class="cart-item__qty">
          <button type="button" class="qty-minus" aria-label="Decrease quantity">−</button>
          <span>${item.quantity}</span>
          <button type="button" class="qty-plus" aria-label="Increase quantity">+</button>
        </div>
        <div>
          <p class="cart-item__price">Rs. ${item.unit_price * item.quantity}</p>
          <button type="button" class="cart-item__remove">Remove</button>
        </div>
      </div>`
      )
      .join('');

    cartSummary.hidden = false;
    cartTotal.textContent = `Rs. ${window.ZaiqaCart.getTotal()}`;
    checkoutForm.hidden = false;
  }

  cartList.addEventListener('click', (e) => {
    const row = e.target.closest('.cart-item');
    if (!row) return;
    const key = row.dataset.key;
    const items = window.ZaiqaCart.getItems();
    const item = items.find((i) => i.key === key);
    if (!item) return;

    if (e.target.classList.contains('qty-plus')) {
      window.ZaiqaCart.updateQuantity(key, item.quantity + 1);
    } else if (e.target.classList.contains('qty-minus')) {
      window.ZaiqaCart.updateQuantity(key, item.quantity - 1);
    } else if (e.target.classList.contains('cart-item__remove')) {
      window.ZaiqaCart.removeByKey(key);
    }
  });

  document.addEventListener('zaiqa:cart-updated', renderCart);

  /* ---------- Surprise Me ---------- */

  const surpriseBtn = document.getElementById('surpriseBtn');
  const surpriseResult = document.getElementById('surpriseResult');

  async function rollSurprise() {
    surpriseResult.hidden = false;
    surpriseResult.innerHTML = '<p>Rolling the dice…</p>';
    try {
      const res = await fetch(`${window.APP_BASE}/api/surprise.php`, { headers: { Accept: 'application/json' } });
      if (!res.ok) throw new Error(`Request failed: ${res.status}`);
      const dish = await res.json();
      renderSurprise(dish);
    } catch (err) {
      surpriseResult.innerHTML = '<p>Couldn\u2019t roll right now — try again.</p>';
      console.error('[zaiqa] surprise fetch failed:', err);
    }
  }

  function renderSurprise(dish) {
    surpriseResult.innerHTML = `
      <img class="surprise-result__image" src="${escapeHtml(dish.image)}" alt="${escapeHtml(dish.name)}" loading="lazy">
      <div>
        <p class="surprise-result__name">${escapeHtml(dish.name)}</p>
        <p class="surprise-result__meta">${escapeHtml(dish.category)} · Rs. ${escapeHtml(String(dish.price))}</p>
        <div class="surprise-result__actions">
          <button type="button" class="is-primary" id="acceptSurprise">Add to Order</button>
          <button type="button" id="retrySurprise">Try Again</button>
        </div>
      </div>
    `;
    document.getElementById('acceptSurprise').addEventListener('click', () => {
      window.ZaiqaCart.add({ dish_id: dish.id, name: dish.name, image: dish.image, unit_price: dish.price, quantity: 1 });
      document.getElementById('acceptSurprise').textContent = 'Added ✓';
      document.getElementById('acceptSurprise').disabled = true;
    });
    document.getElementById('retrySurprise').addEventListener('click', rollSurprise);
  }

  surpriseBtn.addEventListener('click', rollSurprise);

  /* ---------- Budget Mode ---------- */

  const budgetBtn = document.getElementById('budgetBtn');
  const budgetAmount = document.getElementById('budgetAmount');
  const budgetResult = document.getElementById('budgetResult');

  async function buildMeal() {
    const budget = Number(budgetAmount.value);
    budgetResult.hidden = false;
    budgetResult.innerHTML = '<p>Building your meal…</p>';

    try {
      const res = await fetch(`${window.APP_BASE}/api/budget.php?budget=${encodeURIComponent(budget)}`, { headers: { Accept: 'application/json' } });
      const data = await res.json();
      if (!res.ok) {
        budgetResult.innerHTML = `<p>${escapeHtml(data.error || 'Couldn\u2019t build a meal for that budget.')}</p>`;
        return;
      }
      renderBudgetResult(data);
    } catch (err) {
      budgetResult.innerHTML = '<p>Couldn\u2019t reach the kitchen right now — try again.</p>';
      console.error('[zaiqa] budget fetch failed:', err);
    }
  }

  function renderBudgetResult(data) {
    if (!data.items.length) {
      budgetResult.innerHTML = '<p class="budget-result__note">Even our most affordable main doesn\u2019t fit that budget yet — try raising it a little.</p>';
      return;
    }
    budgetResult.innerHTML = `
      ${!data.complete ? '<p class="budget-result__note">Tight budget — we kept it to the essentials.</p>' : ''}
      <div class="budget-result__items">
        ${data.items.map((i) => `<div class="budget-result__item"><span>${escapeHtml(i.name)}</span><span>Rs. ${i.price}</span></div>`).join('')}
      </div>
      <div class="budget-result__total"><span>Total</span><span>Rs. ${data.total} <span style="font-weight:400;color:var(--color-muted)">of Rs. ${data.budget}</span></span></div>
      <button type="button" class="btn btn--primary" id="addMealBtn" style="margin-top:1rem">Add All to Order</button>
    `;
    document.getElementById('addMealBtn').addEventListener('click', () => {
      data.items.forEach((i) => {
        window.ZaiqaCart.add({ dish_id: i.id, name: i.name, image: i.image, unit_price: i.price, quantity: 1 });
      });
      document.getElementById('addMealBtn').textContent = 'Added ✓';
      document.getElementById('addMealBtn').disabled = true;
    });
  }

  budgetBtn.addEventListener('click', buildMeal);

  /* ---------- Checkout ---------- */

  checkoutForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(checkoutForm);
    const items = window.ZaiqaCart.getItems().map((i) => ({
      dish_id: i.dish_id,
      quantity: i.quantity,
      customization: i.customization,
    }));

    const submitBtn = document.getElementById('placeOrderBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Placing order…';

    try {
      const res = await fetch(`${window.APP_BASE}/api/order.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          name: fd.get('name'),
          email: fd.get('email'),
          phone: fd.get('phone'),
          notes: fd.get('notes'),
          items,
        }),
      });
      const data = await res.json();

      if (!res.ok) {
        alert(Object.values(data.errors || { e: 'Something went wrong.' }).join('\n'));
        submitBtn.disabled = false;
        submitBtn.textContent = 'Place Order';
        return;
      }

      renderConfirmation(data);
      window.ZaiqaCart.clear();
      cartList.parentElement.querySelectorAll('.cart-list, .cart-summary, .checkout-form').forEach((el) => (el.hidden = true));
    } catch (err) {
      alert('Couldn\u2019t place your order right now — please try again.');
      console.error('[zaiqa] order submit failed:', err);
      submitBtn.disabled = false;
      submitBtn.textContent = 'Place Order';
    }
  });

  function renderConfirmation(data) {
    confirmationBox.hidden = false;
    confirmationBox.innerHTML = `
      <p class="order-confirmation__icon">👨‍🍳</p>
      <h2>Order received.</h2>
      <div class="order-confirmation__ref">${escapeHtml(data.reference_code)}</div>
      <p>Total: Rs. ${escapeHtml(String(data.total))}</p>
      ${!data.persisted ? '<p class="order-confirmation__note">Note: the database isn\u2019t connected yet in this environment, so this confirmation was generated but not saved.</p>' : ''}
      <div style="display:flex; gap:0.75rem; justify-content:center; flex-wrap:wrap; margin-top:1rem">
        ${data.persisted ? `<a href="${window.APP_BASE}/pages/tracking.php?ref=${encodeURIComponent(data.reference_code)}" class="btn btn--primary">Track Your Order</a>` : ''}
        <a href="${window.APP_BASE}/pages/menu.php" class="btn btn--ghost">Back to Menu</a>
      </div>
    `;
  }

  renderCart();
})();
