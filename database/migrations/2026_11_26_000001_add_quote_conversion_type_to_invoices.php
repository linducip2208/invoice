<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bedakan invoice hasil konversi penuh vs uang muka (DP) dari quote
     * yang sama. Aditif: kolom nullable-default, tidak mengubah data lama
     * (semua konversi lama = 'full').
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('quote_conversion_type', 20)->default('full')->after('quote_revision');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('quote_conversion_type');
        });
    }
};
