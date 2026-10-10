<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // email_logs.status did not accept the 'queued' value that
        // EmailInvoiceController writes when mail is queued but not yet
        // delivered. Extend the allowed set (additive only).
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE email_logs MODIFY COLUMN status ENUM('sent','failed','queued') NOT NULL DEFAULT 'sent'");
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            // SQLite emulates enums with a CHECK constraint: rebuild table.
            DB::statement('ALTER TABLE email_logs RENAME TO email_logs_old');
            // The rename keeps the old index alive under its name: drop it so
            // the rebuilt table can reuse the same index name.
            DB::statement('DROP INDEX IF EXISTS email_logs_invoice_id_index');
            Schema::create('email_logs', function ($table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
                $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
                $table->string('to_email');
                $table->string('subject');
                $table->text('body')->nullable();
                $table->enum('status', ['sent', 'failed', 'queued'])->default('sent');
                $table->text('error_message')->nullable();
                $table->timestamp('sent_at')->useCurrent();
                $table->timestamps();
                $table->index('invoice_id');
            });
            DB::statement('INSERT INTO email_logs (id, workspace_id, invoice_id, to_email, subject, body, status, error_message, sent_at, created_at, updated_at) SELECT id, workspace_id, invoice_id, to_email, subject, body, status, error_message, sent_at, created_at, updated_at FROM email_logs_old');
            Schema::drop('email_logs_old');
        }
    }

    public function down(): void
    {
        if (DB::table('email_logs')->where('status', 'queued')->exists()) {
            throw new RuntimeException('Cannot roll back: queued email logs exist.');
        }
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE email_logs MODIFY COLUMN status ENUM('sent','failed') NOT NULL DEFAULT 'sent'");
        }
        // SQLite: keep the wider set (harmless superset, no rebuild needed).
    }
};
