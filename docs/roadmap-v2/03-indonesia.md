# Indonesia-Specific Features — Sprint 2

> Moat lokal — kompetitor luar (Wave, FreshBooks, QuickBooks) ga punya. Diferensiator besar untuk market PKP & B2B.

---

## 2.1 NPWP Validator ❌

**Status:** ❌ | **Priority:** ⭐ High | **Effort:** 0.5 hari

### Why
Saat user input data klien, NPWP-nya bisa salah ketik atau fake. Validator real-time menjaga data quality + auto-fill nama wajib pajak dari DJP.

### Database Schema
**Tabel BARU `npwp_validations`** (cache global, lihat 01-DATABASE-RELATIONS Section 22).

**Modifikasi `clients`:**
- Tambah `npwp_verified BOOLEAN DEFAULT FALSE`
- Tambah `npwp_verified_at TIMESTAMP NULL`

### Routes
```
POST /api/npwp/validate                api.npwp.validate    (AJAX)
```

### Controller
- `App\Http\Controllers\Api\NpwpValidationController`

### Service
- `App\Services\Indonesia\NpwpValidator`
  - `validate(string $npwp): array`
    - Normalize: 15-digit lama → 16-digit baru (Coretax)
    - Cek cache (`npwp_validations` table, TTL 30 hari)
    - Kalau miss → call DJP API (atau provider seperti Veripath, Bukalapak Cek NPWP)
    - Cache result

**Provider config:**
Tabel `app_settings` global atau tabel khusus `external_providers`:
- `npwp_validator_url`, `npwp_validator_key_encrypted`

Pakai pattern dynamic — admin input sendiri di `/admin/integrations/npwp`.

### View
- Inline check di `resources/views/clients/create.blade.php`:
  - Field NPWP punya tombol "✓ Verifikasi" → POST ke API
  - Kalau valid: tampilkan badge hijau + nama dari DJP
  - Kalau invalid: tampilkan red badge + alasan

### Edge Cases
- NPWP format lama vs baru — terima keduanya, normalize ke 16-digit
- DJP API down → fallback offline check (algoritma cek sum 16-digit) + flag `unverified`
- Rate limit DJP API (1 req/detik) → throttle via Laravel Bus

### Acceptance Criteria
- [ ] Input NPWP di form klien → tombol verifikasi
- [ ] Real-time validate, cache 30 hari
- [ ] Tampilkan nama wajib pajak kalau valid
- [ ] Auto-fill ke `clients.tax_id` (canonical 16-digit)

---

## 2.2 PPh 23 (Withholding Tax) ❌

**Status:** ❌ | **Priority:** 🔥 Critical | **Effort:** 1 hari

### Why
B2B Indonesia — payer (penerima jasa) wajib potong PPh 23 (umumnya 2% kalau ber-NPWP, 4% kalau tidak). Invoice yang ga punya kolom PPh 23 dianggap "tidak profesional" oleh accountant klien.

### Database Schema
**Tabel BARU `invoice_tax_details`** (lihat 01-DATABASE-RELATIONS Section 5).

**Modifikasi `clients`:**
- `default_pph23_rate DECIMAL(5,2) DEFAULT 0`
- `is_pkp BOOLEAN DEFAULT FALSE`

**Modifikasi `invoices`:**
- `pph23_rate DECIMAL(5,2) DEFAULT 0`
- `pph23_amount DECIMAL(12,2) DEFAULT 0`
- `total_after_pph23 DECIMAL(12,2) GENERATED ALWAYS AS (total - pph23_amount) STORED` (atau hitung di app layer)

### Routes
Tidak ada route baru — extend invoice form.

### Form Changes
Di `resources/views/invoices/create.blade.php` & `edit.blade.php`:

```blade
<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label :value="__('PPh 23 (%)')" />
        <select name="pph23_rate" x-model="pph23_rate" class="input-unicorn rounded-xl">
            <option value="0">Tidak dipotong</option>
            <option value="2">2% (Jasa, ber-NPWP)</option>
            <option value="4">4% (Jasa, tidak ber-NPWP)</option>
        </select>
    </div>
    <div>
        <x-input-label :value="__('Object Code (opsional)')" />
        <input name="pph23_object_code" placeholder="24-104-01">
    </div>
</div>
```

