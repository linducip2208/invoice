<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->string('template', 50)->default('classic');
            $table->string('primary_color', 7)->nullable();
            $table->string('accent_color', 7)->nullable();
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'yearly'])->default('monthly');
            $table->unsignedInteger('interval_value')->default(1);
            $table->date('next_run_date');
            $table->date('end_date')->nullable();
            $table->unsignedInteger('max_occurrences')->nullable();
            $table->unsignedInteger('current_occurrence')->default(0);
            $table->enum('status', ['active', 'paused', 'completed'])->default('active');
            $table->unsignedBigInteger('last_invoice_id')->nullable();
            $table->json('invoice_data');
            $table->timestamps();

            $table->index(['next_run_date', 'status']);
            $table->index('workspace_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_invoices');
    }
};
