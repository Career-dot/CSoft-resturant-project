/**
 * ذائقہ (ZAIQA) — shared cart module.
 * Loaded on every page (via includes/footer.php) so the cart badge
 * stays in sync site-wide. No accounts yet, so the cart lives in
 * localStorage per browser — server-side recomputation of prices
 * happens at checkout time in api/order.php, this is just the
 * client-side working copy.
 */
window.ZaiqaCart = (function () {
  const STORAGE_KEY = 'zaiqa_cart';

  function readCart() {
    try {
      const raw = localStorage.getItem(STORAGE_KEY);
      return raw ? JSON.parse(raw) : [];
    } catch (err) {
      console.error('[zaiqa] cart read failed:', err);
      return [];
    }
  }

  function writeCart(items) {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    } catch (err) {
      console.error('[zaiqa] cart write failed:', err);
    }
    updateBadges();
    document.dispatchEvent(new CustomEvent('zaiqa:cart-updated', { detail: { items } }));
  }

  function lineKey(dishId, customization) {
    return `${dishId}::${JSON.stringify(customization || {})}`;
  }

  function add(item) {
    const items = readCart();
    const key = lineKey(item.dish_id, item.customization);
    const existing = items.find((i) => i.key === key);

    if (existing) {
      existing.quantity += item.quantity || 1;
    } else {
      items.push({
        key,
        dish_id: item.dish_id,
        name: item.name,
        image: item.image,
        unit_price: item.unit_price,
        customization: item.customization || null,
        quantity: item.quantity || 1,
      });
    }
    writeCart(items);
  }

  function removeByKey(key) {
    writeCart(readCart().filter((i) => i.key !== key));
  }

  function updateQuantity(key, quantity) {
    const items = readCart();
    const item = items.find((i) => i.key === key);
    if (!item) return;
    if (quantity <= 0) {
      writeCart(items.filter((i) => i.key !== key));
    } else {
      item.quantity = quantity;
      writeCart(items);
    }
  }

  function clear() {
    writeCart([]);
  }

  function getItems() {
    return readCart();
  }

  function getTotal() {
    return readCart().reduce((sum, i) => sum + i.unit_price * i.quantity, 0);
  }

  function getCount() {
    return readCart().reduce((sum, i) => sum + i.quantity, 0);
  }

  function updateBadges() {
    const count = getCount();
    document.querySelectorAll('.cart-badge').forEach((el) => {
      el.textContent = String(count);
      el.hidden = count === 0;
    });
  }

  window.addEventListener('storage', (e) => {
    if (e.key === STORAGE_KEY) updateBadges();
  });
  document.addEventListener('DOMContentLoaded', updateBadges);
  if (document.readyState !== 'loading') updateBadges();

  return { add, removeByKey, updateQuantity, clear, getItems, getTotal, getCount, lineKey };
})();
