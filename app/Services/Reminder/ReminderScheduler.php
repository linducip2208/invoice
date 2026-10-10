<?php

namespace App\Services\Reminder;

use App\Models\Invoice;
use App\Models\InvoiceReminder;

class ReminderScheduler
{
    /**
     * Default reminder schedule (offset days from due_date).
     * Positive = after due date, negative = before, 0 = on due date.
     */
    public const DEFAULT_OFFSETS = [-3, 0, 3, 7];

    public const DEFAULT_TEMPLATES = [
        -3 => [
            'subject' => 'Pengingat: Invoice {{invoice_no}} jatuh tempo dalam 3 hari',
            'body'    => "Halo {{client_name}},\n\nInvoice {{invoice_no}} sebesar {{currency}} {{amount_due}} akan jatuh tempo pada {{due_date}}.\n\nLink invoice: {{pay_link}}\n\nTerima kasih.",
        ],
        0 => [
            'subject' => 'Invoice {{invoice_no}} jatuh tempo hari ini',
            'body'    => "Halo {{client_name}},\n\nInvoice {{invoice_no}} jatuh tempo hari ini ({{due_date}}). Total tagihan: {{currency}} {{amount_due}}.\n\nLink invoice: {{pay_link}}\n\nTerima kasih.",
        ],
        3 => [
            'subject' => 'Invoice {{invoice_no}} sudah lewat 3 hari',
            'body'    => "Halo {{client_name}},\n\nInvoice {{invoice_no}} sudah lewat jatuh tempo. Total: {{currency}} {{amount_due}}.\n\nMohon segera diselesaikan.\n\nLink: {{pay_link}}",
        ],
        7 => [
            'subject' => 'Pengingat ke-2: Invoice {{invoice_no}} sudah lewat 1 minggu',
            'body'    => "Halo {{client_name}},\n\nInvoice {{invoice_no}} sudah lewat jatuh tempo lebih dari 1 minggu. Total: {{currency}} {{amount_due}}.\n\nMohon konfirmasi pembayaran atau hubungi kami untuk diskusi.\n\nLink: {{pay_link}}",
        ],
    ];

    /**
     * Schedule default reminders for an invoice.
     * Skips client.no_reminders=true. Cancels any existing pending reminders first.
     */
    public function scheduleFor(Invoice $invoice): int
    {
        if (! $invoice->client || $invoice->client->no_reminders) {
            return 0;
        }

        if (in_array($invoice->status, ['paid', 'cancelled'])) {
            return 0;
        }

        // Cancel existing pending reminders before re-scheduling
        InvoiceReminder::where('invoice_id', $invoice->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        $count = 0;
        foreach (self::DEFAULT_OFFSETS as $offset) {
            $scheduledAt = $invoice->due_date->copy()->addDays($offset)->setTime(9, 0);

            // Skip if scheduled in the past (e.g., creating invoice with past due date)
            if ($scheduledAt->isPast()) continue;

            $tpl = self::DEFAULT_TEMPLATES[$offset] ?? self::DEFAULT_TEMPLATES[0];

            InvoiceReminder::create([
                'workspace_id'     => $invoice->workspace_id,
                'invoice_id'       => $invoice->id,
                'schedule_offset'  => $offset,
                'scheduled_at'     => $scheduledAt,
                'template_subject' => $tpl['subject'],
                'template_body'    => $tpl['body'],
                'status'           => 'pending',
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Cancel all pending reminders (e.g., when invoice is paid).
     */
    public function cancelFor(Invoice $invoice): int
    {
        return InvoiceReminder::where('invoice_id', $invoice->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);
    }

    /**
     * Render template with invoice variables.
     */
    public static function render(string $template, Invoice $invoice): string
    {
        $client = $invoice->client;
        $vars = [
            '{{client_name}}'    => $client?->name ?? '',
            '{{client_company}}' => $client?->company_name ?? '',
            '{{invoice_no}}'     => $invoice->invoice_no,
            '{{amount_due}}'     => number_format($invoice->balance_due ?? $invoice->total, 0),
            '{{currency}}'       => $invoice->currency,
            '{{due_date}}'       => $invoice->due_date->format('d M Y'),
            '{{issue_date}}'     => $invoice->issue_date->format('d M Y'),
            '{{pay_link}}'       => route('public.invoice.show', $invoice->public_token ?? ''),
            '{{workspace_name}}' => $invoice->workspace?->name ?? '',
        ];
        return strtr($template, $vars);
    }
}
