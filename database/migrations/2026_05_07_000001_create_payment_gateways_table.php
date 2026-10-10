<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('name');                          // user-friendly name, e.g. "Midtrans Production"
            $table->string('gateway');                       // identifier: midtrans, xendit, tripay, duitku, doku, oy, ipaymu, finpay, custom
            $table->string('api_format')->default('rest');   // rest, redirect, qris, ewallets
            $table->string('base_url')->nullable();
            $table->string('api_key_encrypted')->nullable();
            $table->string('api_secret_encrypted')->nullable();
            $table->string('merchant_id_encrypted')->nullable();
            $table->string('client_key_encrypted')->nullable();
            $table->string('server_key_encrypted')->nullable();
            $table->json('extra_headers')->nullable();        // additional headers
            $table->json('supported_channels')->nullable();    // ["bank_transfer", "credit_card", "gopay", "ovo", "dana", "shopeepay", "linkaja", "qris", "va", "convenience_store"]
            $table->boolean('is_active')->default(true);
            $table->boolean('is_sandbox')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('workspace_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('payment_gateway_id')->nullable()->after('workspace_id')->constrained()->nullOnDelete();
            $table->string('gateway_transaction_id')->nullable()->after('payment_gateway_id');
            $table->string('channel')->nullable()->after('payment_method'); // e.g. gopay, bca, qris
            $table->string('gateway_status')->nullable()->after('channel');
            $table->json('gateway_raw')->nullable()->after('gateway_status');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['payment_gateway_id']);
            $table->dropColumn(['payment_gateway_id', 'gateway_transaction_id', 'channel', 'gateway_status', 'gateway_raw']);
        });
        Schema::dropIfExists('payment_gateways');
    }
};
