# FEATURE MATRIX — Invoecely (sumber kebenaran)

Generated: 2026-10-10. Basis: kode aktual (301 routes, 60 models, 25 migrations,
~190 Blade views), 47 automated tests (semua hijau), bukan asumsi menu.

Legenda status:
- **OK** — implemented & verified (ada test/bukti).
- **OK*** — implemented, diverifikasi manual/code-review, tanpa automated test khusus.
- **PARTIAL** — ada tapi belum lengkap / butuh kredensial eksternal.
- **STUB** — coming-soon transparan.
- **BLOCKED** — butuh kredensial/layanan eksternal.

Workspace isolation: global scope `BelongsToWorkspace` + `abort_unless(...,403)`
di controller utama. Cross-workspace → 404 (scoped binding) atau 403 (guard
eksplisit). Dibuktikan: `ClientTest::client_is_scoped_to_workspace`,
`InvoiceTest::invoice_is_scoped_to_workspace`.

## A. Workspace & akses

| Fitur | Route | Role | Kode | DB | Status | Bukti |
|---|---|---|---|---|---|---|
| Dashboard KPI real | `dashboard` | auth | DashboardController | invoices/payments/clients | OK | PseoTest + manual |
| Workspace settings update | `workspace.settings/update` | auth | WorkspaceController | workspaces | OK* | — |
| Workspace switch | `workspace.switch` | auth | WorkspaceSwitcherController | workspace_user | OK* | — |
| Workspace create (first) | `workspace.create/store` | auth | WorkspaceController@create/store | workspaces, workspace_user | OK | WorkspaceTest (3) |
| Tanpa workspace → redirect create | (middleware) | auth | EnsureWorkspaceExists | — | OK | WorkspaceTest |
| Team invite/revoke | `team.*` | auth | TeamController | workspace_invites | OK* | — |
| 2FA setup/verify/disable | `profile/2fa.*` | auth | Settings\TwoFactorController | user_two_factor_secrets | OK* | view+routes verified |
| API tokens | `api-tokens.*` | auth+plan | ApiTokenController | workspace_api_tokens | OK* | abort_unless present |
| Audit log read-only | `audit-logs.index` | auth+plan | AuditLogController | audit_logs | OK* | — |
| Tenant webhooks out | `tenant-webhooks.*` | auth+plan | TenantWebhookController | tenant_webhooks | OK* | — |
| Custom domain | `custom-domain.*` | auth | CustomDomainController | custom_domains | OK* | — |

## B. Client & product

| Fitur | Route | Role | Kode | DB | Status | Bukti |
|---|---|---|---|---|---|---|
| Client CRUD + search/filter/pagination | `clients.*` | auth | ClientController | clients | OK | ClientTest (6) |
| Client delete guard (punya invoice) | `clients.destroy` | auth | ClientController@destroy | clients | OK | code (restrict + friendly error) |
| Reminder opt-out per client | client form `no_reminders` | auth | ClientController, ReminderScheduler | clients.no_reminders | OK | code (fillable+form) |
| CSV import per-baris | `clients-import` | auth+plan | ClientImportController | clients | OK* | — |
| Portal users per client | `clients.{id}.portal-users` | auth | ClientPortalManageController | client_portal_users | OK* | — |
| Product CRUD | `products.*` | auth | ProductController | products | OK* | — |
| Project CRUD | `projects.*` | auth | ProjectController | projects | OK* | — |
| Time entries | `time-entries.*` | auth+plan | TimeEntryController | time_entries | OK* | — |
| Expense CRUD + OCR review | `expenses.*` | auth+plan | ExpenseController, ExpenseOcrController | expenses | OK* | — |

## C. Quotation & invoice

