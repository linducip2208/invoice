# ROUTE MAP (generated 2026-10-10, 310 routes)

## // (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `/` | home | web |

## /__pair (3)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `__pair` | pair.show | web |
| POST | `__pair` | pair.activate | web |
| GET|HEAD | `__pair/success` | pair.success | web |

## /accounting-exports (5)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `accounting-exports` | accounting-exports.index | web,auth,verified,workspace |
| POST | `accounting-exports` | accounting-exports.store | web,auth,verified,workspace |
| GET|HEAD | `accounting-exports/create` | accounting-exports.create | web,auth,verified,workspace |
| DELETE | `accounting-exports/{export}` | accounting-exports.destroy | web,auth,verified,workspace |
| GET|HEAD | `accounting-exports/{export}/download` | accounting-exports.download | web,auth,verified,workspace |

## /admin (31)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `admin` | admin.dashboard | web,auth,superadmin |
| GET|HEAD | `admin/branding` | admin.branding.edit | web,auth,superadmin |
| POST | `admin/branding` | admin.branding.update | web,auth,superadmin |
| GET|HEAD | `admin/coupons` | admin.coupons.index | web,auth,superadmin |
| POST | `admin/coupons` | admin.coupons.store | web,auth,superadmin |
| DELETE | `admin/coupons/{coupon}` | admin.coupons.destroy | web,auth,superadmin |
| POST | `admin/coupons/{coupon}/toggle` | admin.coupons.toggle | web,auth,superadmin |
| GET|HEAD | `admin/manual-payments` | admin.manual-payments.index | web,auth,superadmin |
| POST | `admin/manual-payments/methods` | admin.manual-payments.methods.store | web,auth,superadmin |
| DELETE | `admin/manual-payments/methods/{method}` | admin.manual-payments.methods.destroy | web,auth,superadmin |
| POST | `admin/manual-payments/{subscription}/approve` | admin.manual-payments.approve | web,auth,superadmin |
| POST | `admin/manual-payments/{subscription}/reject` | admin.manual-payments.reject | web,auth,superadmin |
| GET|HEAD | `admin/plans` | admin.plans.index | web,auth,superadmin |
| POST | `admin/plans` | admin.plans.store | web,auth,superadmin |
| GET|HEAD | `admin/plans/create` | admin.plans.create | web,auth,superadmin |
| GET|HEAD | `admin/plans/{plan}` | admin.plans.show | web,auth,superadmin |
| PUT|PATCH | `admin/plans/{plan}` | admin.plans.update | web,auth,superadmin |
| DELETE | `admin/plans/{plan}` | admin.plans.destroy | web,auth,superadmin |
| GET|HEAD | `admin/plans/{plan}/edit` | admin.plans.edit | web,auth,superadmin |
| GET|HEAD | `admin/subscriptions` | admin.subscriptions.index | web,auth,superadmin |
| GET|HEAD | `admin/subscriptions-manual` | admin.subscriptions.manual | web,auth,superadmin |
| POST | `admin/subscriptions-manual` | admin.subscriptions.manual.store | web,auth,superadmin |
| POST | `admin/subscriptions-manual/create-with-user` | admin.subscriptions.manual.create | web,auth,superadmin |
| GET|HEAD | `admin/subscriptions/{subscription}` | admin.subscriptions.show | web,auth,superadmin |
| PUT|PATCH | `admin/subscriptions/{subscription}` | admin.subscriptions.update | web,auth,superadmin |
| GET|HEAD | `admin/users` | admin.users.index | web,auth,superadmin |
| GET|HEAD | `admin/users/{user}` | admin.users.show | web,auth,superadmin |
| DELETE | `admin/users/{user}` | admin.users.destroy | web,auth,superadmin |
| GET|HEAD | `admin/workspaces` | admin.workspaces.index | web,auth,superadmin |
| GET|HEAD | `admin/workspaces/{workspace}` | admin.workspaces.show | web,auth,superadmin |
| POST | `admin/workspaces/{workspace}/assign-plan` | admin.workspaces.assign-plan | web,auth,superadmin |

## /affiliate (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `affiliate` | affiliate.index | web,auth,verified,workspace |

