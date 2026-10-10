<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workspaces', function (Blueprint $t) {
            $t->json('whatsapp_config')->nullable()->after('ai_monthly_budget_idr');
            $t->string('industry', 100)->nullable()->after('whatsapp_config');
        });
    }

    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $t) {
            $t->dropColumn(['whatsapp_config', 'industry']);
        });
    }
};
