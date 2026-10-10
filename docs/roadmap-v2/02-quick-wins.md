# Quick Wins — Sprint 1

> Impact tinggi, effort 1-3 hari per fitur. Dikerjakan duluan supaya cash flow user langsung naik.

---

## 1.1 Estimate / Quotation 🟦

**Status:** 🟦 DB Ready | **Priority:** 🔥 Critical | **Effort:** 2 hari

### Why
Workflow B2B Indonesia: penawaran dulu → klien setuju → invoice. Tanpa estimate, user kerja 2x (bikin di Excel/Word, terus duplikat ke Invocely).

### Database Schema
**Tabel `quotes` SUDAH ADA** (di `mega_features_batch.php`):
- workspace_id, client_id, quote_no (unique per workspace)
- status: draft, sent, accepted, rejected, expired, converted
- issue_date, expiry_date
- subtotal, tax_rate, tax_amount, discount_*, total
- items (JSON — bisa diparse atau extract ke tabel `quote_items` kalau perlu reporting)
- converted_invoice_id → invoices.id (SET NULL on delete invoice)
- public_token unique
- accepted_at

**Tambahan kolom (modifikasi):**
- `quotes.signed_at TIMESTAMP NULL` — track signature
- `quotes.project_id BIGINT NULL FK→projects` — kalau project tracking sudah jalan

**Relasi:**
- workspace → quotes (1:N)
- client → quotes (1:N)
- quote → invoice (1:1 via converted_invoice_id, optional)

### Routes
```
GET    /quotes                       quotes.index
GET    /quotes/create                quotes.create
POST   /quotes                       quotes.store
GET    /quotes/{quote}               quotes.show
GET    /quotes/{quote}/edit          quotes.edit
PUT    /quotes/{quote}               quotes.update
DELETE /quotes/{quote}               quotes.destroy
POST   /quotes/{quote}/send-email    quotes.send
POST   /quotes/{quote}/convert       quotes.convert         → creates invoice
POST   /quotes/{quote}/duplicate     quotes.duplicate
GET    /quotes/{quote}/pdf           quotes.pdf
GET    /q/{token}                    quotes.public          (public view)
POST   /q/{token}/accept             quotes.public.accept   (klien setuju)
POST   /q/{token}/reject             quotes.public.reject
```

### Controllers
- `app/Http/Controllers/QuoteController.php` (mirror InvoiceController)
- `app/Http/Controllers/PublicQuoteController.php` untuk `/q/{token}`

### Views
- `resources/views/quotes/index.blade.php`
- `resources/views/quotes/create.blade.php` — copy dari invoices/create dengan field expiry_date
- `resources/views/quotes/edit.blade.php`
- `resources/views/quotes/show.blade.php` — tombol "Convert to Invoice" prominent
- `resources/views/quotes/public.blade.php` — versi klien dengan tombol Accept / Reject / Sign
- `resources/views/quotes/pdf.blade.php`

### Service
- `App\Services\QuoteService::convertToInvoice(Quote $q): Invoice`
  - Copy items, totals, client, currency
  - Set quote.status = 'converted', quote.converted_invoice_id
  - Audit log entry
  - Return new Invoice (status draft)

### Edge Cases
- Quote yang sudah converted ga bisa diedit lagi
- Expired quote (`expiry_date` < hari ini) auto-status='expired' via daily scheduler
- Klien decline → status='rejected', user dapat notification
- Currency mismatch saat convert: pakai currency dari quote (jangan auto-convert)

### Acceptance Criteria
- [ ] User bisa CRUD quote
- [ ] Quote punya nomor auto-increment per workspace (`QUO-001`, dst — pakai workspace.quote_prefix)
- [ ] Public view `/q/{token}` tanpa login
- [ ] Klien bisa Accept/Reject dari public view, status update real-time
- [ ] Convert → Invoice 1-klik, items copy persis
- [ ] PDF generate dengan template yang sama dengan invoice
- [ ] Email send dengan PDF lampiran

---

## 1.2 Reminder Sequence ❌

