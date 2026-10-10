<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Roadmap v2 — Mega Migration
 *
 * Adds 26 new tables + column modifications across 8 existing tables to support
 * features specced in docs/roadmap-v2/.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ──────────────────────────────────────────────────────────────────
        // Sprint 1 — Cash Flow
        // ──────────────────────────────────────────────────────────────────

        Schema::create('invoice_reminders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $t->integer('schedule_offset');
            $t->timestamp('scheduled_at');
            $t->timestamp('sent_at')->nullable();
            $t->string('template_subject', 200);
            $t->text('template_body');
            $t->enum('status', ['pending', 'sent', 'failed', 'cancelled'])->default('pending');
            $t->string('fail_reason', 500)->nullable();
            $t->timestamps();
            $t->index(['workspace_id', 'status', 'scheduled_at']);
            $t->index('invoice_id');
        });

        Schema::create('invoice_late_fees', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $t->timestamp('applied_at');
            $t->decimal('amount', 12, 2);
            $t->enum('type', ['percent', 'fixed']);
            $t->decimal('percent_value', 5, 2)->nullable();
            $t->decimal('fixed_amount', 12, 2)->nullable();
            $t->string('description', 200)->default('Late fee');
            $t->timestamps();
            $t->index('invoice_id');
        });

        Schema::create('credit_notes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $t->string('credit_no', 50);
            $t->string('reason', 300);
            $t->decimal('amount', 12, 2);
            $t->string('currency', 3);
            $t->date('issued_at');
            $t->enum('status', ['draft', 'issued', 'applied', 'cancelled'])->default('draft');
            $t->text('notes')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->unique(['workspace_id', 'credit_no']);
            $t->index(['workspace_id', 'issued_at']);
        });

        // ──────────────────────────────────────────────────────────────────
        // Sprint 2 — Indonesia
        // ──────────────────────────────────────────────────────────────────

        Schema::create('invoice_tax_details', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $t->decimal('ppn_rate', 5, 2)->default(11.00);
            $t->decimal('ppn_amount', 12, 2)->default(0);
            $t->decimal('pph23_rate', 5, 2)->default(0);
            $t->decimal('pph23_amount', 12, 2)->default(0);
            $t->string('pph23_object_code', 20)->nullable();
            $t->enum('efaktur_status', ['not_applicable', 'draft', 'approved', 'rejected'])->default('not_applicable');
            $t->string('efaktur_no', 50)->nullable();
            $t->string('efaktur_qr_code', 500)->nullable();
            $t->string('efaktur_xml_path', 255)->nullable();
            $t->json('coretax_response_json')->nullable();
            $t->timestamps();
            $t->unique('invoice_id');
            $t->index(['workspace_id', 'efaktur_status']);
        });

        Schema::create('whatsapp_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('quote_id')->nullable()->constrained('quotes')->nullOnDelete();
            $t->string('to_phone', 20);
            $t->string('template_name', 100)->nullable();
            $t->text('message_body');
            $t->enum('status', ['queued', 'sent', 'delivered', 'read', 'failed'])->default('queued');
            $t->string('provider_msg_id', 120)->nullable();
            $t->string('fail_reason', 500)->nullable();
            $t->timestamp('sent_at')->nullable();
            $t->timestamp('delivered_at')->nullable();
            $t->timestamp('read_at')->nullable();
            $t->timestamps();
            $t->index(['workspace_id', 'sent_at']);
            $t->index('invoice_id');
        });

        Schema::create('qris_codes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $t->foreignId('provider_id')->nullable()->constrained('payment_gateways')->nullOnDelete();
            $t->decimal('amount', 12, 2);
            $t->text('qr_payload');
            $t->string('qr_image_path', 255);
            $t->timestamp('expires_at');
            $t->enum('status', ['active', 'paid', 'expired', 'cancelled'])->default('active');
            $t->timestamp('paid_at')->nullable();
            $t->string('provider_ref', 120)->nullable();
            $t->timestamps();
            $t->index(['workspace_id', 'status']);
        });

        Schema::create('npwp_validations', function (Blueprint $t) {
            $t->id();
            $t->string('npwp_number', 20);
            $t->string('canonical_npwp', 16);
            $t->boolean('is_valid');
            $t->string('name_djp', 255)->nullable();
            $t->string('status_djp', 50)->nullable();
            $t->timestamp('last_checked_at');
            $t->json('provider_response')->nullable();
            $t->timestamps();
            $t->unique('canonical_npwp');
            $t->index('last_checked_at');
        });

        Schema::create('bank_reconcile_batches', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->string('bank_name', 50);
            $t->string('account_no', 50);
            $t->string('file_path', 255);
            $t->enum('file_format', ['csv', 'ofx', 'mt940', 'json']);
            $t->foreignId('imported_by')->nullable()->constrained('users')->nullOnDelete();
            $t->integer('total_lines');
            $t->integer('matched_count')->default(0);
            $t->integer('unmatched_count')->default(0);
            $t->enum('status', ['processing', 'review', 'completed'])->default('processing');
            $t->timestamps();
        });

        Schema::create('bank_reconcile_lines', function (Blueprint $t) {
            $t->id();
            $t->foreignId('batch_id')->constrained('bank_reconcile_batches')->cascadeOnDelete();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->date('trx_date');
            $t->decimal('amount', 12, 2);
            $t->string('description', 500);
            $t->foreignId('matched_invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $t->foreignId('matched_payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $t->enum('match_confidence', ['high', 'medium', 'low', 'manual', 'none'])->default('none');
            $t->enum('match_method', ['auto_amount_date', 'auto_reference', 'manual', 'none'])->default('none');
            $t->string('notes', 500)->nullable();
            $t->timestamps();
            $t->index('batch_id');
            $t->index(['workspace_id', 'trx_date']);
        });

        // ──────────────────────────────────────────────────────────────────
        // Sprint 3 — Customer Experience
        // ──────────────────────────────────────────────────────────────────

        Schema::create('invoice_comments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $t->foreignId('author_user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('author_client_id')->nullable()->constrained('clients')->nullOnDelete();
            $t->string('author_name', 120);
            $t->string('author_email', 255)->nullable();
            $t->text('body');
            $t->boolean('is_internal')->default(false);
            $t->foreignId('parent_id')->nullable()->constrained('invoice_comments')->cascadeOnDelete();
            $t->json('attachments_json')->nullable();
            $t->timestamps();
            $t->index(['invoice_id', 'created_at']);
        });

        Schema::create('client_portal_users', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained()->cascadeOnDelete();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->string('email', 255);
            $t->string('password_hash', 255);
            $t->timestamp('last_login_at')->nullable();
            $t->string('remember_token', 100)->nullable();
            $t->timestamp('email_verified_at')->nullable();
            $t->timestamps();
            $t->unique(['client_id', 'email']);
            $t->index('workspace_id');
        });

        Schema::create('coupon_redemptions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $t->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $t->string('subject_type', 50);
            $t->unsignedBigInteger('subject_id');
            $t->decimal('discount_applied', 12, 2);
            $t->timestamp('redeemed_at');
            $t->timestamps();
            $t->index(['coupon_id', 'redeemed_at']);
            $t->index(['subject_type', 'subject_id']);
        });

        // ──────────────────────────────────────────────────────────────────
        // Sprint 4 — Automation & Scale
        // ──────────────────────────────────────────────────────────────────

        Schema::create('approval_rules', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->string('name', 120);
            $t->enum('applies_to', ['invoice', 'quote', 'expense', 'credit_note']);
            $t->enum('condition_type', ['amount_gte', 'always', 'custom_field'])->default('amount_gte');
            $t->decimal('condition_value', 12, 2)->nullable();
            $t->json('approver_user_ids');
            $t->boolean('require_all')->default(false);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->index(['workspace_id', 'applies_to']);
        });

        Schema::create('approval_requests', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('rule_id')->nullable()->constrained('approval_rules')->nullOnDelete();
            $t->string('subject_type', 100);
            $t->unsignedBigInteger('subject_id');
            $t->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $t->enum('status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
            $t->json('approver_responses')->nullable();
            $t->timestamp('final_decision_at')->nullable();
            $t->text('notes')->nullable();
            $t->timestamps();
            $t->index(['workspace_id', 'status']);
            $t->index(['subject_type', 'subject_id']);
        });

        Schema::create('webhook_deliveries', function (Blueprint $t) {
            $t->id();
            $t->foreignId('webhook_id')->constrained('tenant_webhooks')->cascadeOnDelete();
            $t->string('event', 50);
            $t->json('payload');
            $t->integer('status_code')->nullable();
            $t->text('response_body')->nullable();
            $t->integer('attempt')->default(1);
            $t->timestamp('delivered_at')->nullable();
            $t->timestamp('next_retry_at')->nullable();
            $t->enum('final_status', ['pending', 'success', 'failed', 'exhausted'])->default('pending');
            $t->timestamps();
            $t->index(['webhook_id', 'final_status']);
            $t->index('next_retry_at');
        });

        Schema::create('accounting_exports', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->enum('format', ['jurnal', 'mekari', 'quickbooks', 'xero', 'csv_generic']);
            $t->date('period_start');
            $t->date('period_end');
            $t->string('file_path', 255);
            $t->integer('record_count');
            $t->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->index(['workspace_id', 'created_at']);
        });

        Schema::create('email_inbox_forwards', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->string('inbound_address', 255);
            $t->timestamp('forwarded_at');
            $t->string('from_email', 255);
            $t->string('subject', 500)->nullable();
            $t->integer('attachments_count')->default(0);
            $t->enum('status', ['processing', 'converted', 'duplicate', 'failed', 'manual_review'])->default('processing');
            $t->foreignId('expense_id')->nullable()->constrained('expenses')->nullOnDelete();
            $t->string('raw_email_path', 255)->nullable();
            $t->json('ai_extracted_data')->nullable();
            $t->string('error_message', 500)->nullable();
            $t->timestamps();
            $t->index(['workspace_id', 'status']);
        });

        // ──────────────────────────────────────────────────────────────────
        // Sprint 5 — Smart / AI / Project
        // ──────────────────────────────────────────────────────────────────

        Schema::create('ai_providers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->string('name', 120);
            $t->enum('api_format', ['openai_compatible', 'anthropic', 'gemini', 'image_generic', 'ocr_generic']);
            $t->string('base_url', 500);
            $t->text('api_key_encrypted');
            $t->json('extra_headers')->nullable();
            $t->string('default_model', 100)->nullable();
            $t->decimal('default_pricing_input', 10, 6)->nullable();
            $t->decimal('default_pricing_output', 10, 6)->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->index(['workspace_id', 'is_active']);
        });

        Schema::create('ai_feature_mapping', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->string('feature', 50);
            $t->foreignId('provider_id')->nullable()->constrained('ai_providers')->nullOnDelete();
            $t->string('model', 100)->nullable();
            $t->boolean('is_enabled')->default(true);
            $t->json('config')->nullable();
            $t->timestamps();
            $t->unique(['workspace_id', 'feature']);
            $t->index('workspace_id');
        });

        Schema::create('ai_usage_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('provider_id')->nullable()->constrained('ai_providers')->nullOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('feature', 50);
            $t->string('model', 100)->nullable();
            $t->integer('input_tokens')->default(0);
            $t->integer('output_tokens')->default(0);
            $t->decimal('cost_estimate', 10, 4)->default(0);
            $t->integer('duration_ms')->default(0);
            $t->enum('status', ['success', 'error', 'timeout'])->default('success');
            $t->string('error_message', 500)->nullable();
            $t->timestamps();
            $t->index(['workspace_id', 'created_at']);
            $t->index('provider_id');
        });

        Schema::create('projects', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('client_id')->constrained()->cascadeOnDelete();
            $t->string('name', 200);
            $t->string('slug', 100);
            $t->text('description')->nullable();
            $t->enum('status', ['active', 'on_hold', 'completed', 'cancelled'])->default('active');
            $t->decimal('budget_amount', 12, 2)->nullable();
            $t->decimal('budget_hours', 8, 2)->nullable();
            $t->decimal('hourly_rate', 12, 2)->nullable();
            $t->date('start_date')->nullable();
            $t->date('end_date')->nullable();
            $t->string('color', 7)->nullable();
            $t->timestamps();
            $t->index(['workspace_id', 'status']);
            $t->unique(['workspace_id', 'slug']);
        });

        Schema::create('expense_categories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->string('name', 60);
            $t->string('icon', 10)->nullable();
            $t->string('color', 7)->nullable();
            $t->boolean('is_default')->default(false);
            $t->timestamps();
            $t->unique(['workspace_id', 'name']);
        });

        // ──────────────────────────────────────────────────────────────────
        // Sprint 6 — Differentiator & Compliance
        // ──────────────────────────────────────────────────────────────────

        Schema::create('signatures', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->string('subject_type', 100);
            $t->unsignedBigInteger('subject_id');
            $t->string('signer_name', 120);
            $t->string('signer_email', 255);
            $t->string('signer_role', 50)->nullable();
            $t->string('signature_image_path', 255);
            $t->string('signature_typed', 120)->nullable();
            $t->string('ip', 45);
            $t->string('ua', 500);
            $t->timestamp('signed_at');
            $t->string('verification_hash', 64);
            $t->timestamps();
            $t->index(['workspace_id', 'subject_type', 'subject_id']);
        });

        Schema::create('user_2fa_secrets', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->text('secret_encrypted');
            $t->json('recovery_codes_encrypted');
            $t->timestamp('enabled_at')->nullable();
            $t->timestamp('last_used_at')->nullable();
            $t->timestamps();
        });

        Schema::create('nps_responses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $t->tinyInteger('score');
            $t->text('comment')->nullable();
            $t->timestamp('submitted_at');
            $t->string('ip', 45)->nullable();
            $t->string('ua', 500)->nullable();
            $t->string('public_token', 64)->nullable()->unique();
            $t->timestamps();
            $t->index(['workspace_id', 'score']);
        });

        Schema::create('push_subscriptions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $t->string('endpoint', 500);
            $t->string('keys_p256dh', 255);
            $t->string('keys_auth', 255);
            $t->string('device_label', 120)->nullable();
            $t->string('ua', 500)->nullable();
            $t->timestamp('last_used_at')->nullable();
            $t->timestamps();
            $t->index('user_id');
        });

        // ──────────────────────────────────────────────────────────────────
        // Sprint 7 — Premium / Enterprise
        // ──────────────────────────────────────────────────────────────────

        Schema::create('custom_domains', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->string('hostname', 253);
            $t->string('verification_token', 64);
            $t->enum('verification_method', ['cname', 'txt']);
            $t->enum('status', ['pending', 'verified', 'failed', 'disabled'])->default('pending');
            $t->timestamp('ssl_issued_at')->nullable();
            $t->timestamp('ssl_expires_at')->nullable();
            $t->timestamp('verified_at')->nullable();
            $t->timestamp('last_check_at')->nullable();
            $t->timestamps();
            $t->unique('hostname');
            $t->index('workspace_id');
        });

        Schema::create('external_oauth_identities', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->enum('provider', ['google', 'microsoft', 'apple', 'github']);
            $t->string('provider_user_id', 255);
            $t->string('email', 255);
            $t->string('name', 120)->nullable();
            $t->string('avatar_url', 500)->nullable();
            $t->text('token_encrypted')->nullable();
            $t->timestamp('linked_at');
            $t->timestamps();
            $t->unique(['provider', 'provider_user_id']);
            $t->index('user_id');
        });

        Schema::create('dashboard_widgets', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('widget_type', 50);
            $t->integer('position')->default(0);
            $t->enum('size', ['sm', 'md', 'lg', 'xl'])->default('md');
            $t->json('config')->nullable();
            $t->boolean('is_visible')->default(true);
            $t->timestamps();
            $t->index(['workspace_id', 'user_id', 'position']);
        });

        Schema::create('data_export_requests', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->enum('status', ['pending', 'processing', 'ready', 'expired'])->default('pending');
            $t->string('file_path', 255)->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamp('requested_at');
            $t->timestamp('ready_at')->nullable();
            $t->timestamps();
            $t->index(['user_id', 'status']);
        });

        Schema::create('report_subscriptions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('report_type', 50);
            $t->enum('frequency', ['weekly', 'monthly', 'quarterly']);
            $t->tinyInteger('day_of_week')->nullable();
            $t->tinyInteger('day_of_month')->nullable();
            $t->tinyInteger('hour')->default(8);
            $t->timestamp('last_sent_at')->nullable();
            $t->timestamp('next_send_at');
            $t->boolean('is_active')->default(true);
            $t->json('config')->nullable();
            $t->timestamps();
            $t->index(['workspace_id', 'is_active', 'next_send_at']);
        });

        Schema::create('invoice_translations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $t->string('locale', 10);
            $t->json('fields');
            $t->foreignId('generated_by_provider_id')->nullable()->constrained('ai_providers')->nullOnDelete();
            $t->timestamp('translated_at');
            $t->timestamps();
            $t->unique(['invoice_id', 'locale']);
        });

        // ──────────────────────────────────────────────────────────────────
        // Column modifications on existing tables
        // ──────────────────────────────────────────────────────────────────

        Schema::table('invoices', function (Blueprint $t) {
            $t->foreignId('project_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
            $t->foreignId('quote_id')->nullable()->after('project_id')->constrained('quotes')->nullOnDelete();
            $t->decimal('pph23_rate', 5, 2)->default(0)->after('tax_amount');
            $t->decimal('pph23_amount', 12, 2)->default(0)->after('pph23_rate');
            $t->decimal('late_fee_total', 12, 2)->default(0)->after('total');
            $t->timestamp('signed_at')->nullable()->after('updated_at');
            $t->tinyInteger('late_risk_score')->nullable()->after('signed_at');
            $t->json('late_risk_factors')->nullable()->after('late_risk_score');
            $t->foreignId('assigned_to')->nullable()->after('late_risk_factors')->constrained('users')->nullOnDelete();
            $t->enum('late_fee_policy', ['none', 'one_time', 'monthly'])->default('none')->after('assigned_to');
            $t->decimal('late_fee_value', 10, 2)->default(0)->after('late_fee_policy');
            $t->enum('late_fee_type', ['percent', 'fixed'])->default('percent')->after('late_fee_value');
        });

        Schema::table('clients', function (Blueprint $t) {
            $t->decimal('default_pph23_rate', 5, 2)->default(0)->after('tax_id');
            $t->boolean('is_pkp')->default(false)->after('default_pph23_rate');
            $t->boolean('npwp_verified')->default(false)->after('is_pkp');
            $t->timestamp('npwp_verified_at')->nullable()->after('npwp_verified');
            $t->boolean('no_reminders')->default(false)->after('npwp_verified_at');
            $t->integer('avg_payment_days')->nullable()->after('no_reminders');
            $t->enum('payment_reliability', ['excellent', 'good', 'fair', 'poor'])->nullable()->after('avg_payment_days');
            $t->integer('total_invoices_count')->default(0)->after('payment_reliability');
            $t->integer('total_paid_count')->default(0)->after('total_invoices_count');
            $t->foreignId('default_assignee_id')->nullable()->after('total_paid_count')->constrained('users')->nullOnDelete();
        });

        Schema::table('payments', function (Blueprint $t) {
            $t->foreignId('bank_reconcile_line_id')->nullable()->after('id')->constrained('bank_reconcile_lines')->nullOnDelete();
        });

        Schema::table('subscriptions', function (Blueprint $t) {
            $t->foreignId('coupon_id')->nullable()->after('plan_id')->constrained()->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $t) {
            $t->boolean('two_factor_enabled')->default(false)->after('password');
        });

        Schema::table('time_entries', function (Blueprint $t) {
            $t->foreignId('project_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
            $t->timestamp('started_at')->nullable()->after('billable');
            $t->timestamp('ended_at')->nullable()->after('started_at');
            $t->boolean('is_running')->default(false)->after('ended_at');
            $t->json('tags')->nullable()->after('is_running');
        });

        Schema::table('expenses', function (Blueprint $t) {
            $t->foreignId('project_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
            $t->decimal('markup_percent', 5, 2)->default(0)->after('billable');
            $t->string('vendor_name', 120)->nullable()->after('markup_percent');
            $t->enum('ocr_status', ['not_ocr', 'processing', 'done', 'failed'])->nullable()->after('vendor_name');
            $t->json('ocr_extracted')->nullable()->after('ocr_status');
            $t->foreignId('ocr_provider_id')->nullable()->after('ocr_extracted')->constrained('ai_providers')->nullOnDelete();
            $t->string('ai_category_suggested', 50)->nullable()->after('ocr_provider_id');
            $t->decimal('ai_category_confidence', 4, 2)->nullable()->after('ai_category_suggested');
        });

        Schema::table('quotes', function (Blueprint $t) {
            $t->foreignId('project_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
            $t->timestamp('signed_at')->nullable()->after('accepted_at');
        });

        Schema::table('coupons', function (Blueprint $t) {
            $t->enum('applies_to', ['subscription', 'invoice', 'both'])->default('subscription')->after('discount_value');
            $t->decimal('min_amount', 12, 2)->nullable()->after('applies_to');
            $t->foreignId('workspace_id')->nullable()->after('min_amount')->constrained()->cascadeOnDelete();
            $t->boolean('allow_stack')->default(false)->after('workspace_id');
        });

        Schema::table('workspaces', function (Blueprint $t) {
            $t->boolean('is_pkp')->default(false)->after('tax_id');
            $t->string('pkp_no', 30)->nullable()->after('is_pkp');
            $t->string('pkp_certificate_path', 255)->nullable()->after('pkp_no');
            $t->string('quote_prefix', 20)->default('QUO-')->after('invoice_prefix');
            $t->integer('next_quote_no')->default(1)->after('quote_prefix');
            $t->string('invoice_number_format', 100)->default('INV-{seq:000}')->after('next_quote_no');
            $t->enum('reset_period', ['never', 'monthly', 'yearly'])->default('never')->after('invoice_number_format');
            $t->decimal('default_late_fee_value', 10, 2)->default(0)->after('reset_period');
            $t->enum('default_late_fee_type', ['percent', 'fixed'])->default('percent')->after('default_late_fee_value');
            $t->enum('default_late_fee_policy', ['none', 'one_time', 'monthly'])->default('none')->after('default_late_fee_type');
            $t->boolean('enable_client_comments')->default(true)->after('default_late_fee_policy');
            $t->enum('sso_required', ['none', 'google', 'microsoft', 'any'])->default('none')->after('enable_client_comments');
            $t->json('widget_allowed_domains')->nullable()->after('sso_required');
            $t->decimal('ai_monthly_budget_idr', 10, 2)->nullable()->after('widget_allowed_domains');
        });
    }

    public function down(): void
    {
        // Drop columns in reverse order
        Schema::table('workspaces', function (Blueprint $t) {
            $t->dropColumn([
                'is_pkp', 'pkp_no', 'pkp_certificate_path', 'quote_prefix', 'next_quote_no',
                'invoice_number_format', 'reset_period', 'default_late_fee_value',
                'default_late_fee_type', 'default_late_fee_policy', 'enable_client_comments',
                'sso_required', 'widget_allowed_domains', 'ai_monthly_budget_idr',
            ]);
        });

        Schema::table('coupons', function (Blueprint $t) {
            $t->dropForeign(['workspace_id']);
            $t->dropColumn(['applies_to', 'min_amount', 'workspace_id', 'allow_stack']);
        });

        Schema::table('quotes', function (Blueprint $t) {
            $t->dropForeign(['project_id']);
            $t->dropColumn(['project_id', 'signed_at']);
        });

        Schema::table('expenses', function (Blueprint $t) {
            $t->dropForeign(['project_id']);
            $t->dropForeign(['ocr_provider_id']);
            $t->dropColumn(['project_id', 'markup_percent', 'vendor_name', 'ocr_status', 'ocr_extracted', 'ocr_provider_id', 'ai_category_suggested', 'ai_category_confidence']);
        });

        Schema::table('time_entries', function (Blueprint $t) {
            $t->dropForeign(['project_id']);
            $t->dropColumn(['project_id', 'started_at', 'ended_at', 'is_running', 'tags']);
        });

        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn('two_factor_enabled');
        });

        Schema::table('subscriptions', function (Blueprint $t) {
            $t->dropForeign(['coupon_id']);
            $t->dropColumn('coupon_id');
        });

        Schema::table('payments', function (Blueprint $t) {
            $t->dropForeign(['bank_reconcile_line_id']);
            $t->dropColumn('bank_reconcile_line_id');
        });

        Schema::table('clients', function (Blueprint $t) {
            $t->dropForeign(['default_assignee_id']);
            $t->dropColumn([
                'default_pph23_rate', 'is_pkp', 'npwp_verified', 'npwp_verified_at',
                'no_reminders', 'avg_payment_days', 'payment_reliability',
                'total_invoices_count', 'total_paid_count', 'default_assignee_id',
            ]);
        });

        Schema::table('invoices', function (Blueprint $t) {
            $t->dropForeign(['project_id']);
            $t->dropForeign(['quote_id']);
            $t->dropForeign(['assigned_to']);
            $t->dropColumn([
                'project_id', 'quote_id', 'pph23_rate', 'pph23_amount',
                'late_fee_total', 'signed_at', 'late_risk_score', 'late_risk_factors',
                'assigned_to', 'late_fee_policy', 'late_fee_value', 'late_fee_type',
            ]);
        });

        // Drop tables in reverse dependency order
        Schema::dropIfExists('invoice_translations');
        Schema::dropIfExists('report_subscriptions');
        Schema::dropIfExists('data_export_requests');
        Schema::dropIfExists('dashboard_widgets');
        Schema::dropIfExists('external_oauth_identities');
        Schema::dropIfExists('custom_domains');
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('nps_responses');
        Schema::dropIfExists('user_2fa_secrets');
        Schema::dropIfExists('signatures');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('ai_usage_logs');
        Schema::dropIfExists('ai_feature_mapping');
        Schema::dropIfExists('ai_providers');
        Schema::dropIfExists('email_inbox_forwards');
        Schema::dropIfExists('accounting_exports');
        Schema::dropIfExists('webhook_deliveries');
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('approval_rules');
        Schema::dropIfExists('coupon_redemptions');
        Schema::dropIfExists('client_portal_users');
        Schema::dropIfExists('invoice_comments');
        Schema::dropIfExists('bank_reconcile_lines');
        Schema::dropIfExists('bank_reconcile_batches');
        Schema::dropIfExists('npwp_validations');
        Schema::dropIfExists('qris_codes');
        Schema::dropIfExists('whatsapp_logs');
        Schema::dropIfExists('invoice_tax_details');
        Schema::dropIfExists('credit_notes');
        Schema::dropIfExists('invoice_late_fees');
        Schema::dropIfExists('invoice_reminders');
    }
};
