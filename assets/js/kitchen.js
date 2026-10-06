/**
 * Loads the simulated kitchen status snapshot and wires the
 * click-to-reveal Ask the Chef FAQ.
 */
(function () {
  const base = window.APP_BASE || '';
  const statusGrid = document.getElementById('kitchenStatusGrid');

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }

  if (statusGrid) {
    fetch(`${base}/api/kitchen-status.php`, { headers: { Accept: 'application/json' } })
      .then((res) => res.json())
      .then((data) => {
        statusGrid.innerHTML = (data.activity || [])
          .map(
            (a) => `
          <div class="kitchen-status-item">
            <div class="kitchen-status-item__emoji">${a.emoji}</div>
            <div class="kitchen-status-item__count">${a.count}</div>
            <div class="kitchen-status-item__label">${escapeHtml(a.label)}</div>
          </div>`
          )
          .join('');
      })
      .catch((err) => {
        statusGrid.innerHTML = '<p class="panel-loading">Couldn\u2019t check the kitchen right now.</p>';
        console.error('[zaiqa] kitchen status fetch failed:', err);
      });
  }

  const faqList = document.getElementById('faqList');
  const faqAnswer = document.getElementById('faqAnswer');
  if (faqList) {
    faqList.addEventListener('click', (e) => {
      const btn = e.target.closest('.faq-item');
      if (!btn) return;
      faqList.querySelectorAll('.faq-item').forEach((b) => b.classList.remove('is-active'));
      btn.classList.add('is-active');
      faqAnswer.hidden = false;
      faqAnswer.textContent = btn.dataset.answer;
    });
  }
})();
