# UI-MIGRATION MATRIX (2026-10-10, 167 views, 132 MIGRATED)

EXEMPT = print/email/XML/JS yang memang tidak memakai design system admin (sesuai misi).

| View | Lines | route() refs | Status |
|---|---|---|---|
| `accounting-exports/create.blade.php` | 34 | 2 | MIGRATED |
| `accounting-exports/index.blade.php` | 48 | 1 | MIGRATED |
| `admin/blog/categories.blade.php` | 101 | 2 | MIGRATED |
| `admin/blog/create.blade.php` | 84 | 2 | MIGRATED |
| `admin/blog/edit.blade.php` | 87 | 1 | MIGRATED |
| `admin/blog/index.blade.php` | 63 | 2 | MIGRATED |
| `admin/branding/edit.blade.php` | 208 | 1 | MIGRATED |
| `admin/coupons/index.blade.php` | 130 | 1 | MIGRATED |
| `admin/dashboard.blade.php` | 64 | 0 | MIGRATED |
| `admin/manual-payments/index.blade.php` | 185 | 1 | MIGRATED |
| `admin/plans/_form.blade.php` | 173 | 2 | PARTIAL (no layout root) |
| `admin/plans/create.blade.php` | 10 | 1 | MIGRATED |
| `admin/plans/edit.blade.php` | 10 | 1 | MIGRATED |
| `admin/plans/index.blade.php` | 59 | 1 | MIGRATED |
| `admin/subscriptions/index.blade.php` | 84 | 1 | MIGRATED |
| `admin/subscriptions/manual.blade.php` | 323 | 3 | MIGRATED |
| `admin/subscriptions/show.blade.php` | 106 | 1 | MIGRATED |
| `admin/users/index.blade.php` | 79 | 1 | MIGRATED |
| `admin/users/show.blade.php` | 67 | 1 | MIGRATED |
| `admin/workspaces/index.blade.php` | 76 | 1 | MIGRATED |
| `admin/workspaces/show.blade.php` | 130 | 1 | MIGRATED |
| `affiliate/index.blade.php` | 29 | 0 | MIGRATED |
| `ai-providers/create.blade.php` | 97 | 2 | MIGRATED |
| `ai-providers/edit.blade.php` | 72 | 1 | MIGRATED |
| `ai-providers/index.blade.php` | 71 | 1 | MIGRATED |
| `api-tokens/index.blade.php` | 80 | 1 | MIGRATED |
| `approval-rules/index.blade.php` | 110 | 2 | MIGRATED |
| `approvals/index.blade.php` | 70 | 1 | MIGRATED |
| `audit-logs/index.blade.php` | 38 | 0 | MIGRATED |
| `auth/confirm-password.blade.php` | 19 | 1 | MIGRATED |
| `auth/forgot-password.blade.php` | 25 | 2 | MIGRATED |
| `auth/login.blade.php` | 63 | 4 | MIGRATED |
| `auth/register.blade.php` | 39 | 3 | MIGRATED |
| `auth/reset-password.blade.php` | 27 | 3 | MIGRATED |
| `auth/verify-email.blade.php` | 22 | 2 | MIGRATED |
| `bank-reconcile/create.blade.php` | 47 | 2 | MIGRATED |
| `bank-reconcile/index.blade.php` | 54 | 1 | MIGRATED |
| `bank-reconcile/show.blade.php` | 74 | 1 | MIGRATED |
| `billing/manual.blade.php` | 95 | 1 | MIGRATED |
| `billing/show.blade.php` | 169 | 2 | MIGRATED |
| `blog/feed.blade.php` | 21 | 0 | EXEMPT (print/email/xml/js) |
| `blog/index.blade.php` | 52 | 1 | MIGRATED |
| `blog/show.blade.php` | 83 | 3 | MIGRATED |
| `clients/_form.blade.php` | 78 | 0 | PARTIAL (no layout root) |
| `clients/create.blade.php` | 24 | 2 | MIGRATED |
| `clients/edit.blade.php` | 24 | 1 | MIGRATED |
| `clients/import.blade.php` | 32 | 2 | MIGRATED |
| `clients/index.blade.php` | 95 | 2 | MIGRATED |
| `clients/portal-users.blade.php` | 80 | 1 | MIGRATED |
| `clients/show.blade.php` | 123 | 1 | MIGRATED |
| `coming-soon.blade.php` | 21 | 2 | MIGRATED |
| `components/free-tier-watermark.blade.php` | 11 | 0 | EXEMPT (print watermark) |
| `components/input-error.blade.php` | 10 | 0 | PARTIAL (no layout root) |
| `components/tabler/alert.blade.php` | 46 | 0 | PARTIAL (no layout root) |
| `components/tabler/delete-button.blade.php` | 11 | 0 | PARTIAL (no layout root) |
| `components/tabler/empty.blade.php` | 13 | 0 | PARTIAL (no layout root) |
| `components/tabler/field.blade.php` | 18 | 0 | PARTIAL (no layout root) |
| `components/tabler/guest.blade.php` | 36 | 0 | PARTIAL (no layout root) |
| `components/tabler/layout.blade.php` | 269 | 6 | PARTIAL (no layout root) |
| `components/tabler/public.blade.php` | 84 | 6 | PARTIAL (no layout root) |
| `components/tabler/stat.blade.php` | 20 | 0 | PARTIAL (no layout root) |
| `components/tabler/status-badge.blade.php` | 14 | 0 | PARTIAL (no layout root) |
| `components/ti.blade.php` | 16 | 0 | PARTIAL (no layout root) |
| `coupons/index.blade.php` | 115 | 1 | MIGRATED |
| `credit-notes/create.blade.php` | 72 | 2 | MIGRATED |
| `credit-notes/index.blade.php` | 97 | 2 | MIGRATED |
| `credit-notes/show.blade.php` | 94 | 1 | MIGRATED |
| `dashboard.blade.php` | 8 | 0 | MIGRATED |
| `dashboard/customize.blade.php` | 99 | 2 | MIGRATED |
| `dashboard/index.blade.php` | 83 | 6 | MIGRATED |
| `docs.blade.php` | 278 | 1 | MIGRATED |
| `email-forwards/index.blade.php` | 62 | 1 | MIGRATED |
| `emails/invoice-reminder.blade.php` | 43 | 0 | EXEMPT (print/email/xml/js) |
| `emails/invoice.blade.php` | 102 | 0 | EXEMPT (print/email/xml/js) |
| `embed/demo.blade.php` | 51 | 0 | MIGRATED |
| `embed/invoice.blade.php` | 57 | 0 | PARTIAL (no layout root) |
| `embed/widget-js.blade.php` | 28 | 0 | EXEMPT (print/email/xml/js) |
| `errors/403.blade.php` | 17 | 1 | MIGRATED |
| `errors/404.blade.php` | 17 | 1 | MIGRATED |
| `errors/419.blade.php` | 16 | 1 | MIGRATED |
| `errors/429.blade.php` | 16 | 0 | MIGRATED |
| `errors/500.blade.php` | 17 | 1 | MIGRATED |
| `errors/503.blade.php` | 16 | 0 | MIGRATED |
| `expenses/index.blade.php` | 110 | 2 | MIGRATED |
| `expenses/ocr-review.blade.php` | 92 | 1 | MIGRATED |
| `expenses/ocr-upload.blade.php` | 32 | 2 | MIGRATED |
| `exports/index.blade.php` | 35 | 3 | MIGRATED |
| `gateways/index.blade.php` | 295 | 1 | MIGRATED |
| `invoice-layouts/builder.blade.php` | 240 | 2 | MIGRATED |
| `invoice-layouts/index.blade.php` | 50 | 1 | MIGRATED |
| `invoices/_send-modal.blade.php` | 36 | 0 | PARTIAL (no layout root) |
| `invoices/create.blade.php` | 353 | 5 | MIGRATED |
| `invoices/edit.blade.php` | 290 | 0 | MIGRATED |
| `invoices/index.blade.php` | 105 | 2 | MIGRATED |
| `invoices/pdf-custom.blade.php` | 144 | 0 | EXEMPT (print/email/xml/js) |
| `invoices/qris.blade.php` | 72 | 0 | MIGRATED |
| `invoices/reminders.blade.php` | 140 | 0 | MIGRATED |
| `invoices/show.blade.php` | 338 | 0 | MIGRATED |
| `invoices/tax-details.blade.php` | 119 | 0 | MIGRATED |
| `invoices/templates/bilingual.blade.php` | 151 | 0 | EXEMPT (print/email/xml/js) |
| `invoices/templates/bold.blade.php` | 151 | 0 | EXEMPT (print/email/xml/js) |
| `invoices/templates/classic.blade.php` | 188 | 0 | EXEMPT (print/email/xml/js) |
| `invoices/templates/elegant.blade.php` | 149 | 0 | EXEMPT (print/email/xml/js) |
| `invoices/templates/minimal.blade.php` | 133 | 0 | EXEMPT (print/email/xml/js) |
| `invoices/templates/modern.blade.php` | 162 | 0 | EXEMPT (print/email/xml/js) |
| `invoices/whatsapp-preview.blade.php` | 37 | 0 | MIGRATED |
| `license/pair-success.blade.php` | 48 | 0 | MIGRATED |
| `license/pair-wizard.blade.php` | 57 | 0 | MIGRATED |
| `marketplace/index.blade.php` | 28 | 0 | MIGRATED |
| `mobile/quick-invoice.blade.php` | 52 | 1 | MIGRATED |
| `mobile/quick-payment.blade.php` | 45 | 1 | MIGRATED |
| `notifications/index.blade.php` | 35 | 1 | MIGRATED |
| `payments/_form.blade.php` | 118 | 0 | PARTIAL (no layout root) |
| `payments/_list.blade.php` | 46 | 0 | PARTIAL (no layout root) |
| `payments/index.blade.php` | 111 | 1 | MIGRATED |
| `portal/dashboard.blade.php` | 74 | 4 | MIGRATED |
| `portal/invoices.blade.php` | 59 | 3 | MIGRATED |
| `portal/login.blade.php` | 34 | 1 | MIGRATED |
| `portal/quotes.blade.php` | 50 | 3 | MIGRATED |
| `pricing/compare.blade.php` | 83 | 2 | MIGRATED |
| `pricing/index.blade.php` | 137 | 1 | MIGRATED |
| `products/_form.blade.php` | 43 | 0 | PARTIAL (no layout root) |
| `products/create.blade.php` | 24 | 2 | MIGRATED |
| `products/edit.blade.php` | 24 | 1 | MIGRATED |
| `products/index.blade.php` | 116 | 2 | MIGRATED |
| `profile/edit.blade.php` | 25 | 0 | MIGRATED |
| `profile/partials/delete-user-form.blade.php` | 50 | 1 | PARTIAL (no layout root) |
| `profile/partials/update-password-form.blade.php` | 32 | 1 | PARTIAL (no layout root) |
| `profile/partials/update-profile-information-form.blade.php` | 44 | 2 | PARTIAL (no layout root) |
| `projects/create.blade.php` | 86 | 2 | MIGRATED |
| `projects/edit.blade.php` | 86 | 0 | MIGRATED |
| `projects/index.blade.php` | 95 | 2 | MIGRATED |
| `projects/show.blade.php` | 136 | 1 | MIGRATED |
| `public/invoice.blade.php` | 231 | 0 | MIGRATED |
| `public/nps/show.blade.php` | 46 | 0 | MIGRATED |
| `public/nps/thanks.blade.php` | 22 | 0 | MIGRATED |
| `public/quotes/show.blade.php` | 245 | 0 | MIGRATED |
| `quotes/create.blade.php` | 89 | 2 | MIGRATED |
| `quotes/index.blade.php` | 56 | 1 | MIGRATED |
| `quotes/show.blade.php` | 78 | 1 | MIGRATED |
| `recurring/create.blade.php` | 160 | 2 | MIGRATED |
| `recurring/edit.blade.php` | 156 | 1 | MIGRATED |
| `recurring/index.blade.php` | 87 | 2 | MIGRATED |
| `report-subscriptions/index.blade.php` | 85 | 1 | MIGRATED |
| `reports/aging.blade.php` | 167 | 1 | MIGRATED |
| `reports/cashflow.blade.php` | 99 | 1 | MIGRATED |
| `reports/index.blade.php` | 124 | 0 | MIGRATED |
| `reports/pl.blade.php` | 90 | 1 | MIGRATED |
| `reports/tax.blade.php` | 104 | 1 | MIGRATED |
| `robots.blade.php` | 28 | 0 | EXEMPT (print/email/xml/js) |
| `seo/alternatives.blade.php` | 106 | 2 | MIGRATED |
| `seo/best-tools.blade.php` | 104 | 2 | MIGRATED |
| `seo/compare.blade.php` | 131 | 2 | MIGRATED |
| `seo/templates.blade.php` | 114 | 2 | MIGRATED |
| `settings/2fa/setup.blade.php` | 70 | 1 | MIGRATED |
| `settings/2fa/show.blade.php` | 82 | 3 | MIGRATED |
| `settings/custom-domain.blade.php` | 101 | 3 | MIGRATED |
| `sitemap.blade.php` | 59 | 0 | EXEMPT (print/email/xml/js) |
| `support/create.blade.php` | 30 | 2 | MIGRATED |
| `support/index.blade.php` | 44 | 1 | MIGRATED |
| `support/show.blade.php` | 44 | 1 | MIGRATED |
| `team/index.blade.php` | 114 | 1 | MIGRATED |
| `tenant-webhooks/index.blade.php` | 77 | 1 | MIGRATED |
| `time-entries/index.blade.php` | 97 | 1 | MIGRATED |
| `welcome.blade.php` | 163 | 5 | MIGRATED |
| `workspace/create.blade.php` | 36 | 1 | MIGRATED |
| `workspace/settings.blade.php` | 154 | 2 | MIGRATED |
