# DOCS ACCURACY AUDIT — /docs (2026-10-10)

Metode: setiap klaim faktual di `resources/views/docs.blade.php` dicocokkan
ke route/controller/kode. Hasil: PASS semua; tidak ada klaim provider,
limit, atau compliance yang dikarang.

| Klaim di /docs | Sumber kode | Status |
|---|---|---|
| Workspace tunggal per record; setup bisnis/mata uang/logo/prefix | WorkspaceController, workspace.settings | PASS |
| KPI dari database; draft bukan revenue; gagal bukan koleksi | DashboardController@index | PASS |
| Client: search/filter/pagination; hapus diblokir bila ber-invoice | ClientController@destroy guard | PASS |
| Opt-out reminder per client di form | clients._form no_reminders + ReminderScheduler | PASS |
| CSV validasi per baris | ClientImportController | PASS |
| Item/diskon/pajak/total server-side; status draft/sent/partial/paid/overdue/cancelled; hanya draft bisa edit/hapus | InvoiceController | PASS |
| Duplicate nomor baru; token publik unguessable; PDF print layout | InvoiceController@duplicate; public_token uuid | PASS |
| Quote konversi tepat sekali (409 ganda) | QuoteController@convertToInvoice | PASS |
| Manual/parsial/full payment; 8 gateway butuh kredensial | PaymentGatewayService match (midtrans..generic) | PASS |
| Webhook transaksi+lock+replay+stored-amount; signature butuh secret | PaymentController@webhook, BillingWebhookController | PASS |
| Recurring hourly; reminder hourly + opt-out; overdue hourly; late fee 06:00 | routes/console.php | PASS |
| Reports aging/cashflow/PL/tax; cancelled di luar revenue | ReportController | PASS |
| Rekonsiliasi finalize payment rows; CSV anti formula-injection | BankReconcileController; ExportController::safeCell | PASS |
| E-Faktur helper, bukan sertifikasi DJP | EfakturGenerator + label view | PASS |
| Role owner/admin/member; 403/404 lintas workspace | pivot enum; IsolationTest | PASS |
| 2FA, API token revocable, webhook keluar, audit read-only | Settings/*, ApiTokenController | PASS |
| Blog superadmin-only | middleware superadmin + test 403 | PASS |
| Free/Pro/Business; limit di backend | EnforcePlanLimits + PlanSeeder | PASS |
| SMTP log default; gateway per workspace; AI key tak dirender | config/mail; PaymentGateway; AiProviderController | PASS |
| PWA HTTPS/offline fallback/tanpa antrean/tanpa cache API | sw.js v2; manifest | PASS |
| Troubleshooting 6 baris sesuai error nyata | kode terkait | PASS |
| API: web routes saja, pagination 20, 422 bags | route:list; controllers | PASS |
| Webhook events 4 tipe idempoten per external id | BillingWebhookController | PASS |
| Status Active/Partial/Blocked/Coming Soon sesuai matrix | FEATURE-MATRIX | PASS |
| Demo creds hanya local | @if(app()->isLocal()) | PASS |

Tidak ada dead anchor: 15 section id unik; nav cocok; search + scroll-spy + empty state diuji E2E (public pages render, no console error).
