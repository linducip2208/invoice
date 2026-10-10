# REDESIGN IMPLEMENTATION PLAN

## Arah (diputuskan dari audit, bukan asumsi)

Tabler 1.6.1 tetap (satu-satunya framework; Tailwind sudah dihapus).
Brand: violet gelap aksesibel (#6d4ae0, 5.67:1) + sidebar navy gelap —
profesional tanpa rebrand membingungkan. Tanpa library chart baru
(SVG inline data real). Tanpa framework baru.

## Dilaksanakan

1. Token + kontras AA (primary, badge -lt override, alert, footer, underline).
2. Sidebar IA + topbar (quick-create, bell unread, theme, workspace).
3. Landing rewrite 8 section (phase berikutnya).
4. Dashboard KPI + SVG tren/donat/aging + overdue + pending quotes.
5. Invoice detail timeline + delivery log (sudah ada: payments/attachments/email).
6. Superadmin layout terpisah (phase berikutnya).
7. i18n batch, responsive 11 viewport, axe suite, E2E, docs, gates.

## Bukan tujuan

Mengganti kontrak bisnis, menambah integrasi pembayaran, atau mengarang
metrik. Semua angka dari query agregat teruji (DashboardTest).
