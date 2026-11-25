<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Antrean kirim invoice terjadwal (diproses command per jam). */
    public function up(): void
    {
        Schema::create('scheduled_invoice_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('to_email');
            $table->string('subject');
            $table->text('message')->nullable();
            $table->timestamp('send_at');
            $table->string('status', 20)->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->text('fail_reason')->nullable();
            $table->timestamps();
            $table->index(['status', 'send_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_invoice_sends');
    }
};
