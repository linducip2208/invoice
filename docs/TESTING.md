# TESTING

## Menjalankan

```bash
cp .env.example .env
php artisan key:generate
php artisan test            # sqlite :memory:, 48 tests
php artisan test --compact
npm run build               # validasi aset
composer validate           # validasi manifest
```

`phpunit.xml` memaksa isolasi penuh (sqlite memory, array cache/session/mail,
sync queue) — tidak butuh kredensial eksternal. `Http::fake()` dipakai di
BlogTest agar IndexNow tidak menyentuh jaringan.

## Konvensi

- Setiap test butuh workspace: buat user + workspace + attach + (opsional)
  `giveProPlan()` dari `tests/TestCase.php` (plan-gated routes butuh subscription aktif).
- `seedPlans()` wajib sebelum subscription (free plan auto-create abort 500 bila tak ada).
- Assertion tampilan harus locale-aware: `assertSee(__('Clients'))`, bukan string Inggris.
- Isolasi workspace: terima 403 atau 404 (terdokumentasi di tiap test).
- Jangan kembalikan `ExampleTest` placeholder; tiap test harus punya assertion bermakna.

## Cakupan (48)

Client 6 · Invoice 8 · Payment 5 · Blog 8 · Pseo 10 · Workspace 4 ·
Webhook 5 · EfakturXml 2. Belum: auth/2FA, jobs, mail dispatch, plan-limit
negatif, import gagal, gateway sandbox, E2E browser.
