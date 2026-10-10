<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_path')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->default('Indonesia');
            $table->string('postal_code', 20)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('tax_id', 100)->nullable();
            $table->string('invoice_prefix', 20)->default('INV-');
            $table->unsignedInteger('next_invoice_no')->default(1);
            $table->string('default_template', 50)->default('classic');
            $table->string('primary_color', 7)->default('#2563EB');
            $table->string('accent_color', 7)->default('#1E40AF');
            $table->string('currency', 10)->default('IDR');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspaces');
    }
};
