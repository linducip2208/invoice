<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->json('custom_layout')->nullable()->after('template');
            $table->boolean('is_locked')->default(false)->after('status');
            $table->index('is_locked');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['is_locked']);
            $table->dropColumn(['custom_layout', 'is_locked']);
        });
    }
};
