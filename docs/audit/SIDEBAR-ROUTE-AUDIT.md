# SIDEBAR-ROUTE AUDIT (generated 2026-10-10)

Setiap item sidebar dipetakan ke route terdaftar + middleware. Duplikat dan placeholder dicatat.

| Menu | Route | URI | Middleware | Status |
|---|---|---|---|---|
| Dashboard | `dashboard` | `dashboard` | web,auth,verified,workspace | PASS |
| Notifications | `notifications.index` | `notifications` | web,auth,verified,workspace | PASS |
| Invoices | `invoices.index` | `invoices` | web,auth,verified,workspace | PASS |
| Quotations | `quotes.index` | `quotes` | web,auth,verified,workspace | PASS |
| Clients | `clients.index` | `clients` | web,auth,verified,workspace | PASS |
| Products & Services | `products.index` | `products` | web,auth,verified,workspace | PASS |
| Projects | `projects.index` | `projects` | web,auth,verified,workspace | PASS |
| Credit Notes | `credit-notes.index` | `credit-notes` | web,auth,verified,workspace | PASS |
| Payments | `payments.index` | `payments` | web,auth,verified,workspace | PASS |
| Recurring Billing | `recurring.index` | `recurring` | web,auth,verified,workspace | PASS |
| Outstanding & Aging | `reports.aging` | `reports/aging` | web,auth,verified,workspace | PASS |
| Bank Reconciliation | `bank-reconcile.index` | `bank-reconcile` | web,auth,verified,workspace | PASS |
| Expenses | `expenses.index` | `expenses` | web,auth,verified,workspace,expense_tracking | PASS |
| Accounting Exports | `accounting-exports.index` | `accounting-exports` | web,auth,verified,workspace | PASS |
| Financial Overview | `reports.index` | `reports` | web,auth,verified,workspace | PASS |
| Cash Flow | `reports.cashflow` | `reports/cashflow` | web,auth,verified,workspace | PASS |
| Profit & Loss | `reports.pl` | `reports/pl` | web,auth,verified,workspace | PASS |
| Tax Reports | `reports.tax` | `reports/tax` | web,auth,verified,workspace | PASS |
| AI Providers | `ai-providers.index` | `ai-providers` | web,auth,verified,workspace | PASS |
| OCR Receipt | `expenses.ocr.upload` | `expenses/ocr/upload` | web,auth,verified,workspace | PASS |
| Scheduled Reports | `report-subscriptions.index` | `report-subscriptions` | web,auth,verified,workspace | PASS |
| Business Settings | `workspace.settings` | `workspace/settings` | web,auth,verified,workspace | PASS |
| Team & Roles | `team.index` | `team` | web,auth,verified,workspace | PASS |
| Invoice Templates | `invoice-layouts.index` | `invoice-layouts` | web,auth,verified,workspace,drag_drop | PASS |
| Payment Gateways | `gateways.index` | `gateways` | web,auth,verified,workspace | PASS |
| API Tokens | `api-tokens.index` | `api-tokens` | web,auth,verified,workspace,api.access | PASS |
| Webhooks Out | `tenant-webhooks.index` | `tenant-webhooks` | web,auth,verified,workspace,tenant_webhooks | PASS |
| Audit Logs | `audit-logs.index` | `audit-logs` | web,auth,verified,workspace,audit_log | PASS |
| Custom Domain | `custom-domain.show` | `workspace/custom-domain` | web,auth,verified,workspace | PASS |
| CSV Import | `clients.import.show` | `clients-import` | web,auth,verified,workspace,csv.import | PASS |
| Exports | `exports.index` | `exports` | web,auth,verified,workspace | PASS |
| Current Plan | `billing.show` | `billing` | web,auth,verified,workspace | PASS |
| Affiliate | `affiliate.index` | `affiliate` | web,auth,verified,workspace | PASS |
| Documentation | `docs` | `docs` | web | PASS |
| Support | `support.index` | `support` | web,auth,verified,workspace | PASS |
| Platform Dashboard | `admin.dashboard` | `admin` | web,auth,superadmin | PASS |
| Users | `admin.users.index` | `admin/users` | web,auth,superadmin | PASS |
| Workspaces | `admin.workspaces.index` | `admin/workspaces` | web,auth,superadmin | PASS |
| Plans | `admin.plans.index` | `admin/plans` | web,auth,superadmin | PASS |
| Subscriptions | `admin.subscriptions.index` | `admin/subscriptions` | web,auth,superadmin | PASS |
| Manual Payments | `admin.manual-payments.index` | `admin/manual-payments` | web,auth,superadmin | PASS |
| Coupons | `admin.coupons.index` | `admin/coupons` | web,auth,superadmin | PASS |
| Branding | `admin.branding.edit` | `admin/branding` | web,auth,superadmin | PASS |
| Blog Management | `admin.blog.index` | `blog/admin` | web,auth,verified,workspace,superadmin | PASS |

## Duplikat

Tidak ada menu duplikat.

## Catatan

- Tidak ada tautan placeholder sebagai fitur aktif; Coming Soon hanya di `feature.coming` + marketplace (label jujur).
- Section Superadmin hanya dirender untuk `isSuperadmin()`; backend memakai middleware `superadmin`.
- Plan-gating hanya di backend (`plan:*`); sidebar tidak menyembunyikan by plan (by design — guard ada di server).
- Dropdown toggle memakai `href="#"` + `data-bs-toggle` (pola Bootstrap valid, dikecualikan dari dead-link test).