## /ai-providers (7)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `ai-providers` | ai-providers.index | web,auth,verified,workspace |
| POST | `ai-providers` | ai-providers.store | web,auth,verified,workspace |
| GET|HEAD | `ai-providers/create` | ai-providers.create | web,auth,verified,workspace |
| PUT|PATCH | `ai-providers/{provider}` | ai-providers.update | web,auth,verified,workspace |
| DELETE | `ai-providers/{provider}` | ai-providers.destroy | web,auth,verified,workspace |
| GET|HEAD | `ai-providers/{provider}/edit` | ai-providers.edit | web,auth,verified,workspace |
| POST | `ai-providers/{provider}/test` | ai-providers.test | web,auth,verified,workspace |

## /alternatives-to-{slug} (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `alternatives-to-{slug}` | seo.alternatives | web |

## /api (3)

| Method | URI | Name | Middleware |
|---|---|---|---|
| POST | `api/ai/description/expand` | api.ai.description.expand | web,auth,verified,workspace |
| POST | `api/ai/expense/categorize` | api.ai.expense.categorize | web,auth,verified,workspace |
| POST | `api/npwp/validate` | api.npwp.validate | web,auth,verified,workspace |

## /api-tokens (3)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `api-tokens` | api-tokens.index | web,auth,verified,workspace,api.access |
| POST | `api-tokens` | api-tokens.store | web,auth,verified,workspace,api.access |
| DELETE | `api-tokens/{apiToken}` | api-tokens.destroy | web,auth,verified,workspace,api.access |

## /approval-rules (4)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `approval-rules` | approval-rules.index | web,auth,verified,workspace |
| POST | `approval-rules` | approval-rules.store | web,auth,verified,workspace |
| PUT | `approval-rules/{rule}` | approval-rules.update | web,auth,verified,workspace |
| DELETE | `approval-rules/{rule}` | approval-rules.destroy | web,auth,verified,workspace |

## /approvals (3)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `approvals` | approvals.index | web,auth,verified,workspace |
| POST | `approvals/{approvalRequest}/approve` | approvals.approve | web,auth,verified,workspace |
| POST | `approvals/{approvalRequest}/reject` | approvals.reject | web,auth,verified,workspace |

## /audit-logs (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `audit-logs` | audit-logs.index | web,auth,verified,workspace,audit_log |

## /auth (2)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `auth/sso/{provider}/callback` | sso.callback | web |
| GET|HEAD | `auth/sso/{provider}/redirect` | sso.redirect | web |

## /bank-reconcile (6)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `bank-reconcile` | bank-reconcile.index | web,auth,verified,workspace |
| POST | `bank-reconcile` | bank-reconcile.store | web,auth,verified,workspace |
| GET|HEAD | `bank-reconcile/create` | bank-reconcile.create | web,auth,verified,workspace |
| GET|HEAD | `bank-reconcile/{batch}` | bank-reconcile.show | web,auth,verified,workspace |
| POST | `bank-reconcile/{batch}/finalize` | bank-reconcile.finalize | web,auth,verified,workspace |
| POST | `bank-reconcile/{batch}/match` | bank-reconcile.match | web,auth,verified,workspace |

## /best-invoicing-tools-{year} (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `best-invoicing-tools-{year}` | seo.best-tools | web |

## /billing (6)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `billing` | billing.show | web,auth,verified,workspace |
| POST | `billing/cancel` | billing.cancel | web,auth,verified,workspace |
| GET|HEAD | `billing/manual/{subscription}` | billing.manual | web,auth,verified,workspace |
| POST | `billing/manual/{subscription}/proof` | billing.manual.proof | web,auth,verified,workspace |
| POST | `billing/resume` | billing.resume | web,auth,verified,workspace |
| POST | `billing/subscribe/{plan}` | billing.subscribe | web,auth,verified,workspace |

## /blog (14)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `blog` | blog.index | web |
| GET|HEAD | `blog/admin` | admin.blog.index | web,auth,verified,workspace,superadmin |
| POST | `blog/admin` | admin.blog.store | web,auth,verified,workspace,superadmin |
| GET|HEAD | `blog/admin/categories` | admin.blog.categories | web,auth,verified,workspace,superadmin |
| POST | `blog/admin/categories` | admin.blog.categories.store | web,auth,verified,workspace,superadmin |
| PUT | `blog/admin/categories/{category}` | admin.blog.categories.update | web,auth,verified,workspace,superadmin |
| DELETE | `blog/admin/categories/{category}` | admin.blog.categories.destroy | web,auth,verified,workspace,superadmin |
| GET|HEAD | `blog/admin/create` | admin.blog.create | web,auth,verified,workspace,superadmin |
| PUT | `blog/admin/{post}` | admin.blog.update | web,auth,verified,workspace,superadmin |
| DELETE | `blog/admin/{post}` | admin.blog.destroy | web,auth,verified,workspace,superadmin |
| GET|HEAD | `blog/admin/{post}/edit` | admin.blog.edit | web,auth,verified,workspace,superadmin |
| GET|HEAD | `blog/category/{slug}` | blog.category | web |
| GET|HEAD | `blog/feed.xml` | blog.feed | web |
| GET|HEAD | `blog/{slug}` | blog.show | web |

