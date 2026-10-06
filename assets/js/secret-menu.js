/**
 * Renders the secret dishes fetched from /api/secret-menu.php.
 */
(function () {
  const grid = document.getElementById('secretGrid');
  if (!grid) return;
  const base = window.APP_BASE || '';

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }

  fetch(`${base}/api/secret-menu.php`, { headers: { Accept: 'application/json' } })
    .then((res) => res.json())
    .then((data) => {
      const dishes = data.dishes || [];
      if (!dishes.length) {
        grid.innerHTML = '<p class="panel-loading">Nothing hidden right now — check back another night.</p>';
        return;
      }
      grid.innerHTML = dishes
        .map(
          (d) => `
        <article class="secret-card">
          <img class="secret-card__image" src="${escapeHtml(d.image)}" alt="${escapeHtml(d.name)}" loading="lazy">
          <div class="secret-card__body">
            <p class="secret-card__category">${escapeHtml(d.category)}</p>
            <h3 class="secret-card__name">${escapeHtml(d.name)}</h3>
            <p class="secret-card__desc">${escapeHtml(d.description)}</p>
            <span class="secret-card__price">Rs. ${escapeHtml(String(d.price))}</span>
          </div>
        </article>`
        )
        .join('');
    })
    .catch((err) => {
      grid.innerHTML = '<p class="panel-loading">Couldn\u2019t reach the hidden table right now.</p>';
      console.error('[zaiqa] secret menu fetch failed:', err);
    });
})();
