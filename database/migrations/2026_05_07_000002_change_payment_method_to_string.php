<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: change enum to string for dynamic payment methods
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method')->default('bank_transfer')->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'bank_transfer', 'credit_card', 'other'])->default('bank_transfer')->change();
        });
    }
};