## /clients (9)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `clients` | clients.index | web,auth,verified,workspace |
| POST | `clients` | clients.store | web,auth,verified,workspace,client.create |
| GET|HEAD | `clients/create` | clients.create | web,auth,verified,workspace |
| GET|HEAD | `clients/{client}` | clients.show | web,auth,verified,workspace |
| PUT|PATCH | `clients/{client}` | clients.update | web,auth,verified,workspace |
| DELETE | `clients/{client}` | clients.destroy | web,auth,verified,workspace |
| GET|HEAD | `clients/{client}/edit` | clients.edit | web,auth,verified,workspace |
| GET|HEAD | `clients/{client}/portal-users` | clients.portal-users.show | web,auth,verified,workspace |
| POST | `clients/{client}/portal-users` | clients.portal-users.invite | web,auth,verified,workspace |

## /clients-import (2)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `clients-import` | clients.import.show | web,auth,verified,workspace,csv.import |
| POST | `clients-import` | clients.import | web,auth,verified,workspace,csv.import |

## /compare (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `compare/{a}-vs-{b}` | seo.compare | web |

## /confirm-password (2)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `confirm-password` | password.confirm | web,auth |
| POST | `confirm-password` | - | web,auth |

## /coupons (4)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `coupons` | coupons.index | web,auth,verified,workspace |
| POST | `coupons` | coupons.store | web,auth,verified,workspace |
| DELETE | `coupons/{coupon}` | coupons.destroy | web,auth,verified,workspace |
| POST | `coupons/{coupon}/toggle` | coupons.toggle | web,auth,verified,workspace |

## /credit-notes (8)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `credit-notes` | credit-notes.index | web,auth,verified,workspace |
| POST | `credit-notes` | credit-notes.store | web,auth,verified,workspace |
| GET|HEAD | `credit-notes/create` | credit-notes.create | web,auth,verified,workspace |
| GET|HEAD | `credit-notes/{credit_note}` | credit-notes.show | web,auth,verified,workspace |
| DELETE | `credit-notes/{credit_note}` | credit-notes.destroy | web,auth,verified,workspace |
| POST | `credit-notes/{credit_note}/apply` | credit-notes.apply | web,auth,verified,workspace |
| POST | `credit-notes/{credit_note}/cancel` | credit-notes.cancel | web,auth,verified,workspace |
| POST | `credit-notes/{credit_note}/issue` | credit-notes.issue | web,auth,verified,workspace |

## /dashboard (6)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `dashboard` | dashboard | web,auth,verified,workspace |
| GET|HEAD | `dashboard/customize` | dashboard.customize | web,auth,verified,workspace |
| POST | `dashboard/widgets` | dashboard.widgets.store | web,auth,verified,workspace |
| PUT | `dashboard/widgets/reorder` | dashboard.widgets.reorder | web,auth,verified,workspace |
| PUT | `dashboard/widgets/{widget}` | dashboard.widgets.update | web,auth,verified,workspace |
| DELETE | `dashboard/widgets/{widget}` | dashboard.widgets.destroy | web,auth,verified,workspace |

## /docs (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `docs` | docs | web |

## /email (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| POST | `email/verification-notification` | verification.send | web,auth,6,1 |

## /email-forwards (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `email-forwards` | email-forwards.index | web,auth,verified,workspace |

## /embed (2)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `embed/demo` | embed.demo | web,auth,verified,workspace |
| GET|HEAD | `embed/invoice/{token}` | embed.invoice | web |

