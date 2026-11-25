# FASE 9 — Batch lanjutan (continuous execution)

## G1. Kwitansi pembayaran cetak [x] DONE
- `GET payments/{payment}/receipt`: kop workspace + detail terima/untuk/tanggal/metode + tanda tangan; tombol Print/PDF. Tombol "Kwitansi" di daftar pembayaran invoice. Isolasi workspace (scope → 404).

## G2. Panduan bundel & dunning [x] DONE
- `docs/ui-redesign/GUIDES.md`: pola paket/bundel + DP/pelunasan + playbook dunning tanpa perubahan skema.

## Tes
- `tests/Feature/Phase9Test.php`: 2 passed (receipt + isolasi + regresi ukuran ikon `ti`). Full suite: 179 passed. Build sukses.

## Fix susulan: ukuran ikon SVG
- `components/ti.blade.php`: class `icon` selalu dipaksa menempel (merge, bukan replace); width/height 24 dipertahankan; `aria-hidden`.
- `tabler-branding.css`: guard `svg.icon`/`svg.chev`/ikon di button & list.
