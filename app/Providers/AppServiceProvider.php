<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quote;
use App\Observers\AuditableObserver;
use App\Observers\PaymentObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Bootstrap 5 markup so Tabler styles pagination consistently.
        Paginator::useBootstrapFive();

        // Named rate limiters keyed per-route + IP. (The `throttle:N,1`
        // shorthand shares one counter per IP across ALL routes using it,
        // which wrongly trips legitimate traffic behind office NAT and
        // makes unrelated endpoints consume each other's budget.)
        $routeIp = fn ($request) => ($request->route()?->getName() ?? 'x') . '|' . $request->ip();
        RateLimiter::for('quote-public', fn ($request) => Limit::perMinute(120)->by($routeIp($request)));
        RateLimiter::for('quote-otp', fn ($request) => Limit::perMinute(5)->by($routeIp($request)));
        RateLimiter::for('quote-verify', fn ($request) => Limit::perMinute(15)->by($routeIp($request)));
        RateLimiter::for('quote-confirm', fn ($request) => Limit::perMinute(30)->by($routeIp($request)));
        RateLimiter::for('portal-login', fn ($request) => Limit::perMinute(10)->by($routeIp($request)));
        RateLimiter::for('public-invoice', fn ($request) => Limit::perMinute(120)->by($routeIp($request)));
        RateLimiter::for('auth-register', fn ($request) => Limit::perMinute(10)->by($routeIp($request)));
        RateLimiter::for('auth-email', fn ($request) => Limit::perMinute(6)->by($routeIp($request)));

        // Auto-audit mutations on key models
        Invoice::observe(AuditableObserver::class);
        Quote::observe(AuditableObserver::class);
        Payment::observe(AuditableObserver::class);
        CreditNote::observe(AuditableObserver::class);
        Client::observe(AuditableObserver::class);
        Project::observe(AuditableObserver::class);
        \App\Models\QuoteApprovalEvent::observe(AuditableObserver::class);

        // Update client payment behavior stats on payment events
        Payment::observe(PaymentObserver::class);
    }
}
