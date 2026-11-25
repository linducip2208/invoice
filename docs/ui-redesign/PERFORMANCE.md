# Performance

- Vite build ~0.3–1.5s. CSS 646KB (gzip 81KB), JS 228KB (gzip 72KB).
- Tanpa framework berat, tanpa CDN wajib. Font Inter via Google Fonts progresif + fallback sistem.
- Chart SVG murni (dashboard + superadmin) tanpa Chart.js.
- DB: aggregate + paginasi + eager load; N+1 dihindari di dashboard/admin.
