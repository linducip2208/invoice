<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $t) {
            $t->enum('payment_method', ['gateway', 'manual'])->default('gateway')->after('payment_gateway_id');
            $t->string('manual_proof_path')->nullable()->after('payment_method');
            $t->text('manual_payment_note')->nullable()->after('manual_proof_path');
        });

        // Status enum already includes 'past_due' which we'll repurpose for "pending verification"
        // Add new status 'pending' for manual payment awaiting confirmation
        Schema::table('subscriptions', function (Blueprint $t) {
            $t->string('status', 20)->default('active')->change();
        });

        // App-level manual payment instructions (bank account, etc.)
        Schema::create('manual_payment_methods', function (Blueprint $t) {
            $t->id();
            $t->string('label');
            $t->string('type', 30);
            $t->string('account_name')->nullable();
            $t->string('account_number')->nullable();
            $t->string('bank_or_provider')->nullable();
            $t->text('instructions')->nullable();
            $t->boolean('is_active')->default(true);
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_payment_methods');
        Schema::table('subscriptions', function (Blueprint $t) {
            $t->dropColumn(['payment_method', 'manual_proof_path', 'manual_payment_note']);
        });
    }
};
