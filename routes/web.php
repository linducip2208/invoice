<?php

use App\Http\Controllers\Admin\AdminBrandingController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminManualPaymentController;
use App\Http\Controllers\Admin\AdminPlanController;
use App\Http\Controllers\Admin\AdminSubscriptionController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminWorkspaceController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\BillingWebhookController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientImportController;
use App\Http\Controllers\ComingSoonController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TenantWebhookController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailInvoiceController;
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\InvoiceAttachmentController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceCommentController;
use App\Http\Controllers\InvoiceLateFeeController;
use App\Http\Controllers\InvoiceReminderController;
use App\Http\Controllers\NpsController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Settings\AiProviderController;
use App\Http\Controllers\Settings\CustomDomainController;
use App\Http\Controllers\Settings\TwoFactorController;
use App\Http\Controllers\InvoiceLayoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlanComparisonController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgrammaticSeoController;
use App\Http\Controllers\PublicInvoiceController;
use App\Http\Controllers\RecurringInvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceSwitcherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // First-workspace creation for users without any workspace.
    // MUST stay outside the `workspace` middleware group to avoid a
    // redirect loop (EnsureWorkspaceExists redirects here).
    Route::get('/workspace/create', [WorkspaceController::class, 'create'])->name('workspace.create');
    Route::post('/workspace', [WorkspaceController::class, 'store'])->name('workspace.store');
});

