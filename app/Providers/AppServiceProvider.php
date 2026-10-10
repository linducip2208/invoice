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
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Auto-audit mutations on key models
        Invoice::observe(AuditableObserver::class);
        Quote::observe(AuditableObserver::class);
        Payment::observe(AuditableObserver::class);
        CreditNote::observe(AuditableObserver::class);
        Client::observe(AuditableObserver::class);
        Project::observe(AuditableObserver::class);

        // Update client payment behavior stats on payment events
        Payment::observe(PaymentObserver::class);
    }
}
