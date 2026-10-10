# NAVIGATION AUDIT (2026-10-10)

Sumber: `components/tabler/layout.blade.php` + SIDEBAR-ROUTE-AUDIT.md (44/44 PASS)
+ NavIntegrityTest (route-ref terdaftar, guest/superadmin guards, no dead links).

## Temuan & status

- Duplikat menu: tidak ada (terakhir: Tax Summary/Tax e-Faktur sudah satu rute).
- Dead links: tidak ada di shell (`href="#"` hanya pada toggle dropdown Bootstrap,
  dikecualikan eksplisit oleh test).
- Active state: `routeIs()` per grup; parent section selalu terlihat (flat + heading).
- Label: semua via `__()`; ID 649 kunci, EN 1887 (identity, lengkap by construction).
- Mobile: offcanvas via toggler + Escape (E2E); switcher + quick-create tampil di 390px.
- Permission: section SUPERADMIN hanya superadmin; plan-gating di backend
  (sidebar tidak menyembunyikan by plan — by design, guard di server).
- Kekurangan dicatat: tidak ada breadcrumb terpisah (pretitle menjalankan peran);
  pencarian global tidak ada (tidak diklaim ada).

## Keputusan IA (misi redesign)

Struktur 8 grup dipertahankan (semua menu memetakan ke route nyata yang teruji);
pengelompokan ulang agresif hanya akan menyembunyikan fitur aktif.