**Status:** ❌ | **Priority:** 🔥 Critical | **Effort:** 1.5 hari

### Why
Cash flow killer adalah klien yang lupa bayar. Reminder otomatis di H-3, H-0, H+3, H+7 mengubah collection rate +20-40% (data dari studi FreshBooks).

### Database Schema
**Tabel BARU `invoice_reminders`** (lihat `01-DATABASE-RELATIONS.md` Section 1).

Plus tabel template (opsional, bisa pakai workspace settings JSON):
- atau simpan di `app_settings` workspace-scoped: `reminder_templates: {h_minus_3: {...}, h_zero: {...}, h_plus_3: {...}, h_plus_7: {...}}`

### Routes
```
GET    /invoices/{invoice}/reminders          invoices.reminders.index   (lihat scheduled + history)
POST   /invoices/{invoice}/reminders          invoices.reminders.store    (manual schedule)
DELETE /reminders/{reminder}                  invoices.reminders.cancel
GET    /workspace/settings/reminders          settings.reminders.show     (template editor)
POST   /workspace/settings/reminders          settings.reminders.update
```

### Controllers
- `App\Http\Controllers\InvoiceReminderController`
- `App\Http\Controllers\Settings\ReminderTemplateController`

### Service
- `App\Services\Reminder\ReminderScheduler::scheduleFor(Invoice $invoice)`
  - Saat invoice di-mark sebagai 'sent' → auto-schedule 4 reminder
  - Bisa dikustom per-invoice (mis. klien VIP ga di-reminder)
- `App\Console\Commands\ProcessRemindersCommand` — dipanggil tiap jam via scheduler
  - Query reminders WHERE status='pending' AND scheduled_at <= now()
  - Send via existing email infrastructure
  - Update status, tampung error
- `App\Mail\InvoiceReminderMail`

### Scheduler
```php
// app/Console/Kernel.php
$schedule->command('reminders:process')->hourly();
```

### Views
- `resources/views/invoices/reminders.blade.php` — tab di halaman invoice
- `resources/views/settings/reminders.blade.php` — edit template

### Edge Cases
- Invoice paid sebelum reminder kekirim → cancel pending reminders
- Klien opt-out via email link → flag `clients.no_reminders=true` dan skip
- Reminder ke invoice yang ada dispute (status=cancelled atau ada credit note pending) → skip
- Failed send (SMTP error) → retry 3x dengan exponential backoff, lalu mark failed dan notify user

### Acceptance Criteria
- [ ] Tabel reminder + scheduler jalan
- [ ] User bisa edit template per offset (-3, 0, +3, +7) — variabel: `{{client_name}}`, `{{invoice_no}}`, `{{amount_due}}`, `{{due_date}}`, `{{pay_link}}`
- [ ] Auto-schedule saat invoice dikirim
- [ ] Auto-cancel saat invoice paid
- [ ] Manual cancel/snooze per reminder
- [ ] Tab "Reminders" di halaman invoice menampilkan history (Sent / Pending / Failed)
- [ ] Email punya unsubscribe link → set client.no_reminders=true

---

## 1.3 Late Fee Otomatis ❌

**Status:** ❌ | **Priority:** ⭐ High | **Effort:** 1 hari

### Why
Pelengkap reminder. Klien tahu ada konsekuensi, lebih cepat bayar. Industri freelance Indonesia umum charge 1-2%/bulan late fee.

### Database Schema
**Tabel BARU `invoice_late_fees`** (lihat 01-DATABASE-RELATIONS.md Section 2).

**Modifikasi `invoices`:**
- `late_fee_total DECIMAL(12,2) DEFAULT 0` — akumulasi semua late fee
- `late_fee_policy ENUM('none','one_time','monthly') DEFAULT 'none'`
- `late_fee_value DECIMAL(10,2) DEFAULT 0` — % atau Rp
- `late_fee_type ENUM('percent','fixed') DEFAULT 'percent'`

Workspace default ada di `workspaces.default_late_fee_*`.

