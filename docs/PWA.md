# PWA

- Manifest: `public/manifest.webmanifest` (`id` + `start_url` `/`, `display`
  standalone, `orientation` any, ikon 192/512 + maskable di `public/icons/`).
- MIME: `.htaccess` memaksa `application/manifest+json` untuk `.webmanifest`.
- Service worker `public/sw.js` (v2): precache hanya aset statis publik;
  navigasi network-first + fallback `offline.html`; **`/api/` network-only
  (tidak pernah di-cache)**; hanya same-origin GET; `skipWaiting` + hapus cache lama.
- Transaksi keuangan butuh server — tidak ada antrean offline (by design).
- Setelah deploy: bump `CACHE` bila mengubah aset precache; verifikasi
  installability di Chrome (Application → Manifest) dan uji logout/ganti akun
  (tidak boleh ada data akun lama saat offline).