Route::middleware(['auth', 'verified', 'workspace'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/workspace/settings', [WorkspaceController::class, 'edit'])->name('workspace.settings');
    Route::patch('/workspace/settings', [WorkspaceController::class, 'update'])->name('workspace.update');
    Route::post('/workspace/switch/{workspace}', [WorkspaceSwitcherController::class, 'switch'])->name('workspace.switch');

    // Clients (create gated by plan limit)
    Route::resource('clients', ClientController::class)->except(['store']);
    Route::post('clients', [ClientController::class, 'store'])->middleware('plan:client.create')->name('clients.store');

    Route::resource('products', ProductController::class)->except(['show']);

    // Recurring (entire feature gated to Pro+)
    Route::get('recurring', [RecurringInvoiceController::class, 'index'])->name('recurring.index');
    Route::middleware('plan:recurring.create')->group(function () {
        Route::get('recurring/create',         [RecurringInvoiceController::class, 'create'])->name('recurring.create');
        Route::post('recurring',               [RecurringInvoiceController::class, 'store'])->name('recurring.store');
        Route::get('recurring/{recurring}/edit', [RecurringInvoiceController::class, 'edit'])->name('recurring.edit');
        Route::match(['put', 'patch'], 'recurring/{recurring}', [RecurringInvoiceController::class, 'update'])->name('recurring.update');
    });
    Route::delete('recurring/{recurring}', [RecurringInvoiceController::class, 'destroy'])->name('recurring.destroy');
    Route::post('recurring/{recurring}/toggle', [RecurringInvoiceController::class, 'toggle'])->name('recurring.toggle');

    // Invoices (create/edit/delete gated by plan)
    Route::get('invoices',              [InvoiceController::class, 'index'])->name('invoices.index');
    Route::middleware('plan:invoice.create')->group(function () {
        Route::get('invoices/create',   [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('invoices',         [InvoiceController::class, 'store'])->name('invoices.store');
    });
    Route::get('invoices/{invoice}',    [InvoiceController::class, 'show'])->name('invoices.show');
    Route::middleware('plan:invoice.edit')->group(function () {
        Route::get('invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::match(['put', 'patch'], 'invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    });
    Route::middleware('plan:invoice.delete')->delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::post('invoices/{invoice}/duplicate', [InvoiceController::class, 'duplicate'])->middleware('plan:invoice.create')->name('invoices.duplicate');
    Route::post('invoices/{invoice}/status',     [InvoiceController::class, 'updateStatus'])->name('invoices.update-status');
    Route::post('invoices/{invoice}/mark-sent',  [InvoiceController::class, 'markAsSent'])->name('invoices.mark-sent');
    Route::post('invoices/{invoice}/cancel',     [InvoiceController::class, 'markAsCancelled'])->name('invoices.cancel');
    Route::post('invoices/{invoice}/rotate-link', [InvoiceController::class, 'rotateLink'])->name('invoices.rotate-link');
    Route::post('invoices/{invoice}/send',       [EmailInvoiceController::class, 'send'])->name('invoices.send');
    Route::get('invoices/{invoice}/pdf',         [InvoiceController::class, 'pdf'])->name('invoices.pdf');

    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments/invoice/{invoice}', [PaymentController::class, 'store'])->name('payments.store');
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    Route::get('/reports',          [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/aging',    [ReportController::class, 'aging'])->name('reports.aging');
    Route::get('/reports/cashflow', [ReportController::class, 'cashflow'])->name('reports.cashflow');
    Route::get('/reports/pl',       [ReportController::class, 'pl'])->name('reports.pl');
    Route::get('/reports/tax',      [ReportController::class, 'tax'])->name('reports.tax');

    // Reminder Sequence
    Route::get   ('invoices/{invoice}/reminders',           [InvoiceReminderController::class, 'index'])->name('invoices.reminders.index');
    Route::post  ('invoices/{invoice}/reminders/schedule',  [InvoiceReminderController::class, 'schedule'])->name('invoices.reminders.schedule');
    Route::post  ('invoices/{invoice}/reminders',           [InvoiceReminderController::class, 'store'])->name('invoices.reminders.store');
    Route::delete('invoices/reminders/{reminder}',          [InvoiceReminderController::class, 'cancel'])->name('invoices.reminders.cancel');

    // Late Fees
    Route::post  ('invoices/{invoice}/late-fees/apply',     [InvoiceLateFeeController::class, 'apply'])->name('invoices.late-fees.apply');
    Route::delete('invoices/late-fees/{fee}',               [InvoiceLateFeeController::class, 'destroy'])->name('invoices.late-fees.destroy');

    // Invoice Tax Details (PPh 23 + e-Faktur)
    Route::get   ('invoices/{invoice}/tax-details',         [\App\Http\Controllers\InvoiceTaxDetailController::class, 'edit'])->name('invoices.tax-details.edit');
    Route::put   ('invoices/{invoice}/tax-details',         [\App\Http\Controllers\InvoiceTaxDetailController::class, 'update'])->name('invoices.tax-details.update');

    // e-Faktur
    Route::post  ('invoices/{invoice}/efaktur/generate',    [\App\Http\Controllers\EFakturController::class, 'generate'])->name('invoices.efaktur.generate');
    Route::get   ('invoices/{invoice}/efaktur/xml',         [\App\Http\Controllers\EFakturController::class, 'downloadXml'])->name('invoices.efaktur.xml');

    // NPWP validate API
    Route::post  ('api/npwp/validate',                      [\App\Http\Controllers\Api\NpwpValidationController::class, 'validate'])->name('api.npwp.validate');

    // WhatsApp Send
    Route::get   ('invoices/{invoice}/whatsapp',            [\App\Http\Controllers\WhatsappSendController::class, 'preview'])->name('invoices.whatsapp.preview');
    Route::post  ('invoices/{invoice}/whatsapp',            [\App\Http\Controllers\WhatsappSendController::class, 'send'])->name('invoices.whatsapp.send');

    // QRIS
    Route::post  ('invoices/{invoice}/qris/generate',       [\App\Http\Controllers\QrisController::class, 'generate'])->name('invoices.qris.generate');
    Route::get   ('invoices/{invoice}/qris/{qris}',         [\App\Http\Controllers\QrisController::class, 'show'])->name('invoices.qris.show');
    Route::post  ('qris/{qris}/cancel',                     [\App\Http\Controllers\QrisController::class, 'cancel'])->name('invoices.qris.cancel');

    // Coupons
    Route::get   ('coupons',                                [\App\Http\Controllers\CouponController::class, 'index'])->name('coupons.index');
    Route::post  ('coupons',                                [\App\Http\Controllers\CouponController::class, 'store'])->name('coupons.store');
    Route::post  ('coupons/{coupon}/toggle',                [\App\Http\Controllers\CouponController::class, 'toggle'])->name('coupons.toggle');
    Route::delete('coupons/{coupon}',                       [\App\Http\Controllers\CouponController::class, 'destroy'])->name('coupons.destroy');

    // OCR Receipt
    Route::get   ('expenses/ocr/upload',                    [\App\Http\Controllers\ExpenseOcrController::class, 'showUpload'])->name('expenses.ocr.upload');
    Route::post  ('expenses/ocr/process',                   [\App\Http\Controllers\ExpenseOcrController::class, 'process'])->name('expenses.ocr.process');
    Route::get   ('expenses/{expense}/ocr-review',          [\App\Http\Controllers\ExpenseOcrController::class, 'review'])->name('expenses.ocr.review');
    Route::put   ('expenses/{expense}/ocr-confirm',         [\App\Http\Controllers\ExpenseOcrController::class, 'confirm'])->name('expenses.ocr.confirm');

    // Report Subscriptions
    Route::get   ('report-subscriptions',                   [\App\Http\Controllers\ReportSubscriptionController::class, 'index'])->name('report-subscriptions.index');
    Route::post  ('report-subscriptions',                   [\App\Http\Controllers\ReportSubscriptionController::class, 'store'])->name('report-subscriptions.store');
    Route::post  ('report-subscriptions/{sub}/toggle',      [\App\Http\Controllers\ReportSubscriptionController::class, 'toggle'])->name('report-subscriptions.toggle');
    Route::delete('report-subscriptions/{sub}',             [\App\Http\Controllers\ReportSubscriptionController::class, 'destroy'])->name('report-subscriptions.destroy');

    // Dashboard Widgets
    Route::get   ('dashboard/customize',                    [\App\Http\Controllers\DashboardWidgetController::class, 'customize'])->name('dashboard.customize');
    Route::post  ('dashboard/widgets',                      [\App\Http\Controllers\DashboardWidgetController::class, 'store'])->name('dashboard.widgets.store');
    // NOTE: `reorder` must be registered BEFORE `widgets/{widget}` or the
    // literal "reorder" is captured as the {widget} parameter.
    Route::put   ('dashboard/widgets/reorder',              [\App\Http\Controllers\DashboardWidgetController::class, 'reorder'])->name('dashboard.widgets.reorder');
    Route::put   ('dashboard/widgets/{widget}',             [\App\Http\Controllers\DashboardWidgetController::class, 'update'])->name('dashboard.widgets.update');
    Route::delete('dashboard/widgets/{widget}',             [\App\Http\Controllers\DashboardWidgetController::class, 'destroy'])->name('dashboard.widgets.destroy');

    // Accounting Exports
    Route::get   ('accounting-exports',                     [\App\Http\Controllers\AccountingExportController::class, 'index'])->name('accounting-exports.index');
    Route::get   ('accounting-exports/create',              [\App\Http\Controllers\AccountingExportController::class, 'create'])->name('accounting-exports.create');
    Route::post  ('accounting-exports',                     [\App\Http\Controllers\AccountingExportController::class, 'store'])->name('accounting-exports.store');
    Route::get   ('accounting-exports/{export}/download',   [\App\Http\Controllers\AccountingExportController::class, 'download'])->name('accounting-exports.download');
    Route::delete('accounting-exports/{export}',            [\App\Http\Controllers\AccountingExportController::class, 'destroy'])->name('accounting-exports.destroy');

    // Email Forward → Expense
    Route::get   ('email-forwards',                         [\App\Http\Controllers\EmailForwardController::class, 'index'])->name('email-forwards.index');

    // Mobile Quick Capture flows
    Route::get   ('m/quick/invoice',                        [\App\Http\Controllers\MobileQuickController::class, 'quickInvoice'])->name('mobile.quick-invoice');
    Route::post  ('m/quick/invoice',                        [\App\Http\Controllers\MobileQuickController::class, 'storeQuickInvoice'])->name('mobile.quick-invoice.store');
    Route::get   ('m/quick/payment',                        [\App\Http\Controllers\MobileQuickController::class, 'quickPayment'])->name('mobile.quick-payment');
    Route::post  ('m/quick/payment',                        [\App\Http\Controllers\MobileQuickController::class, 'storeQuickPayment'])->name('mobile.quick-payment.store');

    // Marketplace (coming soon stub)
    Route::view  ('marketplace',                            'marketplace.index')->name('marketplace.index');

    // Embed demo (admin tool — needs auth for x-app-layout)
    Route::get   ('embed/demo',                             [\App\Http\Controllers\EmbedController::class, 'demo'])->name('embed.demo');

    // Bank Reconcile
    Route::get   ('bank-reconcile',                         [\App\Http\Controllers\BankReconcileController::class, 'index'])->name('bank-reconcile.index');
    Route::get   ('bank-reconcile/create',                  [\App\Http\Controllers\BankReconcileController::class, 'create'])->name('bank-reconcile.create');
    Route::post  ('bank-reconcile',                         [\App\Http\Controllers\BankReconcileController::class, 'store'])->name('bank-reconcile.store');
    Route::get   ('bank-reconcile/{batch}',                 [\App\Http\Controllers\BankReconcileController::class, 'show'])->name('bank-reconcile.show');
    Route::post  ('bank-reconcile/{batch}/match',           [\App\Http\Controllers\BankReconcileController::class, 'match'])->name('bank-reconcile.match');
    Route::post  ('bank-reconcile/{batch}/finalize',        [\App\Http\Controllers\BankReconcileController::class, 'finalize'])->name('bank-reconcile.finalize');

    // Client Portal Management (workspace admin manages)
    Route::get   ('clients/{client}/portal-users',          [\App\Http\Controllers\ClientPortalManageController::class, 'show'])->name('clients.portal-users.show');
    Route::post  ('clients/{client}/portal-users',          [\App\Http\Controllers\ClientPortalManageController::class, 'invite'])->name('clients.portal-users.invite');
    Route::delete('portal-users/{portalUser}',              [\App\Http\Controllers\ClientPortalManageController::class, 'destroy'])->name('portal-users.destroy');
    Route::post('portal-users/{portalUser}/toggle-approve',    [\App\Http\Controllers\ClientPortalManageController::class, 'toggleApprove'])->name('portal-users.toggle-approve');
    Route::post('portal-users/{portalUser}/toggle-active',     [\App\Http\Controllers\ClientPortalManageController::class, 'toggleActive'])->name('portal-users.toggle-active');

    // Credit Notes
    Route::resource('credit-notes', CreditNoteController::class)->except(['edit', 'update']);
    Route::post  ('credit-notes/{credit_note}/issue',   [CreditNoteController::class, 'issue'])->name('credit-notes.issue');
    Route::post  ('credit-notes/{credit_note}/apply',   [CreditNoteController::class, 'apply'])->name('credit-notes.apply');
    Route::post  ('credit-notes/{credit_note}/cancel',  [CreditNoteController::class, 'cancel'])->name('credit-notes.cancel');

    // Projects
    Route::resource('projects', ProjectController::class);

    // Invoice Comments
    Route::post  ('invoices/{invoice}/comments',          [InvoiceCommentController::class, 'store'])->name('invoices.comments.store');
    Route::delete('invoices/comments/{comment}',          [InvoiceCommentController::class, 'destroy'])->name('invoices.comments.destroy');

    // AI Providers
    Route::resource('ai-providers', AiProviderController::class)->except(['show'])->parameters(['ai-providers' => 'provider']);
    Route::post  ('ai-providers/{provider}/test',          [AiProviderController::class, 'test'])->name('ai-providers.test');

    // AI features
    Route::post  ('api/ai/description/expand',             [\App\Http\Controllers\Api\AiDescriptionController::class, 'expand'])->name('api.ai.description.expand');
    Route::post  ('api/ai/expense/categorize',             [\App\Http\Controllers\Api\AiExpenseController::class, 'categorize'])->name('api.ai.expense.categorize');
    Route::post  ('invoices/{invoice}/translate',          [\App\Http\Controllers\InvoiceTranslateController::class, 'translate'])->name('invoices.translate');

    // Custom Domain (white-label)
    Route::get   ('workspace/custom-domain',               [CustomDomainController::class, 'show'])->name('custom-domain.show');
    Route::post  ('workspace/custom-domain',               [CustomDomainController::class, 'store'])->name('custom-domain.store');
    Route::post  ('workspace/custom-domain/verify',        [CustomDomainController::class, 'verify'])->name('custom-domain.verify');
    Route::delete('workspace/custom-domain',               [CustomDomainController::class, 'destroy'])->name('custom-domain.destroy');

    // Approval Workflow
    Route::get   ('approval-rules',                         [\App\Http\Controllers\ApprovalRuleController::class, 'index'])->name('approval-rules.index');
    Route::post  ('approval-rules',                         [\App\Http\Controllers\ApprovalRuleController::class, 'store'])->name('approval-rules.store');
    Route::put   ('approval-rules/{rule}',                  [\App\Http\Controllers\ApprovalRuleController::class, 'update'])->name('approval-rules.update');
    Route::delete('approval-rules/{rule}',                  [\App\Http\Controllers\ApprovalRuleController::class, 'destroy'])->name('approval-rules.destroy');
    Route::get   ('approvals',                              [\App\Http\Controllers\ApprovalController::class, 'index'])->name('approvals.index');
    Route::post  ('approvals/{approvalRequest}/approve',    [\App\Http\Controllers\ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post  ('approvals/{approvalRequest}/reject',     [\App\Http\Controllers\ApprovalController::class, 'reject'])->name('approvals.reject');

    // 2FA
    Route::get   ('profile/2fa',                           [TwoFactorController::class, 'show'])->name('2fa.show');
    Route::post  ('profile/2fa/enable',                    [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::post  ('profile/2fa/confirm',                   [TwoFactorController::class, 'confirm'])->name('2fa.confirm');
    Route::post  ('profile/2fa/disable',                   [TwoFactorController::class, 'disable'])->name('2fa.disable');
    Route::post  ('profile/2fa/recovery-codes',            [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('2fa.recovery-codes');

    Route::resource('gateways', GatewayController::class)->except(['create', 'edit', 'show']);
    Route::post('gateways/{gateway}/toggle', [GatewayController::class, 'toggle'])->name('gateways.toggle');

    // Drag-drop invoice layout builder (Business plan only)
    Route::middleware('plan:drag_drop')->group(function () {
        Route::resource('invoice-layouts', InvoiceLayoutController::class)->except(['show']);
    });

    // ─── Real implemented features ─────────────────────────────────
    Route::get   ('time-entries',                 [TimeEntryController::class, 'index'])->middleware('plan:time_tracking')->name('time-entries.index');
    Route::post  ('time-entries',                 [TimeEntryController::class, 'store'])->middleware('plan:time_tracking')->name('time-entries.store');
    Route::delete('time-entries/{timeEntry}',     [TimeEntryController::class, 'destroy'])->middleware('plan:time_tracking')->name('time-entries.destroy');

    Route::get   ('expenses',                     [ExpenseController::class, 'index'])->middleware('plan:expense_tracking')->name('expenses.index');
    Route::post  ('expenses',                     [ExpenseController::class, 'store'])->middleware('plan:expense_tracking')->name('expenses.store');
    Route::delete('expenses/{expense}',           [ExpenseController::class, 'destroy'])->middleware('plan:expense_tracking')->name('expenses.destroy');

    Route::resource('quotes', QuoteController::class)->except(['edit', 'update']);
    Route::post  ('quotes/{quote}/convert',       [QuoteController::class, 'convertToInvoice'])->name('quotes.convert');
    Route::post  ('quotes/{quote}/send',          [QuoteController::class, 'send'])->name('quotes.send');
    Route::post  ('quotes/{quote}/withdraw',      [QuoteController::class, 'withdraw'])->name('quotes.withdraw');
    Route::post  ('quotes/{quote}/reopen',        [QuoteController::class, 'reopen'])->name('quotes.reopen');
    Route::post  ('quotes/{quote}/rotate-link',   [QuoteController::class, 'rotateLink'])->name('quotes.rotate-link');

    Route::post  ('invoices/{invoice}/attachments',         [InvoiceAttachmentController::class, 'store'])->name('invoices.attachments.store');
    Route::delete('invoice-attachments/{attachment}',       [InvoiceAttachmentController::class, 'destroy'])->name('invoices.attachments.destroy');

    Route::get   ('audit-logs',                   [AuditLogController::class, 'index'])->middleware('plan:audit_log')->name('audit-logs.index');

    Route::get   ('notifications',                [NotificationController::class, 'index'])->name('notifications.index');
    Route::post  ('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::delete('notifications',                [NotificationController::class, 'clearAll'])->name('notifications.clear');

    Route::get   ('support',                      [SupportController::class, 'index'])->name('support.index');
    Route::get   ('support/create',               [SupportController::class, 'create'])->name('support.create');
    Route::post  ('support',                      [SupportController::class, 'store'])->name('support.store');
    Route::get   ('support/{ticket}',             [SupportController::class, 'show'])->name('support.show');
    Route::post  ('support/{ticket}/reply',       [SupportController::class, 'reply'])->name('support.reply');

    Route::get   ('affiliate',                    [AffiliateController::class, 'index'])->name('affiliate.index');

    Route::get   ('clients-import',               [ClientImportController::class, 'show'])->middleware('plan:csv.import')->name('clients.import.show');
    Route::post  ('clients-import',               [ClientImportController::class, 'import'])->middleware('plan:csv.import')->name('clients.import');

    Route::get   ('exports',                      [ExportController::class, 'index'])->name('exports.index');
    Route::get   ('exports/invoices.csv',         [ExportController::class, 'invoices'])->name('exports.invoices');
    Route::get   ('exports/payments.csv',         [ExportController::class, 'payments'])->name('exports.payments');
    Route::get   ('exports/clients.csv',          [ExportController::class, 'clients'])->name('exports.clients');

    Route::middleware('plan:api.access')->group(function () {
        Route::get   ('api-tokens',               [ApiTokenController::class, 'index'])->name('api-tokens.index');
        Route::post  ('api-tokens',               [ApiTokenController::class, 'store'])->name('api-tokens.store');
        Route::delete('api-tokens/{apiToken}',    [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');
    });

    Route::middleware('plan:tenant_webhooks')->group(function () {
        Route::get   ('tenant-webhooks',          [TenantWebhookController::class, 'index'])->name('tenant-webhooks.index');
        Route::post  ('tenant-webhooks',          [TenantWebhookController::class, 'store'])->name('tenant-webhooks.store');
        Route::post  ('tenant-webhooks/{tenantWebhook}/toggle', [TenantWebhookController::class, 'toggle'])->name('tenant-webhooks.toggle');
        Route::delete('tenant-webhooks/{tenantWebhook}', [TenantWebhookController::class, 'destroy'])->name('tenant-webhooks.destroy');
    });

    // Stubs — replaced with real routes or coming-soon pages
    // All stub links in sidebar now point to real routes or route('feature.coming', ...)

    Route::get   ('team',                         [TeamController::class, 'index'])->name('team.index');
    Route::post  ('team/invite',                  [TeamController::class, 'invite'])->name('team.invite');
    Route::delete('team/invite/{invite}',         [TeamController::class, 'revoke'])->name('team.invite.revoke');

    // Blog admin — superadmin only. These routes publish to the PUBLIC blog,
    // so they must not be reachable by regular workspace users.
    Route::middleware('superadmin')->group(function () {
        Route::get   ('blog/admin',                              [\App\Http\Controllers\Admin\BlogController::class, 'index'])->name('admin.blog.index');
        Route::get   ('blog/admin/create',                       [\App\Http\Controllers\Admin\BlogController::class, 'create'])->name('admin.blog.create');
        Route::post  ('blog/admin',                              [\App\Http\Controllers\Admin\BlogController::class, 'store'])->name('admin.blog.store');
        Route::get   ('blog/admin/{post}/edit',                  [\App\Http\Controllers\Admin\BlogController::class, 'edit'])->name('admin.blog.edit');
        Route::put   ('blog/admin/{post}',                       [\App\Http\Controllers\Admin\BlogController::class, 'update'])->name('admin.blog.update');
        Route::delete('blog/admin/{post}',                       [\App\Http\Controllers\Admin\BlogController::class, 'destroy'])->name('admin.blog.destroy');
        Route::get   ('blog/admin/categories',                   [\App\Http\Controllers\Admin\BlogController::class, 'categories'])->name('admin.blog.categories');
        Route::post  ('blog/admin/categories',                   [\App\Http\Controllers\Admin\BlogController::class, 'storeCategory'])->name('admin.blog.categories.store');
        Route::put   ('blog/admin/categories/{category}',        [\App\Http\Controllers\Admin\BlogController::class, 'updateCategory'])->name('admin.blog.categories.update');
        Route::delete('blog/admin/categories/{category}',        [\App\Http\Controllers\Admin\BlogController::class, 'destroyCategory'])->name('admin.blog.categories.destroy');
    });

    // Feature scaffolds (placeholder pages until real implementation)
    Route::get('feature/{feature}', [ComingSoonController::class, 'index'])->name('feature.coming');

    // Billing — workspace owner only (in real app gate further by role)
    Route::get('billing',                     [BillingController::class, 'show'])->name('billing.show');
    Route::post('billing/subscribe/{plan}',   [BillingController::class, 'subscribe'])->name('billing.subscribe');
    Route::get('billing/manual/{subscription}', [BillingController::class, 'manualInstructions'])->name('billing.manual');
    Route::post('billing/manual/{subscription}/proof', [BillingController::class, 'uploadManualProof'])->name('billing.manual.proof');
    Route::post('billing/cancel',             [BillingController::class, 'cancel'])->name('billing.cancel');
    Route::post('billing/resume',             [BillingController::class, 'resume'])->name('billing.resume');
});

// Superadmin
Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',                          [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('plans',                 AdminPlanController::class);
    Route::resource('users',                 AdminUserController::class)->only(['index', 'show', 'destroy']);
    Route::resource('workspaces',            AdminWorkspaceController::class)->only(['index', 'show']);
    Route::post('workspaces/{workspace}/assign-plan', [AdminWorkspaceController::class, 'assignPlan'])->name('workspaces.assign-plan');
    Route::resource('subscriptions',         AdminSubscriptionController::class)->only(['index', 'show', 'update']);
    Route::get('subscriptions-manual',       [AdminSubscriptionController::class, 'manualAssign'])->name('subscriptions.manual');
    Route::post('subscriptions-manual',      [AdminSubscriptionController::class, 'manualStore'])->name('subscriptions.manual.store');
    Route::post('subscriptions-manual/create-with-user', [AdminSubscriptionController::class, 'manualCreate'])->name('subscriptions.manual.create');

    Route::get('branding',  [AdminBrandingController::class, 'edit'])->name('branding.edit');
    Route::post('branding', [AdminBrandingController::class, 'update'])->name('branding.update');

    Route::get('manual-payments',                       [AdminManualPaymentController::class, 'index'])->name('manual-payments.index');
    Route::post('manual-payments/methods',              [AdminManualPaymentController::class, 'storeMethod'])->name('manual-payments.methods.store');
    Route::delete('manual-payments/methods/{method}',   [AdminManualPaymentController::class, 'destroyMethod'])->name('manual-payments.methods.destroy');
    Route::post('manual-payments/{subscription}/approve', [AdminManualPaymentController::class, 'approve'])->name('manual-payments.approve');
    Route::post('manual-payments/{subscription}/reject',  [AdminManualPaymentController::class, 'reject'])->name('manual-payments.reject');

    Route::get('coupons',                 [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::post('coupons',                [AdminCouponController::class, 'store'])->name('coupons.store');
    Route::post('coupons/{coupon}/toggle', [AdminCouponController::class, 'toggle'])->name('coupons.toggle');
    Route::delete('coupons/{coupon}',     [AdminCouponController::class, 'destroy'])->name('coupons.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/docs', fn() => view('docs'))->name('docs');

Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');

Route::get('/i/{token}', [PublicInvoiceController::class, 'show'])->middleware('throttle:public-invoice')->name('public.invoice.show');

// Inbound email webhook (no auth — provider signature verified inside)
Route::post('/webhooks/email-inbox', [\App\Http\Controllers\Webhook\EmailInboxController::class, 'handle'])->name('webhooks.email-inbox');

// Embed Widget (public, frame-able)
Route::get('/embed/invoice/{token}', [\App\Http\Controllers\EmbedController::class, 'invoice'])->name('embed.invoice');
Route::get('/widget/invocely.js',    [\App\Http\Controllers\EmbedController::class, 'widgetJs'])->name('embed.widget-js');
// /embed/demo is auth-protected (admin tool). Registered inside auth group below by reference.

// SSO OAuth login
Route::get ('/auth/sso/{provider}/redirect', [\App\Http\Controllers\Auth\SsoController::class, 'redirect'])->name('sso.redirect');
Route::get ('/auth/sso/{provider}/callback', [\App\Http\Controllers\Auth\SsoController::class, 'callback'])->name('sso.callback');

// Customer Portal (separate auth guard)
Route::prefix('portal')->group(function () {
    Route::get ('login',     [\App\Http\Controllers\Portal\PortalAuthController::class, 'showLogin'])->name('portal.login');
    Route::post('login',     [\App\Http\Controllers\Portal\PortalAuthController::class, 'login'])->middleware('throttle:portal-login')->name('portal.login.submit');

    Route::middleware('auth:client_portal', 'portal.active')->group(function () {
        Route::post('logout',   [\App\Http\Controllers\Portal\PortalAuthController::class, 'logout'])->name('portal.logout');
        Route::get ('',         [\App\Http\Controllers\Portal\PortalDashboardController::class, 'dashboard'])->name('portal.dashboard');
        Route::get ('invoices', [\App\Http\Controllers\Portal\PortalDashboardController::class, 'invoices'])->name('portal.invoices');
        Route::get ('quotes',   [\App\Http\Controllers\Portal\PortalDashboardController::class, 'quotes'])->name('portal.quotes');
    });
});

// Public Quote — verified approval only. Viewing never mutates state;
// every state change is POST + CSRF + throttled. Legacy instant
// accept/reject/sign endpoints were removed (unauthenticated mutation).
Route::get  ('/q/{token}',           [\App\Http\Controllers\PublicQuoteController::class, 'show'])->middleware('throttle:quote-public')->name('public.quote.show');
Route::post ('/q/{token}/otp',       [\App\Http\Controllers\PublicQuoteController::class, 'requestOtp'])->middleware('throttle:quote-otp')->name('public.quote.otp.request');
Route::get  ('/q/{token}/verify',    [\App\Http\Controllers\PublicQuoteController::class, 'verifyForm'])->middleware('throttle:quote-public')->name('public.quote.verify-form');
Route::post ('/q/{token}/verify',    [\App\Http\Controllers\PublicQuoteController::class, 'verify'])->middleware('throttle:quote-verify')->name('public.quote.verify');
Route::get  ('/q/{token}/confirm',   [\App\Http\Controllers\PublicQuoteController::class, 'confirmForm'])->middleware('throttle:quote-public')->name('public.quote.confirm');
Route::post ('/q/{token}/confirm',   [\App\Http\Controllers\PublicQuoteController::class, 'confirm'])->middleware('throttle:quote-confirm')->name('public.quote.confirm.post');

// NPS public form (post-payment satisfaction)
Route::get ('/nps/{token}', [NpsController::class, 'show'])->name('nps.show');
Route::post('/nps/{token}', [NpsController::class, 'submit'])->name('nps.submit');
Route::post('/i/{invoice}/bayar', [PaymentController::class, 'gatewayPay'])->name('payments.gateway');
Route::post('/webhooks/payment/{gateway}', [PaymentController::class, 'webhook'])->name('webhooks.payment');
Route::post('/webhooks/billing/{gateway}', [BillingWebhookController::class, 'handle'])->name('webhooks.billing');

Route::get('/best-invoicing-tools-{year}', [ProgrammaticSeoController::class, 'bestTools'])->name('seo.best-tools');
Route::get('/alternatives-to-{slug}',      [ProgrammaticSeoController::class, 'alternatives'])->name('seo.alternatives');
Route::get('/compare/{a}-vs-{b}',          [PlanComparisonController::class, 'show'])
    ->where(['a' => 'free|pro|business', 'b' => 'free|pro|business'])
    ->name('plans.compare');
Route::get('/compare/{a}-vs-{b}',          [ProgrammaticSeoController::class, 'compare'])->name('seo.compare');
    Route::get('/invoice-templates',         [ProgrammaticSeoController::class, 'templates'])->name('seo.templates');
    Route::get('/sitemap.xml', fn() => response()->view('sitemap')->header('Content-Type', 'application/xml'));
    Route::get('/robots.txt',  fn() => response()->view('robots')->header('Content-Type', 'text/plain'));

    // Blog (public)
    Route::get('/blog',                    [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/feed.xml',           [\App\Http\Controllers\BlogController::class, 'feed'])->name('blog.feed');
    Route::get('/blog/category/{slug}',   [\App\Http\Controllers\BlogController::class, 'category'])->name('blog.category');
    Route::get('/blog/{slug}',             [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

require __DIR__.'/auth.php';

// License Pairing v3 routes
require base_path('routes/pair-routes.php');