### Routes
```
POST   /invoices/{invoice}/late-fees           invoices.late-fees.apply  (manual trigger)
DELETE /invoices/{invoice}/late-fees/{fee}     invoices.late-fees.destroy
GET    /workspace/settings/late-fees           settings.late-fees.edit
PUT    /workspace/settings/late-fees           settings.late-fees.update
```

### Controllers
- `App\Http\Controllers\InvoiceLateFeeController`

### Service
- `App\Services\LateFee\LateFeeApplier`
  - `applyOnce(Invoice $i)` — fixed atau percent
  - `applyMonthlyIfDue(Invoice $i)` — cek tanggal lewat 30/60/90 hari → apply ulang
- `App\Console\Commands\ProcessLateFeesCommand` — daily

### Edge Cases
- Late fee jangan apply ke invoice yang baru saja paid hari ini (race condition)
- Total invoice + late fee tidak boleh berubah retroaktif setelah invoice paid → freeze saat status=paid
- Late fee sebagai virtual line item di PDF → tampil di bawah subtotal sebagai "Late fee (3 hari × 1%)"
- Saat klien protes, admin bisa hapus late_fee record → otomatis recalculate `invoices.balance_due`

### Acceptance Criteria
- [ ] User set policy default di workspace settings
- [ ] User override policy per-invoice saat create
- [ ] Daily scheduler apply late fee otomatis untuk invoice overdue
- [ ] Late fee tampil di invoice PDF & public view
- [ ] User bisa hapus late fee individu (dengan reason logging ke audit_log)
- [ ] Audit log: setiap apply/remove tercatat

---

## 1.4 Credit Note / Refund ❌

**Status:** ❌ | **Priority:** ⭐ High | **Effort:** 1 hari

### Why
Banyak skenario: klien return barang, salah charge, refund partial. Tanpa credit note, accounting jadi rusak. Plus syarat compliance untuk PKP.

### Database Schema
**Tabel BARU `credit_notes`** (lihat 01-DATABASE-RELATIONS.md Section 3).

### Routes
```
GET    /invoices/{invoice}/credit-notes/create   credit-notes.create
POST   /invoices/{invoice}/credit-notes          credit-notes.store
GET    /credit-notes                             credit-notes.index
GET    /credit-notes/{credit}                    credit-notes.show
PUT    /credit-notes/{credit}                    credit-notes.update
POST   /credit-notes/{credit}/issue              credit-notes.issue       (draft → issued)
POST   /credit-notes/{credit}/apply              credit-notes.apply       (issued → applied)
POST   /credit-notes/{credit}/cancel             credit-notes.cancel
GET    /credit-notes/{credit}/pdf                credit-notes.pdf
```

### Controllers
- `App\Http\Controllers\CreditNoteController`

### Service
- `App\Services\CreditNoteService`
  - `apply(CreditNote $cn)` — reduce `invoices.balance_due` (atau create negative payment row)
  - Validate: `cn.amount` <= `invoices.balance_due` saat issue
  - Update `invoices.status` kalau credit note menutup balance: `paid` atau tetap `partial`

### Views
- `resources/views/credit-notes/index.blade.php`
- `resources/views/credit-notes/create.blade.php` — dropdown invoice (filter to non-cancelled)
- `resources/views/credit-notes/show.blade.php`
- `resources/views/credit-notes/pdf.blade.php`

### Edge Cases
- Credit note > invoice balance → reject saat issue
- Credit note pada invoice yang sudah cancelled → reject
- Credit note sudah applied → tidak bisa diedit (cuma cancel)
- Cancel credit note yang already applied → reverse balance_due (audit log mandatory)

### Acceptance Criteria
- [ ] User bisa buat credit note dari invoice (button "Issue Credit Note")
- [ ] Numbering auto: CN-YYYY-NNN per workspace
- [ ] PDF terpisah dengan header "CREDIT NOTE" jelas
- [ ] Apply update invoice balance_due
- [ ] Tampil di halaman invoice sebagai timeline event
- [ ] Reporting: total credit note per bulan di laporan tax

---

