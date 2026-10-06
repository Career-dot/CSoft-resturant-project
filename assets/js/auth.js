/**
 * Handles both #loginForm and #registerForm — whichever is present
 * on the page. Posts to /api/login.php or /api/register.php.
 */
(function () {
  const base = window.APP_BASE || '';

  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');

  function clearFieldErrors(form) {
    form.querySelectorAll('.field-error').forEach((el) => (el.textContent = ''));
    const generic = form.querySelector('.auth-form__error');
    if (generic) generic.textContent = '';
  }

  function goToMyTaste() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = params.get('return') || `${base}/pages/my-taste.php`;
  }

  if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      clearFieldErrors(loginForm);
      const fd = new FormData(loginForm);
      const btn = document.getElementById('loginBtn');
      btn.disabled = true;
      btn.textContent = 'Logging in…';

      try {
        const res = await fetch(`${base}/api/login.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: fd.get('email'), password: fd.get('password') }),
        });
        const data = await res.json();
        if (!res.ok) {
          document.getElementById('loginError').textContent =
            (data.error || 'Something went wrong.') + (data.debug ? ` (${data.debug})` : '');
          btn.disabled = false;
          btn.textContent = 'Log In';
          return;
        }
        goToMyTaste();
      } catch (err) {
        document.getElementById('loginError').textContent = 'Couldn\u2019t reach the server — please try again.';
        console.error('[zaiqa] login failed:', err);
        btn.disabled = false;
        btn.textContent = 'Log In';
      }
    });
  }

  if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      clearFieldErrors(registerForm);
      const fd = new FormData(registerForm);
      const btn = document.getElementById('registerBtn');
      btn.disabled = true;
      btn.textContent = 'Creating account…';

      try {
        const res = await fetch(`${base}/api/register.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            name: fd.get('name'),
            email: fd.get('email'),
            phone: fd.get('phone'),
            password: fd.get('password'),
            confirm_password: fd.get('confirm_password'),
          }),
        });
        const data = await res.json();
        if (!res.ok) {
          if (data.errors) {
            Object.entries(data.errors).forEach(([field, msg]) => {
              const el = registerForm.querySelector(`[data-error-for="${field}"]`);
              if (el) el.textContent = msg;
            });
          } else {
            alert((data.error || 'Something went wrong.') + (data.debug ? `\n\n(${data.debug})` : ''));
          }
          btn.disabled = false;
          btn.textContent = 'Create Account';
          return;
        }
        goToMyTaste();
      } catch (err) {
        alert('Couldn\u2019t reach the server — please try again.');
        console.error('[zaiqa] register failed:', err);
        btn.disabled = false;
        btn.textContent = 'Create Account';
      }
    });
  }
})();
