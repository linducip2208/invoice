<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->boolean('supports_recurring')->default(false)->after('is_active');
            $table->string('recurring_format', 50)->nullable()->after('supports_recurring');
        });
    }

    public function down(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->dropColumn(['supports_recurring', 'recurring_format']);
        });
    }
};