## /expenses (7)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `expenses` | expenses.index | web,auth,verified,workspace,expense_tracking |
| POST | `expenses` | expenses.store | web,auth,verified,workspace,expense_tracking |
| POST | `expenses/ocr/process` | expenses.ocr.process | web,auth,verified,workspace |
| GET|HEAD | `expenses/ocr/upload` | expenses.ocr.upload | web,auth,verified,workspace |
| DELETE | `expenses/{expense}` | expenses.destroy | web,auth,verified,workspace,expense_tracking |
| PUT | `expenses/{expense}/ocr-confirm` | expenses.ocr.confirm | web,auth,verified,workspace |
| GET|HEAD | `expenses/{expense}/ocr-review` | expenses.ocr.review | web,auth,verified,workspace |

## /exports (4)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `exports` | exports.index | web,auth,verified,workspace |
| GET|HEAD | `exports/clients.csv` | exports.clients | web,auth,verified,workspace |
| GET|HEAD | `exports/invoices.csv` | exports.invoices | web,auth,verified,workspace |
| GET|HEAD | `exports/payments.csv` | exports.payments | web,auth,verified,workspace |

## /feature (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `feature/{feature}` | feature.coming | web,auth,verified,workspace |

## /forgot-password (2)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `forgot-password` | password.request | web,guest |
| POST | `forgot-password` | password.email | web,guest,auth-email |

## /gateways (5)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `gateways` | gateways.index | web,auth,verified,workspace |
| POST | `gateways` | gateways.store | web,auth,verified,workspace |
| PUT|PATCH | `gateways/{gateway}` | gateways.update | web,auth,verified,workspace |
| DELETE | `gateways/{gateway}` | gateways.destroy | web,auth,verified,workspace |
| POST | `gateways/{gateway}/toggle` | gateways.toggle | web,auth,verified,workspace |

## /i (2)

| Method | URI | Name | Middleware |
|---|---|---|---|
| POST | `i/{invoice}/bayar` | payments.gateway | web |
| GET|HEAD | `i/{token}` | public.invoice.show | web,public-invoice |

## /invoice-attachments (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| DELETE | `invoice-attachments/{attachment}` | invoices.attachments.destroy | web,auth,verified,workspace |

## /invoice-layouts (6)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `invoice-layouts` | invoice-layouts.index | web,auth,verified,workspace,drag_drop |
| POST | `invoice-layouts` | invoice-layouts.store | web,auth,verified,workspace,drag_drop |
| GET|HEAD | `invoice-layouts/create` | invoice-layouts.create | web,auth,verified,workspace,drag_drop |
| PUT|PATCH | `invoice-layouts/{invoice_layout}` | invoice-layouts.update | web,auth,verified,workspace,drag_drop |
| DELETE | `invoice-layouts/{invoice_layout}` | invoice-layouts.destroy | web,auth,verified,workspace,drag_drop |
| GET|HEAD | `invoice-layouts/{invoice_layout}/edit` | invoice-layouts.edit | web,auth,verified,workspace,drag_drop |

## /invoice-templates (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `invoice-templates` | seo.templates | web |

