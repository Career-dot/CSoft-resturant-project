/**
 * Contact form: posts to /api/contact.php, shows field errors or a
 * confirmation message.
 */
(function () {
  const form = document.getElementById('contactForm');
  if (!form) return;
  const base = window.APP_BASE || '';
  const statusEl = document.getElementById('contactStatus');

  function clearFieldErrors() {
    form.querySelectorAll('.field-error').forEach((el) => (el.textContent = ''));
    statusEl.textContent = '';
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearFieldErrors();
    const fd = new FormData(form);
    const btn = document.getElementById('contactSubmitBtn');
    btn.disabled = true;
    btn.textContent = 'Sending…';

    try {
      const res = await fetch(`${base}/api/contact.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          name: fd.get('name'),
          email: fd.get('email'),
          message: fd.get('message'),
        }),
      });
      const data = await res.json();

      if (!res.ok) {
        Object.entries(data.errors || {}).forEach(([field, msg]) => {
          const el = form.querySelector(`[data-error-for="${field}"]`);
          if (el) el.textContent = msg;
        });
        btn.disabled = false;
        btn.textContent = 'Send Message';
        return;
      }

      form.reset();
      statusEl.textContent = data.persisted
        ? 'Thanks — your message has been sent. We\u2019ll get back to you soon.'
        : 'Message received (note: the database isn\u2019t connected yet in this environment, so it wasn\u2019t saved).';
      btn.disabled = false;
      btn.textContent = 'Send Message';
    } catch (err) {
      statusEl.textContent = 'Couldn\u2019t send your message right now — please try again.';
      console.error('[zaiqa] contact submit failed:', err);
      btn.disabled = false;
      btn.textContent = 'Send Message';
    }
  });
})();
