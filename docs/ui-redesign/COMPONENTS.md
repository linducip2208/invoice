# Components / A11y / Performance / QA

## COMPONENTS
- Stat, status-badge (kontras AA fix), empty, alert, field, delete-button, guest/public/layout/admin-layout, ti icons (Tabler). Tanpa emoji sebagai ikon utama.

## ACCESSIBILITY (WCAG 2.2 AA praktis)
- Skip-link, h1 per halaman, aria-label search/nav, focus-visible 2px, badge/alert kontras diperbaiki (#18702c, #7a4d00, #a11a0e, #175a9e), tabel pakai th/scope implisit, reduced-motion untuk skeleton/shimmer, touch target ≥2.5rem mobile.

## PERFORMANCE
- Vite build 1.55s, CSS 646KB (gzip 81KB), JS 228KB (gzip 72KB). Tanpa framework berat baru, tanpa CDN wajib (font Google bersifat progresif + fallback sistem). Query: 1 aggregate dashboard + bucketing PHP, paginasi 15–25, eager load client/plan/workspace. Chart SVG murni (tanpa Chart.js) agar render cepat.

## QA-RESULTS
- `php artisan test`: 144 lolos (513 assertions) + `RedesignTest` 9 lolos (40 assertions) = 153 total.
- `npm run build`: sukses.
- Playwright e2e: infra tersedia (v1.64.0), screenshot manual via serve :8111 (lihat SCREENSHOT-INDEX).
- Keterbatasan: screenshot superadmin butuh login manual; churn hanya tampil bila data 30d cukup; gateway butuh kredensial provider (status Partial di docs).
