/**
 * Interactive menu: search/filter grid (calls /api/menu.php) and
 * the "Taste Before You Order" dish preview modal (calls
 * /api/dish.php?id=).
 */
(function () {
  const grid = document.getElementById('menuGrid');
  const searchInput = document.getElementById('menuSearch');
  const categoryFilters = document.getElementById('categoryFilters');
  const spiceMax = document.getElementById('spiceMax');
  const spiceMaxValue = document.getElementById('spiceMaxValue');

  const modal = document.getElementById('dishModal');
  const modalBackdrop = document.getElementById('dishModalBackdrop');
  const modalClose = document.getElementById('dishModalClose');
  const modalContent = document.getElementById('dishModalContent');

  if (!grid) return;

  let state = { search: '', category: 'all', spiceMax: 5 };
  let debounceTimer = null;

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }

  async function loadMenu() {
    grid.innerHTML = '<p class="menu-grid__loading">Loading the menu…</p>';
    const params = new URLSearchParams({
      search: state.search,
      category: state.category,
      spice_max: String(state.spiceMax),
    });

    try {
      const res = await fetch(`${window.APP_BASE}/api/menu.php?${params.toString()}`, { headers: { Accept: 'application/json' } });
      if (!res.ok) throw new Error(`Request failed: ${res.status}`);
      const data = await res.json();
      renderGrid(data.dishes || []);
    } catch (err) {
      grid.innerHTML = '<p class="menu-grid__empty">Couldn\u2019t load the menu right now. Please try again.</p>';
      console.error('[zaiqa] menu fetch failed:', err);
    }
  }

  function renderGrid(dishes) {
    if (!dishes.length) {
      grid.innerHTML = '<p class="menu-grid__empty">No dishes match those filters yet.</p>';
      return;
    }
    grid.innerHTML = dishes
      .map(
        (d, i) => `
      <button class="menu-dish-card" data-id="${d.id}" style="animation-delay:${Math.min(i, 8) * 60}ms">
        <div class="menu-dish-card__image-wrap">
          <img class="menu-dish-card__image" src="${escapeHtml(d.image)}" alt="${escapeHtml(d.name)}" loading="lazy">
          <span class="menu-dish-card__spice">${'🌶️'.repeat(Math.max(1, d.spice_level)) || '—'} ${d.spice_level}/5</span>
        </div>
        <div class="menu-dish-card__body">
          <p class="menu-dish-card__category">${escapeHtml(d.category)}</p>
          <h3 class="menu-dish-card__name">${escapeHtml(d.name)}</h3>
          <p class="menu-dish-card__desc">${escapeHtml(d.description)}</p>
          <div class="menu-dish-card__footer">
            <span class="menu-dish-card__price">Rs. ${escapeHtml(String(d.price))}</span>
            <span class="menu-dish-card__cta">Taste before you order →</span>
          </div>
        </div>
      </button>`
      )
      .join('');
  }

  grid.addEventListener('click', (e) => {
    const card = e.target.closest('.menu-dish-card');
    if (card) openDish(card.dataset.id);
  });

  searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      state.search = searchInput.value.trim();
      loadMenu();
    }, 300);
  });

  categoryFilters.addEventListener('click', (e) => {
    const chip = e.target.closest('.chip');
    if (!chip) return;
    categoryFilters.querySelectorAll('.chip').forEach((c) => c.setAttribute('aria-pressed', 'false'));
    chip.setAttribute('aria-pressed', 'true');
    state.category = chip.dataset.category;
    loadMenu();
  });

  spiceMax.addEventListener('input', () => {
    state.spiceMax = Number(spiceMax.value);
    spiceMaxValue.textContent = `🌶️×${spiceMax.value}`;
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(loadMenu, 200);
  });

  /* ---------- Dish preview modal ---------- */

  async function openDish(id) {
    modal.hidden = false;
    document.body.style.overflow = 'hidden';
    modalContent.innerHTML = '<p class="menu-grid__loading">Preparing your preview…</p>';

    try {
      const res = await fetch(`${window.APP_BASE}/api/dish.php?id=${encodeURIComponent(id)}`, { headers: { Accept: 'application/json' } });
      if (!res.ok) throw new Error(`Request failed: ${res.status}`);
      const dish = await res.json();
      renderDish(dish);
    } catch (err) {
      modalContent.innerHTML = '<div class="dish-detail__body"><p>Couldn\u2019t load this dish. Please try again.</p></div>';
      console.error('[zaiqa] dish fetch failed:', err);
    }
  }

  function flavorRow(label, value) {
    const dots = Array.from({ length: 5 }, (_, i) => {
      const filled = i < value;
      return `<span class="flavor-dot ${filled ? 'is-filled' : ''}" style="animation-delay:${i * 60}ms"></span>`;
    }).join('');
    return `<div class="flavor-row"><span class="flavor-row__label">${label}</span><div class="flavor-dots">${dots}</div></div>`;
  }

  function renderDish(d) {
    const flavor = d.flavor || {};
    const journey = d.journey || [];
    const ingredients = d.ingredients || [];
    const dietary = d.dietary_tags || [];
    const customization = d.customization;

    modalContent.innerHTML = `
      <img class="dish-detail__image" src="${escapeHtml(d.image)}" alt="${escapeHtml(d.name)}" loading="lazy">
      <div class="dish-detail__body">
        <p class="dish-detail__category" id="dishModalName">${escapeHtml(d.category)}</p>
        <h2 class="dish-detail__name">${escapeHtml(d.name)}</h2>
        ${d.name_urdu ? `<p class="dish-detail__name-urdu">${escapeHtml(d.name_urdu)}</p>` : ''}
        <p class="dish-detail__desc">${escapeHtml(d.description)}</p>

        <div class="dish-detail__meta">
          <span>Rs. ${escapeHtml(String(d.price))}</span>
          ${d.prep_time_min ? `<span>${escapeHtml(String(d.prep_time_min))} min prep</span>` : ''}
          ${dietary.length ? `<span>${dietary.map(escapeHtml).join(' · ')}</span>` : ''}
        </div>

        ${ingredients.length ? `
          <p class="dish-detail__section-title">Ingredients</p>
          <div class="dish-detail__ingredients">${ingredients.map((i) => `<span>${escapeHtml(i)}</span>`).join('')}</div>
        ` : ''}

        <p class="dish-detail__section-title">Flavor Map</p>
        <div class="flavor-map">
          ${flavorRow('Spice', flavor.spice || 0)}
          ${flavorRow('Saltiness', flavor.saltiness || 0)}
          ${flavorRow('Tanginess', flavor.tanginess || 0)}
          ${flavorRow('Richness', flavor.richness || 0)}
        </div>

        ${journey.length ? `
          <p class="dish-detail__section-title">Flavor Journey</p>
          <div class="flavor-journey">
            ${journey.map((stage, i) => `<span class="flavor-journey__stage" style="animation-delay:${300 + i * 150}ms">${escapeHtml(stage)}</span>`).join('')}
          </div>
        ` : ''}

        ${d.pairing ? `<p class="dish-detail__pairing">Pairs well with: ${escapeHtml(d.pairing)}</p>` : ''}

        ${customization ? renderCustomizationForm(d, customization) : ''}

        <div class="dish-detail__actions">
          <button class="btn btn--primary" id="addToOrderBtn" data-dish-id="${d.id}" data-dish-name="${escapeHtml(d.name)}" data-dish-image="${escapeHtml(d.image)}" data-base-price="${d.price}">
            Add to Order${customization ? ` — Rs. ${d.price}` : ''}
          </button>
          ${window.APP_USER
            ? `<button class="btn btn--ghost" id="favoriteBtn" data-dish-id="${d.id}">♡ Add to Favorites</button>`
            : `<a class="btn btn--ghost" href="${window.APP_BASE || ''}/pages/login.php">Log in to save favorites</a>`
          }
        </div>
      </div>
    `;

    wireAddToOrder(d, customization);
    if (window.APP_USER) wireFavoriteButton(d.id);
  }

  async function wireFavoriteButton(dishId) {
    const btn = document.getElementById('favoriteBtn');
    if (!btn) return;
    const base = window.APP_BASE || '';

    btn.addEventListener('click', async () => {
      btn.disabled = true;
      try {
        const res = await fetch(`${base}/api/favorites.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ dish_id: dishId }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Request failed');
        btn.textContent = data.favorited ? '♥ Added to Favorites' : '♡ Add to Favorites';
      } catch (err) {
        console.error('[zaiqa] favorite toggle failed:', err);
      } finally {
        btn.disabled = false;
      }
    });
  }

  function renderCustomizationForm(dish, customization) {
    return `
      <p class="dish-detail__section-title">Build Yours</p>
      <div class="customize-form" id="customizeForm" data-dish-id="${dish.id}">
        ${customization.groups
          .map(
            (group) => `
          <div class="customize-group">
            <p class="customize-group__label">${escapeHtml(group.label)}</p>
            <div class="customize-group__options">
              ${group.options
                .map(
                  (opt, i) => `
                <label class="customize-option">
                  <input type="${group.type === 'multi' ? 'checkbox' : 'radio'}" name="${escapeHtml(group.id)}"
                    value="${escapeHtml(opt.id)}" data-price-delta="${opt.price_delta}"
                    ${group.type === 'single' && i === 0 ? 'checked' : ''}>
                  <span>${escapeHtml(opt.label)}${opt.price_delta > 0 ? ` (+Rs.${opt.price_delta})` : ''}</span>
                </label>`
                )
                .join('')}
            </div>
          </div>`
          )
          .join('')}
      </div>
    `;
  }

  function computeCustomizedPrice(basePrice, formEl) {
    let total = basePrice;
    const selections = {};
    formEl.querySelectorAll('input:checked').forEach((input) => {
      total += Number(input.dataset.priceDelta || 0);
      const groupName = input.name;
      if (input.type === 'checkbox') {
        selections[groupName] = selections[groupName] || [];
        selections[groupName].push(input.value);
      } else {
        selections[groupName] = input.value;
      }
    });
    return { total, selections };
  }

  function wireAddToOrder(dish, customization) {
    const addBtn = document.getElementById('addToOrderBtn');
    const formEl = document.getElementById('customizeForm');

    function refreshPriceLabel() {
      if (!customization) return;
      const { total } = computeCustomizedPrice(dish.price, formEl);
      addBtn.textContent = `Add to Order — Rs. ${total}`;
    }

    if (formEl) {
      formEl.addEventListener('change', refreshPriceLabel);
      refreshPriceLabel();
    }

    addBtn.addEventListener('click', () => {
      let unitPrice = dish.price;
      let selections = null;

      if (formEl) {
        const computed = computeCustomizedPrice(dish.price, formEl);
        unitPrice = computed.total;
        selections = computed.selections;
      }

      window.ZaiqaCart.add({
        dish_id: dish.id,
        name: dish.name,
        image: dish.image,
        unit_price: unitPrice,
        customization: selections,
        quantity: 1,
      });

      addBtn.textContent = 'Added ✓';
      addBtn.disabled = true;
      setTimeout(closeModal, 700);
    });
  }



  function closeModal() {
    modal.hidden = true;
    document.body.style.overflow = '';
  }

  modalClose.addEventListener('click', closeModal);
  modalBackdrop.addEventListener('click', closeModal);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.hidden) closeModal();
  });

  loadMenu();
})();