## 1.5 Discount Code (Coupon) — Extend untuk Invoice 🟦

**Status:** 🟦 (sebagian) | **Priority:** 💎 Medium | **Effort:** 1 hari

### Why
Kupon promo yang bisa diapply klien saat checkout via halaman publik. Kompetitor (Stripe Invoice, Wave) punya, kita juga harus.

### Database Schema
**Tabel `coupons` SUDAH ADA**, tapi sekarang cuma untuk subscription. Perlu extend:

**Modifikasi `coupons`:**
- `applies_to ENUM('subscription','invoice','both') DEFAULT 'subscription'`
- `min_amount DECIMAL(12,2) NULL` — minimal invoice total
- `workspace_id BIGINT NULL FK→workspaces ON DELETE CASCADE` — NULL=global (admin/marketing), filled=workspace-scoped (user bikin sendiri)

**Tabel BARU `coupon_redemptions`:**
```sql
id BIGINT PK
coupon_id BIGINT FK→coupons ON DELETE CASCADE
workspace_id BIGINT FK
subject_type VARCHAR(50)  -- 'invoice','subscription'
subject_id BIGINT
discount_applied DECIMAL(12,2)
redeemed_at TIMESTAMP
INDEX(coupon_id, redeemed_at)
INDEX(subject_type, subject_id)
```

### Routes
```
GET  /coupons                          coupons.index    (workspace-scope)
POST /coupons                          coupons.store
PUT  /coupons/{coupon}                 coupons.update
DEL  /coupons/{coupon}                 coupons.destroy
POST /i/{token}/apply-coupon           public.invoice.apply-coupon   (klien input code di public view)
```

### Controllers
- `App\Http\Controllers\CouponController`
- Add method `applyCoupon` ke `PublicInvoiceController`

### Service
- `App\Services\CouponService::redeem(Coupon $c, Invoice|Subscription $subject)`
  - Validate: not expired, not exceeded max_uses, min_amount met, workspace match
  - Create redemption record
  - Apply discount: untuk invoice, push ke `invoices.discount_amount` atau ke virtual line item

### Edge Cases
- Coupon dipakai 2x bersamaan (race) — pakai DB lock saat increment uses_count
- Coupon valid tapi invoice udah paid → reject
- Coupon expired di tengah redeem flow → re-validate sebelum commit
- Stack-able discount (coupon + invoice.discount_value) — by default tidak boleh; konfigurasi di coupon.allow_stack

### Acceptance Criteria
- [ ] User bikin kode promo dari `/coupons`
- [ ] Klien input kode di public invoice view → instant validate + diskon apply
- [ ] Riwayat redemption tampil di halaman coupon detail
- [ ] Kalau exceeded max_uses → reject dengan pesan jelas

---

## 1.6 Aging Report ❌

**Status:** ❌ | **Priority:** ⭐ High | **Effort:** 1 hari

### Why
Standard accounting report. Bucketed view "siapa lewat berapa hari & jumlah" — tools collection paling penting.

### Database Schema
Tidak ada tabel baru. Pure aggregate query.

### Routes
```
GET /reports/aging                     reports.aging
GET /reports/aging.csv                 reports.aging.csv
GET /reports/aging.pdf                 reports.aging.pdf
```

### Controllers
- Add method `aging()` ke `App\Http\Controllers\ReportController`

### Service
- `App\Services\Reports\AgingReportService::generate($workspaceId, ?Carbon $asOf = null): array`
  - Buckets: 0 (current), 1-30, 31-60, 61-90, 90+
  - Per client: total per bucket
  - Total summary across all clients

### View
- `resources/views/reports/aging.blade.php`
  - Table per client × bucket
  - Tombol "Send Reminder" inline per client overdue (link ke 1.2)
  - Filter date range, currency

### Acceptance Criteria
- [ ] Page aging tampil bucket dengan total
- [ ] Per-client drill-down ke daftar invoice
- [ ] Export CSV & PDF
- [ ] Filter currency (kalau workspace multi-currency)
- [ ] Klik bucket → filter invoice list dengan filter sesuai