## /invoices (32)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `invoices` | invoices.index | web,auth,verified,workspace |
| POST | `invoices` | invoices.store | web,auth,verified,workspace,invoice.create |
| DELETE | `invoices/comments/{comment}` | invoices.comments.destroy | web,auth,verified,workspace |
| GET|HEAD | `invoices/create` | invoices.create | web,auth,verified,workspace,invoice.create |
| DELETE | `invoices/late-fees/{fee}` | invoices.late-fees.destroy | web,auth,verified,workspace |
| DELETE | `invoices/reminders/{reminder}` | invoices.reminders.cancel | web,auth,verified,workspace |
| GET|HEAD | `invoices/{invoice}` | invoices.show | web,auth,verified,workspace |
| PUT|PATCH | `invoices/{invoice}` | invoices.update | web,auth,verified,workspace,invoice.edit |
| DELETE | `invoices/{invoice}` | invoices.destroy | web,auth,verified,workspace,invoice.delete |
| POST | `invoices/{invoice}/attachments` | invoices.attachments.store | web,auth,verified,workspace |
| POST | `invoices/{invoice}/cancel` | invoices.cancel | web,auth,verified,workspace |
| POST | `invoices/{invoice}/comments` | invoices.comments.store | web,auth,verified,workspace |
| POST | `invoices/{invoice}/duplicate` | invoices.duplicate | web,auth,verified,workspace,invoice.create |
| GET|HEAD | `invoices/{invoice}/edit` | invoices.edit | web,auth,verified,workspace,invoice.edit |
| POST | `invoices/{invoice}/efaktur/generate` | invoices.efaktur.generate | web,auth,verified,workspace |
| GET|HEAD | `invoices/{invoice}/efaktur/xml` | invoices.efaktur.xml | web,auth,verified,workspace |
| POST | `invoices/{invoice}/late-fees/apply` | invoices.late-fees.apply | web,auth,verified,workspace |
| POST | `invoices/{invoice}/mark-sent` | invoices.mark-sent | web,auth,verified,workspace |
| GET|HEAD | `invoices/{invoice}/pdf` | invoices.pdf | web,auth,verified,workspace |
| POST | `invoices/{invoice}/qris/generate` | invoices.qris.generate | web,auth,verified,workspace |
| GET|HEAD | `invoices/{invoice}/qris/{qris}` | invoices.qris.show | web,auth,verified,workspace |
| GET|HEAD | `invoices/{invoice}/reminders` | invoices.reminders.index | web,auth,verified,workspace |
| POST | `invoices/{invoice}/reminders` | invoices.reminders.store | web,auth,verified,workspace |
| POST | `invoices/{invoice}/reminders/schedule` | invoices.reminders.schedule | web,auth,verified,workspace |
| POST | `invoices/{invoice}/rotate-link` | invoices.rotate-link | web,auth,verified,workspace |
| POST | `invoices/{invoice}/send` | invoices.send | web,auth,verified,workspace |
| POST | `invoices/{invoice}/status` | invoices.update-status | web,auth,verified,workspace |
| GET|HEAD | `invoices/{invoice}/tax-details` | invoices.tax-details.edit | web,auth,verified,workspace |
| PUT | `invoices/{invoice}/tax-details` | invoices.tax-details.update | web,auth,verified,workspace |
| POST | `invoices/{invoice}/translate` | invoices.translate | web,auth,verified,workspace |
| GET|HEAD | `invoices/{invoice}/whatsapp` | invoices.whatsapp.preview | web,auth,verified,workspace |
| POST | `invoices/{invoice}/whatsapp` | invoices.whatsapp.send | web,auth,verified,workspace |

## /login (2)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `login` | login | web,guest |
| POST | `login` | - | web,guest |

## /logout (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| POST | `logout` | logout | web,auth |

## /m (4)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `m/quick/invoice` | mobile.quick-invoice | web,auth,verified,workspace |
| POST | `m/quick/invoice` | mobile.quick-invoice.store | web,auth,verified,workspace |
| GET|HEAD | `m/quick/payment` | mobile.quick-payment | web,auth,verified,workspace |
| POST | `m/quick/payment` | mobile.quick-payment.store | web,auth,verified,workspace |

## /marketplace (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `marketplace` | marketplace.index | web,auth,verified,workspace |

## /notifications (3)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `notifications` | notifications.index | web,auth,verified,workspace |
| DELETE | `notifications` | notifications.clear | web,auth,verified,workspace |
| POST | `notifications/{notification}/read` | notifications.read | web,auth,verified,workspace |

## /nps (2)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `nps/{token}` | nps.show | web |
| POST | `nps/{token}` | nps.submit | web |

## /password (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| PUT | `password` | password.update | web,auth |

## /payments (3)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `payments` | payments.index | web,auth,verified,workspace |
| POST | `payments/invoice/{invoice}` | payments.store | web,auth,verified,workspace |
| DELETE | `payments/{payment}` | payments.destroy | web,auth,verified,workspace |

## /portal (6)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `portal` | portal.dashboard | web,client_portal,portal.active |
| GET|HEAD | `portal/invoices` | portal.invoices | web,client_portal,portal.active |
| GET|HEAD | `portal/login` | portal.login | web |
| POST | `portal/login` | portal.login.submit | web,portal-login |
| POST | `portal/logout` | portal.logout | web,client_portal,portal.active |
| GET|HEAD | `portal/quotes` | portal.quotes | web,client_portal,portal.active |

## /portal-users (3)

| Method | URI | Name | Middleware |
|---|---|---|---|
| DELETE | `portal-users/{portalUser}` | portal-users.destroy | web,auth,verified,workspace |
| POST | `portal-users/{portalUser}/toggle-active` | portal-users.toggle-active | web,auth,verified,workspace |
| POST | `portal-users/{portalUser}/toggle-approve` | portal-users.toggle-approve | web,auth,verified,workspace |

