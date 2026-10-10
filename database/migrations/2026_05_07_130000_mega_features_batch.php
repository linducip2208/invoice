<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // P22: Audit log
        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('action', 100);
            $t->string('subject_type', 100)->nullable();
            $t->unsignedBigInteger('subject_id')->nullable();
            $t->string('description', 500);
            $t->json('changes')->nullable();
            $t->ipAddress('ip')->nullable();
            $t->timestamps();
            $t->index(['workspace_id', 'created_at']);
        });

        // P25: Notifications (in-app)
        Schema::create('app_notifications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('type', 50);
            $t->string('title');
            $t->string('message', 500);
            $t->string('action_url')->nullable();
            $t->string('icon', 10)->default('🔔');
            $t->timestamp('read_at')->nullable();
            $t->timestamps();
            $t->index(['workspace_id', 'read_at']);
        });

        // P31: Time tracking
        Schema::create('time_entries', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('description', 500);
            $t->decimal('hours', 8, 2)->default(0);
            $t->decimal('hourly_rate', 12, 2)->default(0);
            $t->date('entry_date');
            $t->boolean('billable')->default(true);
            $t->boolean('invoiced')->default(false);
            $t->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $t->timestamps();
            $t->index(['workspace_id', 'entry_date']);
        });

        // P32: Expenses
        Schema::create('expenses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $t->string('category', 100)->nullable();
            $t->string('description', 500);
            $t->decimal('amount', 12, 2);
            $t->string('currency', 3)->default('IDR');
            $t->date('expense_date');
            $t->string('receipt_path')->nullable();
            $t->boolean('billable')->default(false);
            $t->boolean('invoiced')->default(false);
            $t->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $t->text('notes')->nullable();
            $t->timestamps();
            $t->index(['workspace_id', 'expense_date']);
        });

        // P33: Quotes / estimates
        Schema::create('quotes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('client_id')->constrained()->restrictOnDelete();
            $t->string('quote_no', 50);
            $t->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired', 'converted'])->default('draft');
            $t->date('issue_date');
            $t->date('expiry_date')->nullable();
            $t->decimal('subtotal', 12, 2)->default(0);
            $t->decimal('tax_rate', 5, 2)->default(0);
            $t->decimal('tax_amount', 12, 2)->default(0);
            $t->decimal('discount_value', 12, 2)->default(0);
            $t->string('discount_type', 10)->default('none');
            $t->decimal('discount_amount', 12, 2)->default(0);
            $t->decimal('total', 12, 2)->default(0);
            $t->string('currency', 3)->default('IDR');
            $t->json('items')->nullable();
            $t->text('notes')->nullable();
            $t->text('terms')->nullable();
            $t->foreignId('converted_invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $t->string('public_token', 64)->nullable()->unique();
            $t->timestamp('accepted_at')->nullable();
            $t->timestamps();
            $t->index(['workspace_id', 'status']);
            $t->unique(['workspace_id', 'quote_no']);
        });

        // P36: Invoice attachments
        Schema::create('invoice_attachments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->string('file_name');
            $t->string('file_path');
            $t->unsignedInteger('file_size')->default(0);
            $t->string('mime_type', 100)->nullable();
            $t->timestamps();
            $t->index('invoice_id');
        });

        // P39: Coupons
        Schema::create('coupons', function (Blueprint $t) {
            $t->id();
            $t->string('code', 50)->unique();
            $t->string('description')->nullable();
            $t->enum('discount_type', ['percent', 'fixed']);
            $t->decimal('discount_value', 10, 2);
            $t->string('currency', 3)->nullable();
            $t->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $t->unsignedInteger('max_uses')->nullable();
            $t->unsignedInteger('uses_count')->default(0);
            $t->timestamp('expires_at')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        // P40: Support tickets
        Schema::create('support_tickets', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('subject', 255);
            $t->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $t->enum('status', ['open', 'pending', 'resolved', 'closed'])->default('open');
            $t->timestamp('resolved_at')->nullable();
            $t->timestamps();
            $t->index(['status', 'created_at']);
        });

        Schema::create('support_messages', function (Blueprint $t) {
            $t->id();
            $t->foreignId('ticket_id')->constrained('support_tickets')->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->boolean('is_admin_reply')->default(false);
            $t->text('body');
            $t->timestamps();
        });

        // P38: Affiliate
        Schema::create('affiliates', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->string('code', 50)->unique();
            $t->unsignedInteger('clicks')->default(0);
            $t->unsignedInteger('signups')->default(0);
            $t->unsignedInteger('paid_conversions')->default(0);
            $t->decimal('commission_pct', 5, 2)->default(30);
            $t->decimal('balance_idr', 12, 2)->default(0);
            $t->decimal('balance_usd', 10, 2)->default(0);
            $t->string('payout_method')->nullable();
            $t->json('payout_details')->nullable();
            $t->timestamps();
        });

        // P12: Workspace invites
        Schema::create('workspace_invites', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('invited_by')->constrained('users')->cascadeOnDelete();
            $t->string('email');
            $t->enum('role', ['admin', 'member'])->default('member');
            $t->string('token', 64)->unique();
            $t->timestamp('accepted_at')->nullable();
            $t->timestamp('expires_at');
            $t->timestamps();
            $t->index(['workspace_id', 'email']);
        });

        // P21: API tokens (Business plan) — using Laravel personal_access_tokens equivalent
        Schema::create('workspace_api_tokens', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('token_hash', 64)->unique();
            $t->string('prefix', 12)->index();
            $t->json('abilities')->nullable();
            $t->timestamp('last_used_at')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();
        });

        // P30: Tenant outbound webhooks
        Schema::create('tenant_webhooks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->string('url');
            $t->string('secret', 64);
            $t->json('events');
            $t->boolean('is_active')->default(true);
            $t->timestamp('last_fired_at')->nullable();
            $t->unsignedInteger('failure_count')->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_webhooks');
        Schema::dropIfExists('workspace_api_tokens');
        Schema::dropIfExists('workspace_invites');
        Schema::dropIfExists('affiliates');
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('invoice_attachments');
        Schema::dropIfExists('quotes');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('time_entries');
        Schema::dropIfExists('app_notifications');
        Schema::dropIfExists('audit_logs');
    }
};
