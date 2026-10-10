<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Short-lived approval challenges (OTP). Secrets are NEVER stored:
        // only SHA-256 of the verifier token and bcrypt of the 6-digit OTP.
        Schema::create('quote_approval_challenges', function (Blueprint $t) {
            $t->id();
            $t->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $t->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('revision');
            $t->string('contact_email', 255);
            $t->foreignId('portal_user_id')->nullable()->constrained('client_portal_users')->nullOnDelete();
            $t->enum('purpose', ['accept', 'reject']);
            $t->char('token_hash', 64)->unique();
            $t->string('otp_hash', 255);
            $t->timestamp('expires_at');
            $t->unsignedTinyInteger('attempts')->default(0);
            $t->unsignedTinyInteger('max_attempts')->default(5);
            $t->timestamp('consumed_at')->nullable();
            $t->timestamp('last_sent_at')->nullable();
            $t->timestamps();

            $t->index(['quote_id', 'revision']);
            $t->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_approval_challenges');
    }
};