## /pricing (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `pricing` | pricing | web |

## /products (6)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `products` | products.index | web,auth,verified,workspace |
| POST | `products` | products.store | web,auth,verified,workspace |
| GET|HEAD | `products/create` | products.create | web,auth,verified,workspace |
| PUT|PATCH | `products/{product}` | products.update | web,auth,verified,workspace |
| DELETE | `products/{product}` | products.destroy | web,auth,verified,workspace |
| GET|HEAD | `products/{product}/edit` | products.edit | web,auth,verified,workspace |

## /profile (8)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `profile` | profile.edit | web,auth |
| PATCH | `profile` | profile.update | web,auth |
| DELETE | `profile` | profile.destroy | web,auth |
| GET|HEAD | `profile/2fa` | 2fa.show | web,auth,verified,workspace |
| POST | `profile/2fa/confirm` | 2fa.confirm | web,auth,verified,workspace |
| POST | `profile/2fa/disable` | 2fa.disable | web,auth,verified,workspace |
| POST | `profile/2fa/enable` | 2fa.enable | web,auth,verified,workspace |
| POST | `profile/2fa/recovery-codes` | 2fa.recovery-codes | web,auth,verified,workspace |

## /projects (7)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `projects` | projects.index | web,auth,verified,workspace |
| POST | `projects` | projects.store | web,auth,verified,workspace |
| GET|HEAD | `projects/create` | projects.create | web,auth,verified,workspace |
| GET|HEAD | `projects/{project}` | projects.show | web,auth,verified,workspace |
| PUT|PATCH | `projects/{project}` | projects.update | web,auth,verified,workspace |
| DELETE | `projects/{project}` | projects.destroy | web,auth,verified,workspace |
| GET|HEAD | `projects/{project}/edit` | projects.edit | web,auth,verified,workspace |

## /q (6)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `q/{token}` | public.quote.show | web,quote-public |
| GET|HEAD | `q/{token}/confirm` | public.quote.confirm | web,quote-public |
| POST | `q/{token}/confirm` | public.quote.confirm.post | web,quote-confirm |
| POST | `q/{token}/otp` | public.quote.otp.request | web,quote-otp |
| GET|HEAD | `q/{token}/verify` | public.quote.verify-form | web,quote-public |
| POST | `q/{token}/verify` | public.quote.verify | web,quote-verify |

## /qris (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| POST | `qris/{qris}/cancel` | invoices.qris.cancel | web,auth,verified,workspace |

## /quotes (10)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `quotes` | quotes.index | web,auth,verified,workspace |
| POST | `quotes` | quotes.store | web,auth,verified,workspace |
| GET|HEAD | `quotes/create` | quotes.create | web,auth,verified,workspace |
| GET|HEAD | `quotes/{quote}` | quotes.show | web,auth,verified,workspace |
| DELETE | `quotes/{quote}` | quotes.destroy | web,auth,verified,workspace |
| POST | `quotes/{quote}/convert` | quotes.convert | web,auth,verified,workspace |
| POST | `quotes/{quote}/reopen` | quotes.reopen | web,auth,verified,workspace |
| POST | `quotes/{quote}/rotate-link` | quotes.rotate-link | web,auth,verified,workspace |
| POST | `quotes/{quote}/send` | quotes.send | web,auth,verified,workspace |
| POST | `quotes/{quote}/withdraw` | quotes.withdraw | web,auth,verified,workspace |

## /recurring (7)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `recurring` | recurring.index | web,auth,verified,workspace |
| POST | `recurring` | recurring.store | web,auth,verified,workspace,recurring.create |
| GET|HEAD | `recurring/create` | recurring.create | web,auth,verified,workspace,recurring.create |
| PUT|PATCH | `recurring/{recurring}` | recurring.update | web,auth,verified,workspace,recurring.create |
| DELETE | `recurring/{recurring}` | recurring.destroy | web,auth,verified,workspace |
| GET|HEAD | `recurring/{recurring}/edit` | recurring.edit | web,auth,verified,workspace,recurring.create |
| POST | `recurring/{recurring}/toggle` | recurring.toggle | web,auth,verified,workspace |

## /register (2)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `register` | register | web,guest |
| POST | `register` | - | web,guest,auth-register |

