<x-tabler.public :title="__('Documentation')" :meta-description="__('User guides, billing, integrations, PWA, troubleshooting and developer notes for Invocely.')">
    <div class="row g-3">
        <div class="col-lg-3 d-print-none">
            <div class="card position-sticky" style="top: 1rem;">
                <div class="card-body">
                    <div class="mb-3">
                        <div class="input-icon">
                            <span class="input-icon-addon"><x-ti name="search" /></span>
                            <input type="search" id="docs-search" class="form-control"
                                placeholder="{{ __('Search documentation…') }}"
                                aria-label="{{ __('Search documentation') }}">
                        </div>
                    </div>
                    <nav id="docs-nav" class="nav nav-pills nav-vertical" aria-label="{{ __('Documentation sections') }}">
                        @php
                            $docSections = [
                                'getting-started' => __('Getting Started'),
                                'dashboard' => __('Dashboard'),
                                'clients-products' => __('Clients & Products'),
                                'invoices' => __('Invoices'),
                                'quotations' => __('Quotations'),
                                'payments' => __('Payments'),
                                'recurring-reminders' => __('Recurring & Reminders'),
                                'finance-reports' => __('Finance & Reports'),
                                'team-security' => __('Team & Security'),
                                'billing-plans' => __('Billing & Plans'),
                                'integrations' => __('Integrations'),
                                'pwa-mobile' => __('PWA & Mobile'),
                                'troubleshooting' => __('Troubleshooting'),
                                'developer' => __('Developer & API'),
                                'release-status' => __('Release Notes & Status'),
                            ];
                        @endphp
                        @foreach($docSections as $id => $label)
                            <a class="nav-link docs-nav-link" href="#{{ $id }}">{{ $label }}</a>
                        @endforeach
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="mb-3">
                <h1 class="page-title">{{ __('Invocely Documentation') }}</h1>
                <p class="text-muted">{{ __('Practical guides that match the actual application: routes, buttons, statuses and settings as they exist today.') }}</p>
            </div>
            <div id="docs-empty" class="alert alert-info d-none" role="status">
                {{ __('No sections match your search.') }}
            </div>

            <div class="card mb-3" data-docs-section id="getting-started">
                <div class="card-header"><h2 class="card-title h3">{{ __('1. Getting Started') }}</h2></div>
                <div class="card-body">
                    <p>{{ __('Invocely is an invoicing workspace: create a workspace, add clients, issue invoices, collect payments, and track reports. Every record belongs to exactly one workspace.') }}</p>
                    <h3 class="h4">{{ __('Initial checklist') }}</h3>
                    <ol>
                        <li>{{ __('Register, then create your first workspace (name + default currency).') }}</li>
                        <li>{{ __('Open Business Settings: company identity, logo, invoice prefix, default template, tax ID.') }}</li>
                        <li>{{ __('Add a payment gateway under Payment Gateways, or record manual bank transfers.') }}</li>
                        <li>{{ __('Invite your team under Team & Roles.') }}</li>
                        <li>{{ __('On the server: cron schedule:run, queue worker, SMTP, storage link (see Deployment guide).') }}</li>
                    </ol>
                </div>
            </div>

            <div class="card mb-3" data-docs-section id="dashboard">
                <div class="card-header"><h2 class="card-title h3">{{ __('2. Dashboard') }}</h2></div>
                <div class="card-body">
                    <p>{{ __('The dashboard shows totals computed from your workspace database: invoiced, collected, outstanding and overdue. Draft invoices are never counted as revenue; failed payment intents are never counted as collected.') }}</p>
                    <p class="text-muted">{{ __('Use quick actions (New Invoice, New Quote, Add Client, Record Payment) from any page header.') }}</p>
                </div>
            </div>

            <div class="card mb-3" data-docs-section id="clients-products">
                <div class="card-header"><h2 class="card-title h3">{{ __('3. Clients & Products') }}</h2></div>
                <div class="card-body">
                    <ul>
                        <li>{{ __('Clients: list, search, status filter, pagination. Deleting is blocked while invoices exist.') }}</li>
                        <li>{{ __('Per-client reminder opt-out lives on the client form ("Do not send payment reminders").') }}</li>
                        <li>{{ __('CSV import validates every row and reports per-row results; failed rows never create partial records.') }}</li>
                        <li>{{ __('Products & Services feed invoice line items with quantity, unit price and per-line tax.') }}</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3" data-docs-section id="invoices">
                <div class="card-header"><h2 class="card-title h3">{{ __('4. Invoices') }}</h2></div>
                <div class="card-body">
                    <h3 class="h4">{{ __('Creating') }}</h3>
                    <p>{{ __('Invoices → New Invoice: pick a client from your workspace, set issue/due dates, add line items, then tax rate and discount (none, percent, fixed). Totals are computed once, server-side.') }}</p>
                    <h3 class="h4">{{ __('Statuses') }}</h3>
                    <p>{{ __('draft → sent → partial → paid, plus overdue and cancelled. Only draft invoices can be edited or deleted. Mark as Sent reopens cancelled ones; paid invoices are immutable except via credit note / payment correction.') }}</p>
                    <ul>
                        <li>{{ __('Duplicate creates a brand-new number and draft; payments are never copied.') }}</li>
                        <li>{{ __('Share link uses an unguessable public token; PDF keeps a dedicated print layout.') }}</li>
                        <li>{{ __('Attachments and comments are scoped to the invoice workspace.') }}</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3" data-docs-section id="quotations">
                <div class="card-header"><h2 class="card-title h3">{{ __('5. Quotations') }}</h2></div>
                <div class="card-body">
                    <p>{{ __('Quotes have their own numbering and revision counter. States: draft, sent, accepted, rejected, expired, withdrawn, converted.') }}</p>
                    <h3 class="h4">{{ __('Approval') }}</h3>
                    <ul>
                        <li>{{ __('Opening the public link only lets you review — it never approves anything.') }}</li>
                        <li>{{ __('Approve via customer-portal account (designated approver) or a 6-digit email code sent to the address on file (15 minutes, 5 attempts).') }}</li>
                        <li>{{ __('A code proves access to that mailbox, not corporate signing authority; the approval record stores the evidence level.') }}</li>
                        <li>{{ __('You confirm the exact revision, total, and consent text with an unchecked checkbox.') }}</li>
                        <li>{{ __('Revisions invalidate old codes; expiry and withdrawal are enforced server-side.') }}</li>
                        <li>{{ __('Accepted quotes convert to at most one invoice per revision (repeat-safe).') }}</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3" data-docs-section id="payments">
                <div class="card-header"><h2 class="card-title h3">{{ __('6. Payments') }}</h2></div>
                <div class="card-body">
                    <ul>
                        <li>{{ __('Manual payments support partial amounts; full payment flips the invoice to paid and zeroes the balance.') }}</li>
                        <li>{{ __('Gateways implemented: Midtrans, Xendit, Tripay, Duitku, DOKU, OY!, iPaymu, Finpay, plus a generic adapter. Each needs its own provider credentials; without them checkout cannot be tested end-to-end.') }}</li>
                        <li>{{ __('Webhooks settle inside a database transaction with row locks, ignore replays, and never trust the callback amount — the server-side stored amount wins and mismatches are logged.') }}</li>
                        <li>{{ __('Gateway signature verification requires provider secrets (see Integrations). Until configured, treat callbacks as untrusted input.') }}</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3" data-docs-section id="recurring-reminders">
                <div class="card-header"><h2 class="card-title h3">{{ __('7. Recurring & Reminders') }}</h2></div>
                <div class="card-body">
                    <ul>
                        <li>{{ __('Recurring schedules generate invoices hourly (invoices:generate-recurring). Generation is idempotent per schedule run.') }}</li>
                        <li>{{ __('Payment reminders process hourly; clients with the opt-out flag are skipped; rescheduling cancels pending items first.') }}</li>
                        <li>{{ __('Overdue escalation runs hourly; late fees apply daily at 06:00 with an explicit policy — set a cap before relying on percentage fees.') }}</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3" data-docs-section id="finance-reports">
                <div class="card-header"><h2 class="card-title h3">{{ __('8. Finance & Reports') }}</h2></div>
                <div class="card-body">
                    <ul>
                        <li>{{ __('Reports: Financial Overview, Aging (outstanding), Cash Flow, P&L, Tax. Cancelled invoices are excluded from revenue.') }}</li>
                        <li>{{ __('Bank Reconciliation matches statement lines to invoices and finalizes real payment rows (linked back to the line).') }}</li>
                        <li>{{ __('CSV exports escape spreadsheet formulas to prevent formula injection.') }}</li>
                        <li>{{ __('E-Faktur XML follows the simplified Coretax layout; it is a helper, not a certified DJP integration.') }}</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3" data-docs-section id="team-security">
                <div class="card-header"><h2 class="card-title h3">{{ __('9. Team & Security') }}</h2></div>
                <div class="card-body">
                    <ul>
                        <li>{{ __('Roles on the workspace pivot: owner, admin, member. Cross-workspace URLs return 403/404 — never data.') }}</li>
                        <li>{{ __('2FA via authenticator app, API tokens per workspace (revocable), outbound tenant webhooks, read-only audit log.') }}</li>
                        <li>{{ __('Blog publishing is superadmin-only; the sidebar hides it from regular users.') }}</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3" data-docs-section id="billing-plans">
                <div class="card-header"><h2 class="card-title h3">{{ __('10. Billing & Plans') }}</h2></div>
                <div class="card-body">
                    <p>{{ __('Plans: Free, Pro, Business. Limits (invoices, clients, features) are enforced server-side, not just by hiding buttons. Upgrade, cancel, resume and manual bank-transfer proof with superadmin approval are supported.') }}</p>
                    <p><a href="{{ route('pricing') }}" class="btn btn-outline-primary btn-sm">{{ __('See pricing') }}</a></p>
                </div>
            </div>

            <div class="card mb-3" data-docs-section id="integrations">
                <div class="card-header"><h2 class="card-title h3">{{ __('11. Integrations') }}</h2></div>
                <div class="card-body">
                    <ul>
                        <li>{{ __('SMTP: set MAIL_* in .env; the default log driver never delivers real mail.') }}</li>
                        <li>{{ __('Payment gateways: credentials per workspace under Payment Gateways; callback URLs are generated per gateway.') }}</li>
                        <li>{{ __('Webhook signatures: configure each provider secret as documented by that provider; the code verifies only when secrets exist and otherwise logs + limits impact (documented as BLOCKED without secrets).') }}</li>
                        <li>{{ __('AI providers need a vendor API key; keys are never rendered back or logged. WhatsApp/QRIS need provider accounts.') }}</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3" data-docs-section id="pwa-mobile">
                <div class="card-header"><h2 class="card-title h3">{{ __('12. PWA & Mobile') }}</h2></div>
                <div class="card-body">
                    <ul>
                        <li>{{ __('Install from a supporting browser (Add to Home Screen / Install). Requires HTTPS in production.') }}</li>
                        <li>{{ __('Offline shows a friendly fallback; financial data is never served stale and no offline transactions are queued by design.') }}</li>
                        <li>{{ __('After logout or account switch, no previous-account data is shown — API responses are never cached.') }}</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3" data-docs-section id="troubleshooting">
                <div class="card-header"><h2 class="card-title h3">{{ __('13. Troubleshooting') }}</h2></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead><tr><th>{{ __('Symptom') }}</th><th>{{ __('Cause') }}</th><th>{{ __('Fix') }}</th></tr></thead>
                            <tbody>
                                <tr><td>{{ __('Redirected to workspace creation') }}</td><td>{{ __('Account has no workspace yet') }}</td><td>{{ __('Create one — you become owner.') }}</td></tr>
                                <tr><td>{{ __('Invoice create blocked by plan page') }}</td><td>{{ __('Free limits reached') }}</td><td>{{ __('Upgrade or remove unused records.') }}</td></tr>
                                <tr><td>{{ __('Mails only in log') }}</td><td>{{ __('MAIL_MAILER=log') }}</td><td>{{ __('Configure SMTP + queue worker.') }}</td></tr>
                                <tr><td>{{ __('PDF/fonts look off') }}</td><td>{{ __('Missing GD/intl or storage link') }}</td><td>{{ __('Install exts, run storage:link.') }}</td></tr>
                                <tr><td>{{ __('Scheduler silent') }}</td><td>{{ __('No cron / worker') }}</td><td>{{ __('Add schedule:run cron + queue:work.') }}</td></tr>
                                <tr><td>{{ __('500 after deploy') }}</td><td>{{ __('Stale config/view cache') }}</td><td>{{ __('config:clear, view:clear, rebuild.') }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3" data-docs-section id="developer">
                <div class="card-header"><h2 class="card-title h3">{{ __('14. Developer & API') }}</h2></div>
                <div class="card-body">
                    <ul>
                        <li>{{ __('Web (session) routes only — no versioned REST API yet. API tokens scope workspace access for enabled plans.') }}</li>
                        <li>{{ __('Pagination is server-side (20/page default on lists). Errors use Laravel defaults; validation returns 422 with field bags.') }}</li>
                        <li>{{ __('Tenant webhook events: invoice.paid / subscription.renewed / invoice.payment_failed / subscription.cancelled; deliveries are idempotent per external id.') }}</li>
                        <li>{{ __('Tests: php artisan test (isolated sqlite). See docs/TESTING.md in the repository.') }}</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3" data-docs-section id="release-status">
                <div class="card-header"><h2 class="card-title h3">{{ __('15. Release Notes & Status') }}</h2></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead><tr><th>{{ __('Area') }}</th><th>{{ __('Status') }}</th></tr></thead>
                            <tbody>
                                <tr><td>{{ __('Core invoicing, payments (manual), reports, billing') }}</td><td><span class="badge bg-green">{{ __('Active') }}</span></td></tr>
                                <tr><td>{{ __('Gateway checkout, WhatsApp, QRIS, AI') }}</td><td><span class="badge bg-yellow">{{ __('Partial — needs provider credentials') }}</span></td></tr>
                                <tr><td>{{ __('Webhook signature verification') }}</td><td><span class="badge bg-red">{{ __('Blocked — needs provider secrets') }}</span></td></tr>
                                <tr><td>{{ __('Marketplace, selected sidebar scaffolds') }}</td><td><span class="badge bg-azure">{{ __('Coming Soon') }}</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    @if(app()->isLocal())
                        <div class="alert alert-warning mt-3" role="note">
                            <strong>{{ __('Local demo accounts (never shown in production):') }}</strong>
                            <code>superadmin@invocely.test / superadmin123</code>,
                            <code>demo@invocely.test / password</code>
                        </div>
                    @endif
                    <p class="text-muted mt-2">{{ __('Full evidence: repository docs/audit/ (feature matrix, test evidence, known limitations, release checklist).') }}</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            var input = document.getElementById('docs-search');
            var empty = document.getElementById('docs-empty');
            if (!input) return;
            var sections = Array.prototype.slice.call(document.querySelectorAll('[data-docs-section]'));
            var links = Array.prototype.slice.call(document.querySelectorAll('.docs-nav-link'));
            input.addEventListener('input', function () {
                var q = input.value.trim().toLowerCase();
                var visible = 0;
                sections.forEach(function (s) {
                    var hit = !q || s.textContent.toLowerCase().indexOf(q) !== -1;
                    s.classList.toggle('d-none', !hit);
                    if (hit) visible++;
                });
                links.forEach(function (a) {
                    var target = document.querySelector(a.getAttribute('href'));
                    a.classList.toggle('d-none', !!target && target.classList.contains('d-none'));
                });
                empty.classList.toggle('d-none', visible !== 0);
            });
            // Scroll-spy lite: highlight current section.
            var spy = new IntersectionObserver(function (entries) {
                entries.forEach(function (en) {
                    if (!en.isIntersecting) return;
                    links.forEach(function (a) {
                        a.classList.toggle('active', a.getAttribute('href') === '#' + en.target.id);
                    });
                });
            }, { rootMargin: '-30% 0px -60% 0px' });
            sections.forEach(function (s) { spy.observe(s); });
        })();
    </script>
    @endpush
</x-tabler.public>
