<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_portal_users', function (Blueprint $t) {
            $t->boolean('can_approve_quotes')->default(false)->after('email_verified_at');
            $t->boolean('is_active')->default(true)->after('can_approve_quotes');
        });

        Schema::table('workspaces', function (Blueprint $t) {
            $t->string('quote_auto_convert', 20)->default('manual')->after('quote_prefix');
            // Null = no portal-only enforcement; set an amount to require
            // authenticated designated-approver acceptance above it.
            $t->decimal('quote_high_risk_threshold', 15, 2)->nullable()->after('quote_auto_convert');
        });
    }

    public function down(): void
    {
        Schema::table('client_portal_users', function (Blueprint $t) {
            $t->dropColumn(['can_approve_quotes', 'is_active']);
        });
        Schema::table('workspaces', function (Blueprint $t) {
            $t->dropColumn(['quote_auto_convert', 'quote_high_risk_threshold']);
        });
    }
};