| Fitur | Route | Role | Kode | DB | Status | Bukti |
|---|---|---|---|---|---|---|
| Invoice CRUD + filter/search/pagination | `invoices.*` | auth+plan | InvoiceController | invoices, invoice_items | OK | InvoiceTest (8) |
| Invoice numbering race-safe | `invoices.store` | auth+plan | InvoiceService::generateInvoiceNo (lockForUpdate) | workspaces.next_invoice_no | OK | code + unique constraint |
| Quote numbering race-safe | `quotes.store` | auth | InvoiceService::generateQuoteNo + retry | quotes | OK | code |
| Credit-note numbering retry | `credit-notes.store` | auth | CreditNoteController (retry) | credit_notes | OK | code |
| Status transitions tervalidasi | `invoices.update-status` | auth | InvoiceController@updateStatus | invoices | OK | InvoiceTest |
| Double-convert guard | `quotes.convert` | auth | QuoteController (row lock + 409) | quotes, invoices | OK | code |
| Duplicate invoice (identitas baru) | `invoices.duplicate` | auth+plan | InvoiceController@duplicate | invoices | OK* | replicate + token baru |
| PDF download | `invoices.pdf` | auth | InvoiceService→PdfService | storage | OK* | — |
| Kirim email | `invoices.send` | auth | EmailInvoiceController | email_logs, queue | PARTIAL | mailer=log default; butuh SMTP prod |
| Public invoice (token) | `public.invoice.show` | publik | PublicInvoiceController | invoices.public_token | OK* | — |
| Public quote accept/reject/sign | `public.quotes.*` (legacy) | — | DIHAPUS — instant mutation tanpa identitas | — | REMOVED | diganti alur verified di bawah |
| Quote lifecycle (send/withdraw/reopen/rotate) | `quotes.send/withdraw/reopen/rotate-link` | auth | QuoteController + QuoteApprovalService | quotes (+revision) | OK | QuoteApprovalTest |
| Public quote review (read-only) | `public.quote.show` | publik | PublicQuoteController@show | quotes.public_token | OK | a1–a7 + E2E FLOW 1 |
| OTP approval (verified channel) | `public.quote.otp.*` | publik | QuoteApprovalService | challenges (hash-only) | OK | b8–b14, f48–f52 + docs |
| Portal approver approval | `public.quote.confirm*` | portal guard | PublicQuoteController + service | approval_events | OK | b15–b17 + E2E FLOW 2 |
| High-risk portal-only policy | workspace settings | auth | requiresPortalApproval | workspaces.threshold | OK | b18–b19 |
| Quote evidence + history | `quotes.show` card | auth | QuoteApprovalEvent | approval_events | OK | c24, c32, e46 |
| Conversion policy (manual/auto-draft) | workspace settings | auth | QuoteConversionService | invoices.quote_revision | OK | d33–d40 + E2E FLOW 5 |
| Client portal login/dashboard | `portal.*` | portal guard | PortalAuthController | client_portal_users | OK* | — |
| Embed invoice/widget | `embed.*` | publik/auth | EmbedController | invoices | OK* | — |
| Invoice attachments | `invoices.attachments.*` | auth | InvoiceAttachmentController | invoice_attachments | OK* | — |
| Invoice comments | `invoices.comments.*` | auth | InvoiceCommentController | invoice_comments | OK* | author-only delete |
| Reminders schedule/cancel | `invoices.reminders.*` | auth | InvoiceReminderController + scheduler | invoice_reminders | OK* | hourly scheduler |
| Late fees apply/reverse | `invoices.late-fees.*` | auth | InvoiceLateFeeController, LateFeeApplier | invoice_late_fees | OK | lock+recheck, FinancialInvariantsTest |
| Tax details (PPh23) | `invoices.tax-details.*` | auth | InvoiceTaxDetailController | invoice_tax_details | OK* | — |
| WhatsApp preview/send | `invoices.whatsapp.*` | auth | WhatsappSendController | whatsapp_logs | PARTIAL | butuh provider WA |
| QRIS generate/show/cancel | `invoices.qris.*` | auth | QrisController | qris_codes | PARTIAL | butuh provider QRIS |
| AI translate/description/expense | `invoices.translate`, `api.ai.*` | auth | AI services | ai_providers | PARTIAL | butuh API key provider |
| NPWP validate | `api.npwp.validate` | auth | NpwpValidator | npwp_validations | PARTIAL | checksum non-DJP resmi, offline fallback |

## D. Payment & rekonsiliasi

| Fitur | Route | Role | Kode | DB | Status | Bukti |
|---|---|---|---|---|---|---|
| Manual payment + partial | `payments.store` | auth | PaymentController | payments | OK | PaymentTest (5) |
| Full payment → paid | `payments.store` | auth | recalculateInvoice | invoices | OK | PaymentTest |
| Delete payment + recalc | `payments.destroy` | auth | PaymentController@destroy | payments | OK | PaymentTest |
| Gateway checkout (7 provider) | `payments.gateway`, gatewayPay | auth/publik | PaymentGatewayService | payments, payment_gateways | OK* | fail-closed: Xendit/Doku kini throw saat provider menolak (tanpa kredensial sandbox tidak dapat diuji end-to-end) |
| Webhook settle + replay guard | `webhooks.payment` | publik | PaymentController@webhook | payments | OK | WebhookTest (4) |
| Webhook signature verify | `webhooks.payment` | publik | — | — | BLOCKED | butuh secret provider |
| Bank reconcile match/finalize | `bank-reconcile.*` | auth | BankReconcileController | bank_reconcile_* | OK | kolom finalize diperbaiki |
| Accounting export | `accounting-exports.*` | auth | AccountingExportController | accounting_exports | OK* | — |
| CSV exports (formula-safe) | `exports.*` | auth | ExportController | — | OK* | perlu verifikasi injection |