Auto-prefill `pph23_rate` dari `clients.default_pph23_rate`.

### Service
- `App\Services\Indonesia\Pph23Calculator`
  - `calculate(Invoice $i): array { rate, amount, total_after }`
  - Apply ke subtotal (sebelum PPN biasanya, tapi user bisa toggle)

### PDF Template
Tambahkan baris di summary template:
```
Subtotal:           Rp 10.000.000
PPN (11%):          Rp  1.100.000
─────────────────────────────────
Total Tagihan:      Rp 11.100.000
PPh 23 (2%):        (Rp   200.000)
─────────────────────────────────
Total Diterima:     Rp 10.900.000
```

Catatan di footer: *"Klien wajib potong & setor PPh 23 ke kas negara, kirim bukti potong ke kami."*

### Edge Cases
- Klien punya NPWP → 2%, tanpa NPWP → 4% (auto pilih based on `clients.tax_id` exists)
- PPh 23 hanya untuk jasa (object code 24-xxx). Kalau invoice campur barang+jasa, biasanya hanya jasa yang dipotong → user manual mark per line item (advanced; phase 2)
- Workspace yang non-PKP tidak charge PPN tapi masih bisa kena PPh 23

### Acceptance Criteria
- [ ] Field PPh 23 di form invoice
- [ ] Auto-default dari client setting
- [ ] PDF tampilkan baris PPh 23 dengan benar
- [ ] Total balance_due = total - pph23_amount
- [ ] Reporting: rekap PPh 23 yang sudah dipotong klien per bulan

---

## 2.3 e-Faktur / Coretax Export ❌

**Status:** ❌ | **Priority:** ⭐ High | **Effort:** 3 hari

### Why
PKP wajib pakai e-Faktur. Coretax (sejak 2025) menggantikan e-Faktur Web Based. Auto-generate XML compatible dengan upload Coretax = waktu accountant klien hemat berjam-jam.

### Database Schema
Pakai `invoice_tax_details` table (Section 2.2). Field-field e-Faktur:
- `efaktur_status`, `efaktur_no`, `efaktur_qr_code`, `efaktur_xml_path`, `coretax_response_json`

### Routes
```
POST /invoices/{invoice}/efaktur/generate        invoices.efaktur.generate
POST /invoices/{invoice}/efaktur/upload          invoices.efaktur.upload-coretax
GET  /invoices/{invoice}/efaktur/xml             invoices.efaktur.download-xml
GET  /invoices/{invoice}/efaktur/preview         invoices.efaktur.preview
```

### Controller
- `App\Http\Controllers\EFakturController`

### Service
- `App\Services\Indonesia\Coretax\EfakturGenerator`
  - `generateXml(Invoice $i): string`
  - Schema: `<TaxInvoiceBundle>` per spec Coretax (cek dokumen DJP terbaru)
  - Field wajib: NomorFaktur, TanggalFaktur, NPWPSeller, NamaSeller, NPWPBuyer, NamaBuyer, items, DPP, PPN
- `App\Services\Indonesia\Coretax\CoretaxClient`
  - `submit(string $xml): array` — upload ke Coretax API
  - Handle 5xx retry, 422 validation error parsing

**Coretax integration:** dynamic provider — user input API key sendiri di `/admin/integrations/coretax`.

### Workspace Settings
Tambahan field di `workspaces`:
- `is_pkp BOOLEAN DEFAULT FALSE`
- `pkp_no VARCHAR(30) NULL`
- `pkp_certificate_path VARCHAR(255) NULL`  -- digital cert untuk signing

### View
- `resources/views/invoices/efaktur-modal.blade.php` (modal dari halaman invoice show)
  - Status badge (Draft / Approved / Rejected)
  - Tombol "Generate XML"
  - Tombol "Submit to Coretax"
  - Display QR code kalau approved
  - Tombol Download XML

### Edge Cases
- Workspace bukan PKP → fitur disabled (tampil upgrade prompt)
- Invoice ke klien tanpa NPWP → tetap bisa, tapi NPWP=00.000.000.0-000.000
- Submission fail → log error, biarkan user retry; jangan hapus draft
- Coretax punya nomor seri faktur (NSFP) limited — fetch range dari Coretax sebelum generate

