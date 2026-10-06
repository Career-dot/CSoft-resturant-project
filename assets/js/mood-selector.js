/**
 * "How are you feeling today?" mood selector.
 * Taps a mood chip -> calls /api/recommendations.php -> renders
 * dishes inline, no page reload.
 */
(function () {
  const grid = document.getElementById('moodGrid');
  const resultsWrap = document.getElementById('moodResults');
  const resultsLabel = document.getElementById('moodResultsLabel');
  const resultsGrid = document.getElementById('moodResultsGrid');

  if (!grid) return;

  const MOOD_LABELS = {
    comfort: 'Comfort',
    spicy: 'Spicy',
    fancy: 'Fancy',
    fresh: 'Fresh',
    sweet: 'Sweet',
    late_night: 'Late Night Craving',
  };

  async function loadMood(mood) {
    resultsLabel.textContent = '';
    resultsLabel.classList.remove('is-visible');
    resultsGrid.innerHTML = '<p class="mood-results__loading">Finding dishes that match your mood…</p>';

    try {
      const res = await fetch(`${window.APP_BASE}/api/recommendations.php?mood=${encodeURIComponent(mood)}`, {
        headers: { Accept: 'application/json' },
      });
      if (!res.ok) throw new Error(`Request failed: ${res.status}`);
      const data = await res.json();
      renderResults(mood, data.dishes || []);
    } catch (err) {
      resultsGrid.innerHTML = '<p class="mood-results__empty">Couldn\u2019t load recommendations right now. Please try again.</p>';
      console.error('[zaiqa] mood fetch failed:', err);
    }
  }

  function renderResults(mood, dishes) {
    resultsLabel.textContent = `For your ${MOOD_LABELS[mood] || mood} mood`;
    resultsLabel.classList.add('is-visible');

    if (!dishes.length) {
      resultsGrid.innerHTML = '<p class="mood-results__empty">No dishes matched yet — check back soon.</p>';
      return;
    }

    resultsGrid.innerHTML = dishes
      .map(
        (d, i) => `
        <article class="dish-card" style="animation-delay:${i * 80}ms">
          <img class="dish-card__image" src="${escapeAttr(d.image)}" alt="${escapeAttr(d.name)}" loading="lazy">
          <div class="dish-card__body">
            <h3 class="dish-card__name">${escapeHtml(d.name)}</h3>
            <p class="dish-card__desc">${escapeHtml(d.description)}</p>
            <span class="dish-card__price">Rs. ${escapeHtml(String(d.price))}</span>
          </div>
        </article>`
      )
      .join('');
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }
  function escapeAttr(str) {
    return escapeHtml(str).replace(/"/g, '&quot;');
  }

  grid.addEventListener('click', (e) => {
    const chip = e.target.closest('.mood-chip');
    if (!chip) return;

    grid.querySelectorAll('.mood-chip').forEach((c) => c.setAttribute('aria-pressed', 'false'));
    chip.setAttribute('aria-pressed', 'true');
    resultsWrap.hidden = false;

    loadMood(chip.dataset.mood);
  });
})();
