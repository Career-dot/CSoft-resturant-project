/**
 * Order tracking: looks up a reference code via
 * /api/order-status.php and polls every 5s while the order is
 * still in progress. Auto-fills and tracks immediately if the
 * page was opened with ?ref=ZO-XXXXXX (e.g. from the checkout
 * confirmation).
 */
(function () {
  const base = window.APP_BASE || '';
  const refInput = document.getElementById('refInput');
  const trackBtn = document.getElementById('trackBtn');
  const errorEl = document.getElementById('trackingError');
  const resultEl = document.getElementById('trackingResult');
  const stepperEl = document.getElementById('trackingStepper');
  const noteEl = document.getElementById('trackingNote');
  const metaEl = document.getElementById('trackingMeta');
  const heading = document.getElementById('trackingHeading');

  if (!refInput) return;

  const STAGE_META = {
    received: { icon: '📝', label: 'Received' },
    preparing: { icon: '👨‍🍳', label: 'Preparing' },
    cooking: { icon: '🔥', label: 'Cooking' },
    ready: { icon: '🍽️', label: 'Almost Ready' },
    served: { icon: '✅', label: 'Served' },
  };

  let pollTimer = null;

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }

  async function track(ref) {
    errorEl.textContent = '';
    try {
      const res = await fetch(`${base}/api/order-status.php?ref=${encodeURIComponent(ref)}`, { headers: { Accept: 'application/json' } });
      const data = await res.json();
      if (!res.ok) {
        errorEl.textContent = data.error || 'Order not found.';
        resultEl.hidden = true;
        clearInterval(pollTimer);
        return;
      }
      render(data);

      clearInterval(pollTimer);
      if (data.status !== 'served' && data.status !== 'cancelled') {
        pollTimer = setInterval(() => track(ref), 5000);
      }
    } catch (err) {
      errorEl.textContent = 'Couldn\u2019t reach the server — please try again.';
      console.error('[zaiqa] tracking fetch failed:', err);
    }
  }

  function render(data) {
    resultEl.hidden = false;
    heading.textContent = `Tracking ${data.reference_code}`;

    stepperEl.innerHTML = data.stages
      .map((stage, i) => {
        const meta = STAGE_META[stage] || { icon: '•', label: stage };
        const cls = i < data.stage_index ? 'is-done' : i === data.stage_index ? 'is-current' : '';
        return `
        <div class="tracking-step ${cls}">
          <div class="tracking-step__dot">${meta.icon}</div>
          <div class="tracking-step__label">${escapeHtml(meta.label)}</div>
        </div>`;
      })
      .join('');

    noteEl.textContent = data.simulated
      ? 'Status updates automatically as the kitchen works through your order.'
      : '';

    metaEl.innerHTML = `
      <p><span>Name</span><span>${escapeHtml(data.name)}</span></p>
      <p><span>Total</span><span>Rs. ${escapeHtml(String(data.total))}</span></p>
    `;
  }

  trackBtn.addEventListener('click', () => {
    const ref = refInput.value.trim().toUpperCase();
    if (ref) track(ref);
  });
  refInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') trackBtn.click();
  });

  const params = new URLSearchParams(window.location.search);
  const prefill = params.get('ref');
  if (prefill) {
    refInput.value = prefill.toUpperCase();
    track(prefill.toUpperCase());
  }
})();
