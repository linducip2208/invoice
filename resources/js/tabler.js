import './bootstrap';

import Alpine from 'alpinejs';
import '@tabler/core/dist/js/tabler.min.js';

window.Alpine = Alpine;
Alpine.start();

// Persisted light/dark theme (theme name only — never secrets).
(() => {
  const key = 'invocely-theme';
  const root = document.documentElement;
  const apply = (t) => {
    root.setAttribute('data-bs-theme', t === 'dark' ? 'dark' : 'light');
    document.querySelectorAll('[data-theme-label]').forEach((el) => {
      el.textContent = t === 'dark' ? el.dataset.themeLabelDark || 'Light' : el.dataset.themeLabelLight || 'Dark';
    });
  };
  let theme = 'light';
  try {
    theme = localStorage.getItem(key) || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  } catch (e) { /* private mode */ }
  apply(theme);
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-theme-toggle]');
    if (!btn) return;
    theme = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
    try { localStorage.setItem(key, theme); } catch (err) { /* ignore */ }
    apply(theme);
  });
})();

// Keyboard access to scrollable table regions (axe scrollable-region-focusable):
// any .table-responsive that actually overflows becomes focusable with a label.
function makeScrollableFocusable() {
  document.querySelectorAll('.table-responsive').forEach((el) => {
    if (el.scrollWidth > el.clientWidth + 1) {
      el.setAttribute('tabindex', '0');
      el.setAttribute('role', 'region');
      if (!el.getAttribute('aria-label')) el.setAttribute('aria-label', 'Scrollable table');
    } else {
      el.removeAttribute('tabindex');
      el.removeAttribute('role');
    }
  });
}
document.addEventListener('DOMContentLoaded', makeScrollableFocusable);
window.addEventListener('resize', makeScrollableFocusable);

// Prevent double-submit on financial forms (create invoice/payment/quote).
document.addEventListener('submit', (e) => {
  const form = e.target;
  if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-prevent-double-submit')) return;
  const btn = form.querySelector('[type="submit"]');
  if (form.dataset.submitted === '1') {
    e.preventDefault();
    return;
  }
  form.dataset.submitted = '1';
  if (btn) {
    btn.disabled = true;
    btn.classList.add('disabled');
  }
});
