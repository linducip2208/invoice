# Accessibility

- Target WCAG 2.2 AA praktis. Skip-link, h1 tunggal, breadcrumb nav, aria-label search/toggle/menu, focus-visible 2px, kontras badge/alert diperbaiki (hijau #18702c, kuning #7a4d00, merah #a11a0e, biru #175a9e), touch target mobile ≥2.5rem, reduced-motion untuk skeleton, tabel dengan th, form error via @error + aria.
- Uji: `tests-e2e/a11y.spec.js` tersedia; axe manual disarankan sebelum rilis.