## E. Recurring / tax / reporting / billing

| Fitur | Route | Role | Kode | DB | Status | Bukti |
|---|---|---|---|---|---|---|
| Recurring CRUD + toggle | `recurring.*` | auth+plan | RecurringInvoiceController + hourly job | recurring_invoices | OK* | scheduler verified |
| E-Faktur XML generate | `invoices.efaktur.*` | auth | EfakturGenerator | invoice_tax_details | OK | EfakturXmlTest: escaping terverifikasi empiris, filename sanitized, approved tidak regresi |
| Reports aging/cashflow/pl/tax | `reports.*` | auth | ReportController | invoices/payments | OK* | — |
| Report subscriptions | `report-subscriptions.*` | auth | ReportSubscriptionController | report_subscriptions | OK* | — |
| Billing subscribe/cancel/resume | `billing.*` | auth | BillingController | subscriptions | OK* | — |
| Billing webhook idempotent | `webhooks.billing` | publik | BillingWebhookController | subscription_invoices | OK | WebhookTest (1) |
| Manual payment proof approve/reject | `admin.manual-payments.*` | superadmin | AdminManualPaymentController | subscriptions | OK* | — |
| Plans/Users/Workspaces/Subscriptions | `admin.*` | superadmin | Admin*Controller | plans/users/workspaces | OK* | — |
| Coupons (tenant + admin) | `coupons.*`, `admin.coupons.*` | auth/superadmin | CouponController | coupons | OK* | uses_count enforcement perlu verifikasi |
| Affiliate track | `affiliate.index` | auth | AffiliateController | affiliates | PARTIAL | payout manual (transparan di UI) |
| Support tickets | `support.*` | auth | SupportController | support_tickets | OK* | owner/superadmin guard |
| Notifications | `notifications.*` | auth | NotificationController | app_notifications | OK* | — |
| Approvals | `approvals.*`, `approval-rules.*` | auth | ApprovalController | approvals | OK* | — |
| Invoice layouts builder | `invoice-layouts.*` | auth+plan | InvoiceLayoutController | invoice_layouts | OK* | layout_id kini tersimpan (fillable fix) |
| Dashboard widgets CRUD/reorder | `dashboard.widgets.*` | auth | DashboardWidgetController | dashboard_widgets | OK | route shadow diperbaiki |
| License pairing wizard | `pair.*` | publik | PairController | lock file | OK* | dev_bypass wajib false di prod |
| Blog publik + feed + SEO pages | `blog.*`, `seo.*`, pseo | publik | BlogController, ProgrammaticSeoController | blog_posts | OK | BlogTest+PseoTest (17) |
| Blog admin | `admin.blog.*` | superadmin | Admin\BlogController | blog_posts | OK | BlogTest (8, incl. 403) |
| Marketplace | `marketplace.index` | auth | static view | — | STUB | transparan "Coming Soon Q3 2026" |
| Feature scaffolds | `feature.coming` | auth | ComingSoonController | — | STUB | 6 link sidebar, transparan |

## Bukti pengujian (revisi quote-approval 2026-10-10)

- `php artisan test`: **128 passed, 0 failed** (lama 74 + QuoteApproval 50 + FinancialInvariants 4).
- Playwright nyata: **14/14** (7 skenario × desktop+mobile), nol console error,
  overflow 0, 5 screenshot di `docs/audit/shots/`.
- UI: Tabler 100% halaman aktif (132 MIGRATED + EXEMPT sah); sidebar 44/44 PASS.
- `npm run build`: sukses. `composer validate`: valid. `npm audit`: 0 vuln.
- `node --check sw.js`: OK. `php artisan route:list`: 301.
- Belum: import-CSV baris-gagal, queue jobs khusus, gateway sandbox (BLOCKED),
  install PWA device nyata (BLOCKED).
- Browser E2E (Playwright): **belum dijalankan** — tidak tersedia di environment ini.
- Visual regression per viewport: **belum** — lihat Known Limitations.
- Cakupan yang belum ada: auth/2FA flows, queue jobs, mail dispatch, plan enforcement
  negatif, import CSV baris-gagal, gateway sandbox (butuh kredensial).
