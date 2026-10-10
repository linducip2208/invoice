<?php

namespace App\Console\Commands;

use App\Models\RecurringInvoice;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\InvoiceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateRecurringInvoices extends Command
{
    protected $signature = 'invoices:generate-recurring';

    protected $description = 'Generate invoices from active recurring invoice schedules';

    public function __construct(
        protected InvoiceService $invoiceService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $recurringInvoices = RecurringInvoice::where('status', 'active')
            ->where('next_run_date', '<=', now()->toDateString())
            ->with('client')
            ->get();

        if ($recurringInvoices->isEmpty()) {
            $this->info('No recurring invoices to generate.');

            return self::SUCCESS;
        }

        $generated = 0;

        foreach ($recurringInvoices as $recurring) {
            try {
                DB::transaction(function () use ($recurring, &$generated) {
                    $invData = $recurring->invoice_data;
                    $items = $invData['items'] ?? [];

                    $workspace = $recurring->workspace;
                    $invoiceNo = $this->invoiceService->generateInvoiceNo($workspace);

                    $taxRate = (float) ($invData['tax_rate'] ?? 0);
                    $discountType = $invData['discount_type'] ?? 'none';
                    $discountValue = (float) ($invData['discount_value'] ?? 0);

                    $totals = InvoiceService::calculateTotals($items, $taxRate, $discountType, $discountValue);

                    $invoice = Invoice::create([
                        'workspace_id'          => $recurring->workspace_id,
                        'client_id'             => $recurring->client_id,
                        'invoice_no'            => $invoiceNo,
                        'recurring_invoice_id'  => $recurring->id,
                        'template'              => $recurring->template ?? $workspace->default_template ?? 'classic',
                        'primary_color'         => $recurring->primary_color,
                        'accent_color'          => $recurring->accent_color,
                        'issue_date'            => now()->toDateString(),
                        'due_date'              => now()->addDays(30)->toDateString(),
                        'subtotal'              => $totals['subtotal'],
                        'tax_rate'              => $taxRate,
                        'tax_amount'            => $totals['tax_amount'],
                        'discount_type'         => $discountType,
                        'discount_value'        => $discountValue,
                        'discount_amount'       => $totals['discount_amount'],
                        'total'                 => $totals['total'],
                        'amount_paid'           => 0,
                        'balance_due'           => $totals['balance_due'],
                        'currency'              => $invData['currency'] ?? $workspace->currency ?? 'IDR',
                        'notes'                 => $invData['notes'] ?? null,
                        'terms'                 => $invData['terms'] ?? null,
                        'status'                => 'draft',
                    ]);

                    foreach ($items as $i => $item) {
                        $qty = (float) ($item['quantity'] ?? 1);
                        $price = (float) ($item['unit_price'] ?? 0);
                        $itemTaxRate = (float) ($item['tax_rate'] ?? 0);
                        $lineSubtotal = round($qty * $price, 2);
                        $lineTax = round($lineSubtotal * ($itemTaxRate / 100), 2);
                        $lineTotal = round($lineSubtotal + $lineTax, 2);

                        InvoiceItem::create([
                            'invoice_id'    => $invoice->id,
                            'description'   => $item['description'],
                            'quantity'      => $qty,
                            'unit_price'    => $price,
                            'tax_rate'      => $itemTaxRate,
                            'tax_amount'    => $lineTax,
                            'discount_type'  => 'none',
                            'discount_value' => 0,
                            'subtotal'      => $lineSubtotal,
                            'total'         => $lineTotal,
                            'sort_order'    => $i + 1,
                        ]);
                    }

                    $nextRunDate = $this->calculateNextDate(
                        $recurring->next_run_date,
                        $recurring->frequency,
                        $recurring->interval_value
                    );

                    $newOccurrence = $recurring->current_occurrence + 1;
                    $newStatus = 'active';

                    if ($recurring->max_occurrences && $newOccurrence >= $recurring->max_occurrences) {
                        $newStatus = 'completed';
                    }

                    if ($recurring->end_date && $nextRunDate->gt($recurring->end_date)) {
                        $newStatus = 'completed';
                    }

                    $recurring->update([
                        'next_run_date'       => $nextRunDate->toDateString(),
                        'current_occurrence'  => $newOccurrence,
                        'last_invoice_id'     => $invoice->id,
                        'status'              => $newStatus,
                    ]);
                });

                $generated++;
                $this->info("Generated invoice for recurring #{$recurring->id} - client: {$recurring->client->name}");
            } catch (\Throwable $e) {
                $this->error("Failed to generate invoice for recurring #{$recurring->id}: {$e->getMessage()}");
            }
        }

        $this->info("Done. Generated {$generated} invoice(s) from recurring schedules.");

        return self::SUCCESS;
    }

    protected function calculateNextDate(\Carbon\Carbon $current, string $frequency, int $interval): \Carbon\Carbon
    {
        return match ($frequency) {
            'daily'   => $current->copy()->addDays($interval),
            'weekly'  => $current->copy()->addWeeks($interval),
            'monthly' => $current->copy()->addMonths($interval),
            'yearly'  => $current->copy()->addYears($interval),
            default   => $current->copy()->addMonth(),
        };
    }
}
