<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Append-only acceptance/decline evidence. One row per (quote, revision,
        // outcome attempt that reached a decision); repeats are idempotent by
        // unique(quote_id, revision, outcome, approver_key).
        Schema::create('quote_approval_events', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('revision')->default(1);
            $t->foreignId('client_id')->constrained()->restrictOnDelete();
            $t->foreignId('approver_portal_user_id')->nullable()->constrained('client_portal_users')->nullOnDelete();
            $t->string('approver_name', 120);
            $t->string('approver_role', 120)->nullable();
            $t->string('approver_email', 255);
            $t->string('verification_method', 30); // portal | otp | manual
            $t->string('authority_basis', 30); // designated_approver | verified_contact | workspace_manual
            $t->string('consent_text_version', 20);
            $t->text('consent_text_snapshot');
            $t->char('document_sha256', 64);
            $t->char('pdf_sha256', 64)->nullable();
            $t->enum('outcome', ['accepted', 'rejected']);
            $t->string('decline_reason', 500)->nullable();
            $t->timestamp('decided_at');
            $t->ipAddress('ip')->nullable();
            $t->string('user_agent', 500)->nullable();
            $t->string('correlation_id', 64)->nullable();
            $t->timestamps();

            $t->unique(['quote_id', 'revision', 'outcome', 'approver_email'], 'qqappr_uniq');
            $t->index(['workspace_id', 'outcome']);
            $t->index('decided_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_approval_events');
    }
};