### Acceptance Criteria
- [ ] Generate XML compatible Coretax schema
- [ ] Submit ke Coretax via API → terima nomor + QR code
- [ ] QR code embed di PDF invoice (di pojok kanan)
- [ ] Download XML untuk upload manual kalau API down
- [ ] Audit log per submission attempt

---

## 2.4 WhatsApp Send ❌

**Status:** ❌ | **Priority:** 🔥 Critical | **Effort:** 1.5 hari

### Why
Di Indonesia, WhatsApp dominan dibanding email. User mau klik tombol, langsung kirim invoice via WA dengan link checkout.

### Database Schema
**Tabel BARU `whatsapp_logs`** (lihat 01-DATABASE-RELATIONS Section 6).

### Routes
```
POST /invoices/{invoice}/whatsapp/send         invoices.whatsapp.send
POST /quotes/{quote}/whatsapp/send             quotes.whatsapp.send
GET  /invoices/{invoice}/whatsapp/preview      invoices.whatsapp.preview
GET  /workspace/settings/whatsapp              settings.whatsapp.show
PUT  /workspace/settings/whatsapp              settings.whatsapp.update
```

### Controller
- `App\Http\Controllers\WhatsappSendController`

### Service
- `App\Services\Whatsapp\WhatsappAdapter` (interface)
- `App\Services\Whatsapp\WhatsappOfficialAdapter` — WA Business Cloud API (Meta)
- `App\Services\Whatsapp\WhatsappWablasAdapter` — Wablas / Fonnte (lokal Indonesia)
- `App\Services\Whatsapp\WhatsappFactory::resolve($workspace)` — pilih based on `workspaces.whatsapp_provider`

**Pattern dynamic:** user input di workspace settings:
- Provider name (free text)
- API format (dropdown: Meta/Twilio/Wablas/Fonnte/custom-rest)
- Base URL
- API Key (encrypted)
- Sender phone number / Business ID

### View
- `resources/views/settings/whatsapp.blade.php` — config provider
- Tombol "Kirim WhatsApp" di invoice show, modal preview message + edit phone

### Message Template
```
Halo {{client_name}}, 👋

Berikut invoice dari *{{workspace_name}}*:

📄 No: {{invoice_no}}
💰 Total: {{currency}} {{total}}
📅 Jatuh tempo: {{due_date}}

Detail & bayar: {{public_link}}

Terima kasih! 🙏
```

User bisa edit di settings.

### Edge Cases
- Phone number klien kosong/tidak valid → tombol disabled + prompt edit klien
- WA Business butuh template approval — kasih warning user untuk approve template di Meta Business Manager
- Rate limit WA Business Cloud API (250/24h tier 1) — queue + throttle
- Status delivered/read pakai webhook receive — set up `/webhooks/whatsapp/{provider}` endpoint

### Acceptance Criteria
- [ ] Konfigurasi WA provider di workspace settings
- [ ] Tombol kirim di invoice & quote show
- [ ] Preview message + edit before send
- [ ] Log delivery status (sent/delivered/read/failed)
- [ ] Webhook receive untuk update status real-time

---

## 2.5 QRIS Embed ❌

**Status:** ❌ | **Priority:** ⭐ High | **Effort:** 1 hari

### Why
QRIS = standar pembayaran nasional Indonesia. Klien scan, bayar via app bank/ewallet apapun. Lebih simple dari payment gateway redirect flow.

### Database Schema
**Tabel BARU `qris_codes`** (lihat 01-DATABASE-RELATIONS Section 21).

### Routes
```
POST /invoices/{invoice}/qris/generate         invoices.qris.generate
GET  /invoices/{invoice}/qris/{qris}.png       invoices.qris.image
POST /webhooks/qris/{provider}                 webhooks.qris.callback
```

### Controller
- `App\Http\Controllers\QrisController`

