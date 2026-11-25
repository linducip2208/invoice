<?php

namespace App\Console\Commands;

use App\Mail\InvoiceMail;
use App\Models\EmailLog;
use App\Models\ScheduledInvoiceSend;
use App\Services\InvoiceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendScheduledInvoicesCommand extends Command
{
    protected $signature = 'invoices:send-scheduled {--dry-run : Show what would be sent without sending}';
    protected $description = 'Kirim invoice terjadwal yang sudah jatuh tempo';

    public function handle(InvoiceService $invoiceService): int
    {
        $due = ScheduledInvoiceSend::query()
            ->withoutGlobalScope('workspace')
            ->where('status', 'pending')
            ->where('send_at', '<=', now())
            ->with(['invoice.client', 'invoice.workspace'])
            ->limit(100)
            ->get();

        if ($due->isEmpty()) {
            $this->info('No scheduled sends due.');
            return self::SUCCESS;
        }

        $sent = $failed = $skipped = 0;

        foreach ($due as $job) {
            $invoice = $job->invoice;

            if (! $invoice || in_array($invoice->status, ['paid', 'cancelled'], true)) {
                $job->update(['status' => 'cancelled']);
                $skipped++;
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("Would send {$invoice->invoice_no} to {$job->to_email}");
                $sent++;
                continue;
            }

            try {
                $pdfPath = $invoiceService->generatePdf($invoice);
                Mail::to($job->to_email)->queue(new InvoiceMail($invoice, $job->subject, $job->message ?? '', $pdfPath));
                EmailLog::create([
                    'invoice_id' => $invoice->id,
                    'to_email' => $job->to_email,
                    'subject' => $job->subject,
                    'body' => $job->message ?? '',
                    'status' => 'queued',
                    'sent_at' => now(),
                ]);
                $invoice->update([
                    'last_sent_at' => now(),
                    'status' => $invoice->status === 'draft' ? 'sent' : $invoice->status,
                ]);
                $job->update(['status' => 'sent', 'sent_at' => now()]);
                $sent++;
            } catch (\Throwable $e) {
                $job->update(['status' => 'failed', 'fail_reason' => substr($e->getMessage(), 0, 500)]);
                $failed++;
            }
        }

        $this->info("Scheduled sends: {$sent} sent, {$failed} failed, {$skipped} skipped.");
        return self::SUCCESS;
    }
}
