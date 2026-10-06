/**
 * Reservation wizard: Vibe -> Table -> Details -> Confirmed.
 * Step 1 & 2 call /api/tables.php, Step 3 submits to /api/reserve.php.
 */
(function () {
  const tracker = document.getElementById('wizardTracker');
  const stepEls = Array.from(document.querySelectorAll('.wizard-step'));
  const vibeGrid = document.getElementById('vibeGrid');
  const tableMap = document.getElementById('tableMap');
  const tableMapSubtitle = document.getElementById('tableMapSubtitle');
  const toDetailsBtn = document.getElementById('toDetails');
  const backToVibeBtn = document.getElementById('backToVibe');
  const backToTableBtn = document.getElementById('backToTable');
  const form = document.getElementById('reservationForm');
  const summaryBox = document.getElementById('reserveSummary');
  const confirmationBox = document.getElementById('reserveConfirmation');

  if (!vibeGrid) return;

  const state = { seatingAreas: [], selectedArea: null, selectedTable: null };

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }

  function goToStep(n) {
    stepEls.forEach((el) => (el.hidden = Number(el.dataset.step) !== n));
    tracker.querySelectorAll('li').forEach((li) => {
      const s = Number(li.dataset.step);
      li.classList.toggle('is-active', s === n);
      li.classList.toggle('is-done', s < n);
    });
  }

  /* ---------- Step 1: Vibe selector ---------- */

  async function loadVibes() {
    try {
      const res = await fetch(`${window.APP_BASE}/api/tables.php`, { headers: { Accept: 'application/json' } });
      if (!res.ok) throw new Error(`Request failed: ${res.status}`);
      const data = await res.json();
      state.seatingAreas = data.seating_areas || [];
      renderVibes();
    } catch (err) {
      vibeGrid.innerHTML = '<p class="wizard-step__loading">Couldn\u2019t load seating options. Please try again.</p>';
      console.error('[zaiqa] tables fetch failed:', err);
    }
  }

  function renderVibes() {
    vibeGrid.innerHTML = state.seatingAreas
      .map(
        (a) => `
      <button class="vibe-card" data-slug="${escapeHtml(a.slug)}">
        <img class="vibe-card__image" src="${escapeHtml(a.image)}" alt="${escapeHtml(a.name)}" loading="lazy">
        <div class="vibe-card__body">
          <p class="vibe-card__name">${a.emoji || ''} ${escapeHtml(a.name)}</p>
          <p class="vibe-card__desc">${escapeHtml(a.description)}</p>
          <p class="vibe-card__meta">${(a.occasions || []).map(escapeHtml).join(' · ')}</p>
        </div>
      </button>`
      )
      .join('');
  }

  vibeGrid.addEventListener('click', (e) => {
    const card = e.target.closest('.vibe-card');
    if (!card) return;
    const area = state.seatingAreas.find((a) => a.slug === card.dataset.slug);
    state.selectedArea = area;
    state.selectedTable = null;
    loadTables(area.slug);
    goToStep(2);
  });

  /* ---------- Step 2: Table map ---------- */

  async function loadTables(slug) {
    tableMap.innerHTML = '<p class="wizard-step__loading">Loading tables…</p>';
    tableMapSubtitle.textContent = `${state.selectedArea.emoji || ''} ${state.selectedArea.name} — pick an available table`;
    toDetailsBtn.disabled = true;

    try {
      const res = await fetch(`${window.APP_BASE}/api/tables.php?seating_area=${encodeURIComponent(slug)}`, { headers: { Accept: 'application/json' } });
      if (!res.ok) throw new Error(`Request failed: ${res.status}`);
      const data = await res.json();
      renderTables(data.tables || []);
    } catch (err) {
      tableMap.innerHTML = '<p class="wizard-step__loading">Couldn\u2019t load the table map. Please try again.</p>';
      console.error('[zaiqa] table map fetch failed:', err);
    }
  }

  function renderTables(tables) {
    tableMap.innerHTML = tables
      .map(
        (t) => `
      <button class="table-tile table-tile--${t.status}" data-id="${t.id}" data-number="${escapeHtml(t.table_number)}" data-capacity="${t.capacity}"
        ${t.status !== 'available' ? 'disabled' : ''}>
        <span>${escapeHtml(t.table_number)}</span>
        <span class="table-tile__cap">seats ${t.capacity}</span>
      </button>`
      )
      .join('');
  }

  tableMap.addEventListener('click', (e) => {
    const tile = e.target.closest('.table-tile');
    if (!tile || tile.disabled) return;

    tableMap.querySelectorAll('.table-tile').forEach((t) => {
      if (!t.classList.contains('table-tile--reserved') && !t.classList.contains('table-tile--unavailable')) {
        t.classList.remove('table-tile--selected');
        t.classList.add('table-tile--available');
      }
    });
    tile.classList.remove('table-tile--available');
    tile.classList.add('table-tile--selected');

    state.selectedTable = { id: tile.dataset.id, number: tile.dataset.number, capacity: tile.dataset.capacity };
    toDetailsBtn.disabled = false;
  });

  backToVibeBtn.addEventListener('click', () => goToStep(1));

  toDetailsBtn.addEventListener('click', () => {
    renderSummary();
    goToStep(3);
  });

  /* ---------- Step 3: Details ---------- */

  function renderSummary() {
    if (!state.selectedArea || !state.selectedTable) return;
    summaryBox.innerHTML = `Reserving <strong>${escapeHtml(state.selectedTable.number)}</strong>
      (seats ${escapeHtml(state.selectedTable.capacity)}) in <strong>${escapeHtml(state.selectedArea.name)}</strong>.`;
  }

  backToTableBtn.addEventListener('click', () => goToStep(2));

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearFieldErrors();

    const fd = new FormData(form);
    const payload = {
      name: fd.get('name'),
      email: fd.get('email'),
      phone: fd.get('phone'),
      date: fd.get('date'),
      time: fd.get('time'),
      guests: fd.get('guests'),
      special_request: fd.get('special_request'),
      seating_area_slug: state.selectedArea ? state.selectedArea.slug : null,
      table_id: state.selectedTable ? state.selectedTable.id : null,
    };

    const submitBtn = document.getElementById('submitReservation');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Confirming…';

    try {
      const res = await fetch(`${window.APP_BASE}/api/reserve.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      const data = await res.json();

      if (!res.ok) {
        showFieldErrors(data.errors || {});
        submitBtn.disabled = false;
        submitBtn.textContent = 'Confirm Reservation';
        return;
      }

      renderConfirmation(data);
      goToStep(4);
    } catch (err) {
      alert('Couldn\u2019t submit your reservation right now — please try again.');
      console.error('[zaiqa] reservation submit failed:', err);
      submitBtn.disabled = false;
      submitBtn.textContent = 'Confirm Reservation';
    }
  });

  function clearFieldErrors() {
    document.querySelectorAll('.field-error').forEach((el) => (el.textContent = ''));
  }
  function showFieldErrors(errors) {
    Object.entries(errors).forEach(([field, msg]) => {
      const el = document.querySelector(`[data-error-for="${field}"]`);
      if (el) el.textContent = msg;
    });
  }

  /* ---------- Step 4: Confirmation ---------- */

  function renderConfirmation(data) {
    confirmationBox.innerHTML = `
      <p class="reserve-confirmation__icon">🎉</p>
      <h2 class="reserve-confirmation__title">You're booked, ${escapeHtml(data.name)}.</h2>
      <p>A Taste Worth Remembering is waiting for you.</p>
      <div class="reserve-confirmation__ref">${escapeHtml(data.reference_code)}</div>
      <div class="reserve-confirmation__details">
        <p><span>Table</span><span>${escapeHtml(state.selectedTable.number)} · ${escapeHtml(state.selectedArea.name)}</span></p>
        <p><span>Date</span><span>${escapeHtml(data.date)}</span></p>
        <p><span>Time</span><span>${escapeHtml(data.time)}</span></p>
        <p><span>Guests</span><span>${escapeHtml(String(data.guests))}</span></p>
      </div>
      ${!data.persisted ? '<p class="reserve-confirmation__note">Note: the database isn\u2019t connected yet in this environment, so this confirmation was generated but not saved. Connect MySQL to persist reservations.</p>' : ''}
      <a href="${window.APP_BASE}/" class="btn btn--primary">Back to Home</a>
    `;
  }

  loadVibes();
})();
