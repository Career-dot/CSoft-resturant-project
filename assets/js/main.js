/**
 * ذائقہ (ZAIQA) — global site behavior
 * Navbar scroll state, mobile nav toggle, and the Secret Menu
 * logo-click discovery mechanic. Kept dependency-free since it
 * runs on every page.
 */
(function () {
  const nav = document.getElementById('siteNav');
  const toggle = document.getElementById('navToggle');
  const links = document.getElementById('siteNavLinks');

  if (nav) {
    const onScroll = () => {
      nav.classList.toggle('is-scrolled', window.scrollY > 40);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  if (toggle && links) {
    toggle.addEventListener('click', () => {
      const isOpen = links.classList.toggle('is-open');
      nav.classList.toggle('is-open', isOpen);
      toggle.setAttribute('aria-expanded', String(isOpen));
    });

    links.querySelectorAll('a').forEach((a) => {
      a.addEventListener('click', () => {
        links.classList.remove('is-open');
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  // Secret Menu discovery: 5 clicks on the logo within 3 seconds
  // unlocks "The Hidden Table". An Easter egg, not a security
  // boundary — the page itself is always reachable directly.
  const logo = document.querySelector('.site-nav__logo');
  if (logo) {
    let clickCount = 0;
    let resetTimer = null;

    logo.addEventListener('click', (e) => {
      clickCount++;
      clearTimeout(resetTimer);
      resetTimer = setTimeout(() => (clickCount = 0), 3000);

      if (clickCount >= 5) {
        e.preventDefault();
        clickCount = 0;
        const base = window.APP_BASE || '';
        window.location.href = `${base}/pages/secret-menu.php`;
      }
    });
  }
})();
