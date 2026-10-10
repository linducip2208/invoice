<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(
        protected PdfService $pdfService,
    ) {}

    public function generateInvoiceNo(Workspace $workspace): string
    {
        return DB::transaction(function () use ($workspace) {
            $workspace->refresh();
            $next = $workspace->next_invoice_no;
            $workspace->increment('next_invoice_no');

            $date = now()->format('Ymd');
            $padded = str_pad((string) $next, 4, '0', STR_PAD_LEFT);

            return $workspace->invoice_prefix . $date . '-' . $padded;
        });
    }

    public static function calculateTotals(array $items, float $taxRate, string $discountType, float $discountValue): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += (float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0);
        }

        $taxAmount = round($subtotal * ($taxRate / 100), 2);

        $discountAmount = 0;
        if ($discountType === 'percent') {
            $discountAmount = round($subtotal * ($discountValue / 100), 2);
        } elseif ($discountType === 'fixed') {
            $discountAmount = round($discountValue, 2);
        }

        $total = round($subtotal + $taxAmount - $discountAmount, 2);

        return [
            'subtotal'       => round($subtotal, 2),
            'tax_amount'     => $taxAmount,
            'discount_amount' => $discountAmount,
            'total'          => $total,
            'balance_due'    => $total,
        ];
    }

    public function generatePdf(Invoice $invoice): string
    {
        return $this->pdfService->generate($invoice);
    }
}
