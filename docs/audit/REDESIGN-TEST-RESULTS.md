# REDESIGN TEST RESULTS (2026-10-16, ronde 4 — rerun penuh serial)

- phpunit: **144/144 passed** (513 assertions).
- Playwright `--workers=1`: **84/84 passed** — app 14 + quote 12 +
  responsive 30 + a11y 18 + zoom 10, NOL console error, overflow 0 di
  11 viewport (320→1920) + 4 lebar zoom-200 (180/195/512/683).
- Temuan ronde 4 (diperbaiki, lalu hijau):
  - axe `color-contrast` serius di halaman authed baru: tombol
    `btn-outline-warning` (/clients) dan `btn-outline-success` (/quotes) →
    override global AA di `tabler-branding.css` (teks gelap + hover fill gelap).
  - axe `label` serius di /workspace/settings: dua color picker tanpa label
    → `aria-label`.
  - zoom 180px: tombol header "Bandingkan semua fitur" nowrap →
    `.page-header .btn` wrap di ≤400px.
  - Diadopsi dari WIP: display fluid `clamp()`, `btn-group`/datagrid/
    `card-actions` wrap, badge hero wrap, login demo jadi definition list,
    metodologi zoom jujur (setengah-viewport, bukan properti CSS `zoom`).
- Pelajaran proses: E2E wajib serial di lingkungan ini — run paralel
  default flaky (server PHP + SQLite tunggal dibagi worker browser);
  reseed tetap stop-server + hapus file + `migrate:fresh --seeder=E2eSeeder`.
- Temuan ronde 2 (diperbaiki, lalu hijau):
  - Landing rewrite menyebabkan overflow horizontal 4–23px di ≤768px:
    `row g-4`/`row-deck` di seksi capabilities/FAQ (`welcome.blade.php`).
    Penyebab: `row-deck > [class*=col-]` memaksa kolom jadi flex
    (`min-width:auto`) sehingga konten editorial menahan shrink.
    Perbaikan: kembalikan ke `row` biasa (gutter default menyeimbangi
    padding container) — terverifikasi 0 overflow di 320→1920 + zoom 200%.
  - Angka metrik hero (`61%`, `4`, `8`) hitam di atas navy (class `.h2`
    bukan elemen `h2`, lolos dari rule CSS) — diperbaiki dengan `text-white`.
  - Badge `POPULAR` (ribbon-top) terpotong di kartu pricing — diganti badge
    inline di card body (`pricing/index.blade.php`).
  - Baris Pajak/Diskon nol tetap tampil di preview invoice: `x-show`
    Alpine kalah vs Bootstrap `.d-flex !important`. Perbaikan: `d-flex`
    kondisional (`:class`) di `invoices/create` + `invoices/edit` +
    `invoice-layouts/builder` (pola `d-grid` sama).
- `npm run build` sukses; `composer validate` valid; `route:list` 310.
- Zoom 200% (viewport 683px @2x): overflow 0 + 0 pageerror di
  landing/dashboard/invoice-create.
- Visual: 8 screenshot baru direview manual (landing-pricing-dashboard-
  invoice-create desktop+mobile, quote publik): hero kuat + preview
  dominan, dashboard profesional, form 3-langkah + sticky preview OK.
- Pelajaran proses: suite quote-approval stateful — JANGAN rerun tanpa
  reseed (flow2/flow6 gagal karena fixture termutasi run sebelumnya);
  reseed E2E harus stop-server + hapus file (`migrate:fresh` saat server
  jalan tidak andal di SQLite Windows); throttle Laravel berbagi counter
  per IP → dipakai named limiters.