### Service
- `App\Services\Indonesia\QrisGenerator`
  - `generate(Invoice $invoice, PaymentGateway $provider): QrisCode`
  - Call provider API (Midtrans/Xendit/iPaymu/Mekari Pay) → terima `qr_string`
  - Render PNG via `chillerlan/php-qrcode` package
  - Save to `storage/app/public/qris/`
- `App\Services\Indonesia\QrisCallbackHandler::handle($provider, $payload)`
  - Verify signature
  - Update qris_codes.status='paid'
  - Auto-create payment record

### View
Embed di `resources/views/invoices/show.blade.php` & `public.blade.php`:
```
[Tombol: Generate QRIS untuk Pembayaran]
→ tampil QR code besar + nominal + countdown expiry
→ klien scan via mobile banking
```

### Edge Cases
- QRIS dynamic per invoice (bukan static merchant QR) — nominal terikat
- Expiry default 30 menit; renew via tombol "Generate QR baru"
- Multiple QRIS untuk 1 invoice (klien gagal scan, generate ulang) → status='cancelled' yang lama
- Webhook double-fire → idempotent via provider_ref unique check

### Acceptance Criteria
- [ ] Generate QR per invoice
- [ ] Tampil di public view dengan countdown
- [ ] Webhook callback update payment status
- [ ] Audit log per generate & per callback
- [ ] QR image di-cache (regenerate kalau hilang)

---

## 2.6 Bank Auto-Reconcile ❌

**Status:** ❌ | **Priority:** 💎 Medium | **Effort:** 2 hari

### Why
Klien bayar via transfer bank, user harus cocok-kan manual antara mutasi rekening BCA/Mandiri dengan invoice. Repetitive & error-prone. Auto-reconcile saving ~2 jam/minggu.

### Database Schema
**Tabel BARU `bank_reconcile_batches` + `bank_reconcile_lines`** (lihat 01-DATABASE-RELATIONS Section 7).

**Modifikasi `payments`:**
- `bank_reconcile_line_id BIGINT NULL FK→bank_reconcile_lines ON DELETE SET NULL`

### Routes
```
GET    /bank-reconcile                       bank-reconcile.index
GET    /bank-reconcile/upload                bank-reconcile.create
POST   /bank-reconcile                       bank-reconcile.store     (upload CSV)
GET    /bank-reconcile/{batch}               bank-reconcile.show      (review matching)
POST   /bank-reconcile/{batch}/match         bank-reconcile.match     (manual approve)
POST   /bank-reconcile/{batch}/finalize      bank-reconcile.finalize  (commit semua match → create payments)
```

### Controller
- `App\Http\Controllers\BankReconcileController`

### Service
- `App\Services\Bank\StatementParser` — abstract
- `App\Services\Bank\BcaCsvParser`, `MandiriCsvParser`, `BniCsvParser`, `OfxParser`, `Mt940Parser`
- `App\Services\Bank\Matcher`
  - `findMatchCandidates(Line $line): Collection`
  - Strategi:
    1. Exact amount + date in last 7 days → high confidence
    2. Reference contains invoice_no → high confidence
    3. Amount + date range 14 days → medium
    4. Amount only (large discrepancy) → low / manual

### View
- `resources/views/bank-reconcile/index.blade.php` — list batches dengan status
- `resources/views/bank-reconcile/create.blade.php` — drag-drop CSV upload + pilih bank
- `resources/views/bank-reconcile/show.blade.php` — table line × candidate match dropdown, approve buttons

### Edge Cases
- Mutasi tidak match invoice apapun (private, transfer pribadi) → mark "ignore"
- 1 mutasi match >1 invoice (klien bayar borongan) → split flow: input mapping manual `1.5jt → INV-001 (1jt) + INV-002 (500k)`
- Duplicate import → check by `(account_no, trx_date, amount, description)` hash, reject
- Bank format berubah → parser mapping ditaruh di JSON config supaya gampang update

### Acceptance Criteria
- [ ] Upload CSV BCA, Mandiri, BNI berhasil parse
- [ ] Auto-match high confidence (>=80% akurasi target)
- [ ] User review & approve manual untuk medium/low
- [ ] Finalize → create payment records, update invoice status
- [ ] Re-runnable kalau salah (rollback batch)
- [ ] Audit log per batch