## /report-subscriptions (4)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `report-subscriptions` | report-subscriptions.index | web,auth,verified,workspace |
| POST | `report-subscriptions` | report-subscriptions.store | web,auth,verified,workspace |
| DELETE | `report-subscriptions/{sub}` | report-subscriptions.destroy | web,auth,verified,workspace |
| POST | `report-subscriptions/{sub}/toggle` | report-subscriptions.toggle | web,auth,verified,workspace |

## /reports (5)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `reports` | reports.index | web,auth,verified,workspace |
| GET|HEAD | `reports/aging` | reports.aging | web,auth,verified,workspace |
| GET|HEAD | `reports/cashflow` | reports.cashflow | web,auth,verified,workspace |
| GET|HEAD | `reports/pl` | reports.pl | web,auth,verified,workspace |
| GET|HEAD | `reports/tax` | reports.tax | web,auth,verified,workspace |

## /reset-password (2)

| Method | URI | Name | Middleware |
|---|---|---|---|
| POST | `reset-password` | password.store | web,guest |
| GET|HEAD | `reset-password/{token}` | password.reset | web,guest |

## /robots.txt (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `robots.txt` | - | web |

## /sitemap.xml (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `sitemap.xml` | - | web |

## /storage (2)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `storage/{path}` | storage.local |  |
| PUT | `storage/{path}` | storage.local.upload |  |

## /support (5)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `support` | support.index | web,auth,verified,workspace |
| POST | `support` | support.store | web,auth,verified,workspace |
| GET|HEAD | `support/create` | support.create | web,auth,verified,workspace |
| GET|HEAD | `support/{ticket}` | support.show | web,auth,verified,workspace |
| POST | `support/{ticket}/reply` | support.reply | web,auth,verified,workspace |

## /team (3)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `team` | team.index | web,auth,verified,workspace |
| POST | `team/invite` | team.invite | web,auth,verified,workspace |
| DELETE | `team/invite/{invite}` | team.invite.revoke | web,auth,verified,workspace |

## /tenant-webhooks (4)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `tenant-webhooks` | tenant-webhooks.index | web,auth,verified,workspace,tenant_webhooks |
| POST | `tenant-webhooks` | tenant-webhooks.store | web,auth,verified,workspace,tenant_webhooks |
| DELETE | `tenant-webhooks/{tenantWebhook}` | tenant-webhooks.destroy | web,auth,verified,workspace,tenant_webhooks |
| POST | `tenant-webhooks/{tenantWebhook}/toggle` | tenant-webhooks.toggle | web,auth,verified,workspace,tenant_webhooks |

## /time-entries (3)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `time-entries` | time-entries.index | web,auth,verified,workspace,time_tracking |
| POST | `time-entries` | time-entries.store | web,auth,verified,workspace,time_tracking |
| DELETE | `time-entries/{timeEntry}` | time-entries.destroy | web,auth,verified,workspace,time_tracking |

## /up (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `up` | - |  |

## /verify-email (2)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `verify-email` | verification.notice | web,auth |
| GET|HEAD | `verify-email/{id}/{hash}` | verification.verify | web,auth,signed,6,1 |

## /webhooks (3)

| Method | URI | Name | Middleware |
|---|---|---|---|
| POST | `webhooks/billing/{gateway}` | webhooks.billing | web |
| POST | `webhooks/email-inbox` | webhooks.email-inbox | web |
| POST | `webhooks/payment/{gateway}` | webhooks.payment | web |

## /widget (1)

| Method | URI | Name | Middleware |
|---|---|---|---|
| GET|HEAD | `widget/invocely.js` | embed.widget-js | web |

## /workspace (9)

| Method | URI | Name | Middleware |
|---|---|---|---|
| POST | `workspace` | workspace.store | web,auth,verified |
| GET|HEAD | `workspace/create` | workspace.create | web,auth,verified |
| GET|HEAD | `workspace/custom-domain` | custom-domain.show | web,auth,verified,workspace |
| POST | `workspace/custom-domain` | custom-domain.store | web,auth,verified,workspace |
| DELETE | `workspace/custom-domain` | custom-domain.destroy | web,auth,verified,workspace |
| POST | `workspace/custom-domain/verify` | custom-domain.verify | web,auth,verified,workspace |
| GET|HEAD | `workspace/settings` | workspace.settings | web,auth,verified,workspace |
| PATCH | `workspace/settings` | workspace.update | web,auth,verified,workspace |
| POST | `workspace/switch/{workspace}` | workspace.switch | web,auth,verified,workspace |

