<?php

namespace App\Console\Commands;

use App\Mail\InvoiceReminderMail;
use App\Models\InvoiceReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ProcessRemindersCommand extends Command
{
    protected $signature = 'reminders:process {--dry-run : Show what would be sent without sending}';
    protected $description = 'Process pending invoice reminders that are due to send';

    public function handle(): int
    {
        $due = InvoiceReminder::query()
            ->withoutGlobalScope('workspace')
            ->where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->with(['invoice.client', 'invoice.workspace'])
            ->limit(200)
            ->get();

        if ($due->isEmpty()) {
            $this->info('No pending reminders due.');
            return self::SUCCESS;
        }

        $this->info("Processing {$due->count()} reminders…");

        $sent = $failed = $skipped = 0;

        foreach ($due as $reminder) {
            $invoice = $reminder->invoice;

            if (! $invoice || in_array($invoice->status, ['paid', 'cancelled'])) {
                $reminder->update(['status' => 'cancelled']);
                $skipped++;
                continue;
            }

            if (! $invoice->client?->email) {
                $reminder->update(['status' => 'failed', 'fail_reason' => 'Client email kosong']);
                $failed++;
                continue;
            }

            if ($invoice->client->no_reminders) {
                $reminder->update(['status' => 'cancelled']);
                $skipped++;
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("  [DRY] {$invoice->invoice_no} → {$invoice->client->email} (offset {$reminder->schedule_offset})");
                continue;
            }

            try {
                Mail::to($invoice->client->email)->send(
                    new InvoiceReminderMail($invoice, $reminder->template_subject, $reminder->template_body)
                );
                $reminder->update(['status' => 'sent', 'sent_at' => now()]);
                $sent++;
                $this->line("  ✓ {$invoice->invoice_no} → {$invoice->client->email}");
            } catch (\Throwable $e) {
                $reminder->update(['status' => 'failed', 'fail_reason' => substr($e->getMessage(), 0, 500)]);
                $failed++;
                $this->error("  ✗ {$invoice->invoice_no}: {$e->getMessage()}");
            }
        }

        $this->info("Done. Sent: {$sent}, Failed: {$failed}, Skipped: {$skipped}");
        return self::SUCCESS;
    }
}
