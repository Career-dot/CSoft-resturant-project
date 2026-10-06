/**
 * My Taste page: fetches the saved taste profile, food passport
 * progress, and favorites for the logged-in user (all three APIs
 * require login — this page already redirected server-side if
 * the person isn't authenticated, so 401s here would only happen
 * if the session expired mid-visit).
 */
(function () {
  const base = window.APP_BASE || '';

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }

  async function loadJSON(path) {
    const res = await fetch(`${base}${path}`, { headers: { Accept: 'application/json' } });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || `Request failed: ${res.status}`);
    return data;
  }

  /* ---------- Taste personality ---------- */

  async function loadTaste() {
    const box = document.getElementById('tasteContent');
    try {
      const data = await loadJSON('/api/taste.php');
      if (!data.profile) {
        box.innerHTML = `
          <p class="panel-empty">You haven\u2019t taken the taste quiz yet.</p>
          <a href="${base}/pages/discover.php#taste-quiz" class="btn btn--primary" style="margin-top:0.75rem">Discover Your Taste</a>
        `;
        return;
      }
      const p = data.profile;
      box.innerHTML = `
        <p class="taste-summary__name">${escapeHtml(p.personality_name)}</p>
        <p class="taste-summary__desc">${escapeHtml(p.description)}</p>
        <div class="taste-summary__meta">
          <span><strong>Spice:</strong> ${escapeHtml(p.spice_pref)}</span>
          <span><strong>Drink:</strong> ${escapeHtml(p.drink || '')}</span>
        </div>
      `;
    } catch (err) {
      box.innerHTML = '<p class="panel-empty">Couldn\u2019t load your taste profile.</p>';
      console.error('[zaiqa] my-taste: taste load failed:', err);
    }
  }

  /* ---------- Food Passport ---------- */

  async function loadPassport() {
    const box = document.getElementById('passportContent');
    try {
      const data = await loadJSON('/api/food-passport.php');
      const pct = data.total_dishes > 0 ? Math.round((data.tried_count / data.total_dishes) * 100) : 0;

      box.innerHTML = `
        <span class="passport-badge">${escapeHtml(data.badge.name)}</span>
        <p class="panel-empty" style="margin-bottom:0.5rem">${escapeHtml(data.badge.description)}</p>
        <div class="passport-progress">
          <div class="passport-progress__bar-wrap"><div class="passport-progress__bar" style="width:${pct}%"></div></div>
          <p class="panel-empty">${data.tried_count} of ${data.total_dishes} dishes explored</p>
        </div>
        ${data.tried.length ? `<div class="passport-dishes">${data.tried.map((d) => `
          <span class="passport-dish"><img src="${escapeHtml(d.image)}" alt="${escapeHtml(d.name)}" loading="lazy">${escapeHtml(d.name)}</span>
        `).join('')}</div>` : '<p class="panel-empty">Order something from the menu to start your passport.</p>'}
      `;
    } catch (err) {
      box.innerHTML = '<p class="panel-empty">Couldn\u2019t load your Food Passport.</p>';
      console.error('[zaiqa] my-taste: passport load failed:', err);
    }
  }

  /* ---------- Favorites ---------- */

  async function loadFavorites() {
    const box = document.getElementById('favoritesContent');
    try {
      const data = await loadJSON('/api/favorites.php');
      if (!data.favorites.length) {
        box.innerHTML = `
          <p class="panel-empty">No favorites yet — tap the heart on any dish in the menu.</p>
          <a href="${base}/pages/menu.php" class="btn btn--primary" style="margin-top:0.75rem">Browse the Menu</a>
        `;
        return;
      }
      box.innerHTML = `<div class="favorites-grid">${data.favorites.map((d) => `
        <div class="favorite-card">
          <img class="favorite-card__image" src="${escapeHtml(d.image)}" alt="${escapeHtml(d.name)}" loading="lazy">
          <div class="favorite-card__body">
            <p class="favorite-card__name">${escapeHtml(d.name)}</p>
            <p class="favorite-card__price">Rs. ${escapeHtml(String(d.price))}</p>
          </div>
        </div>
      `).join('')}</div>`;
    } catch (err) {
      box.innerHTML = '<p class="panel-empty">Couldn\u2019t load your favorites.</p>';
      console.error('[zaiqa] my-taste: favorites load failed:', err);
    }
  }

  /* ---------- Logout ---------- */

  document.getElementById('logoutBtn').addEventListener('click', async () => {
    try {
      await fetch(`${base}/api/logout.php`, { method: 'POST' });
    } catch (err) {
      console.error('[zaiqa] logout failed:', err);
    }
    window.location.href = `${base}/`;
  });

  loadTaste();
  loadPassport();
  loadFavorites();
})();
