<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->string('invoice_no', 50);
            $table->enum('status', ['draft', 'sent', 'paid', 'partial', 'overdue', 'cancelled'])->default('draft');
            $table->foreignId('recurring_invoice_id')->nullable()->constrained('recurring_invoices')->nullOnDelete();
            $table->string('template', 50)->default('classic');
            $table->string('primary_color', 7)->nullable();
            $table->string('accent_color', 7)->nullable();
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->enum('discount_type', ['fixed', 'percent', 'none'])->default('none');
            $table->decimal('discount_value', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('balance_due', 15, 2)->default(0);
            $table->string('currency', 10)->default('IDR');
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->char('public_token', 36)->unique();
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamps();

            $table->index(['workspace_id', 'status']);
            $table->index('client_id');
            $table->index('public_token');
            $table->index(['due_date', 'status']);
            $table->unique(['workspace_id', 'invoice_no']);
        });

        // Add foreign key in recurring_invoices that points back here
        Schema::table('recurring_invoices', function (Blueprint $table) {
            // The column already exists, constraint was deferred
        });
    }

    public function down(): void
    {
        Schema::table('recurring_invoices', function (Blueprint $table) {
            $table->dropForeign(['last_invoice_id']);
        });
        Schema::dropIfExists('invoices');
    }
};
