<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'credit_card', 'other'])->default('bank_transfer');
            $table->string('reference_no', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('invoice_id');
            $table->index('workspace_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
