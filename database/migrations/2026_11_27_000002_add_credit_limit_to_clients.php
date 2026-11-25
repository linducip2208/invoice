<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Batas piutang per klien; null = tanpa batas. */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->decimal('credit_limit', 15, 2)->nullable()->after('tax_id');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('credit_limit');
        });
    }
};
