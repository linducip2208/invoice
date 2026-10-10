# TABLER MIGRATION REPORT

- Versi: `@tabler/core@1.6.1` + `@tabler/icons@3.49.0` (pin exact, npm/Vite,
  tanpa CDN). Bootstrap 5.3 via dependency Tabler — satu-satunya framework CSS.
- Tailwind 3 + configs + 919-line unicorn CSS + 18 komponen lama + 4 layout
  lama + 12 stub views + StubController DIHAPUS. `git grep unicorn|tailwind`
  pada views/css/js = 0.
- Cakupan: 132/167 views MIGRATED (79%); sisanya EXEMPT yang sah: print/PDF
  templates, HTML email, feed/sitemap XML, widget JS, partials/fragments,
  dan komponen Tabler itu sendiri. Tidak ada halaman aplikasi aktif ber-layout lama.
- Ikon: 74 SVG outline lokal via `<x-ti>` (tanpa webfont); nol emoji dekoratif
  sebagai ikon; nol SVG inline.
- Tidak ada duplikasi Bootstrap/runtime. Alpine dipertahankan untuk interaksi
  (dropdown/sidebar/modal/builder) — komponen visual 100% Tabler.
- Brand hanya token: `--tblr-primary` ungu + dark mode `data-bs-theme` +
  toggle persisten (localStorage, hanya nama tema).
- Kontras/AA: badge + tombol Tabler default; focus-visible global; label +
  aria pada form/menu; keyboard: offcanvas via toggler + Escape (E2E).
- Fonts: system stack (tanpa fetch eksternal); opsi font branding yang mati
  dihapus dari form + validasi.
