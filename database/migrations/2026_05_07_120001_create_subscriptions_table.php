<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();
            $table->foreignId('payment_gateway_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('status', ['trial', 'active', 'past_due', 'cancelled', 'expired'])
                ->default('active');
            $table->enum('billing_period', ['monthly', 'yearly', 'free'])->default('free');
            $table->string('currency', 3)->default('IDR');

            $table->string('external_subscription_id')->nullable();
            $table->string('external_customer_id')->nullable();

            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_starts_at')->nullable();
            $table->timestamp('current_period_ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->boolean('cancel_at_period_end')->default(false);
            $table->timestamp('last_payment_at')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique('workspace_id');
            $table->index(['status', 'current_period_ends_at']);
            $table->index('external_subscription_id');
        });

        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('external_invoice_id')->nullable();
            $table->string('reference_no');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3);
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('period_starts_at')->nullable();
            $table->timestamp('period_ends_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index(['workspace_id', 'status']);
            $table->index('external_invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_invoices');
        Schema::dropIfExists('subscriptions');
    }
};
