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

    /**
     * Generate the next invoice number for a workspace.
     *
     * The workspace row is locked (SELECT ... FOR UPDATE) inside the
     * transaction so concurrent requests serialize on the counter instead
     * of reading the same value and colliding on the
     * unique(workspace_id, invoice_no) constraint.
     */
    public function generateInvoiceNo(Workspace $workspace): string
    {
        return DB::transaction(function () use ($workspace) {
            $locked = Workspace::where('id', $workspace->id)->lockForUpdate()->firstOrFail();
            $next = $locked->next_invoice_no;
            $locked->increment('next_invoice_no');

            $date = now()->format('Ymd');
            $padded = str_pad((string) $next, 4, '0', STR_PAD_LEFT);

            return $locked->invoice_prefix . $date . '-' . $padded;
        });
    }

    /**
     * Generate the next quotation number for a workspace.
     * Same locking strategy as invoices; scoped per workspace (the old
     * global Quote::count()+1 collided across workspaces).
     */
    public function generateQuoteNo(Workspace $workspace): string
    {
        return DB::transaction(function () use ($workspace) {
            Workspace::where('id', $workspace->id)->lockForUpdate()->firstOrFail();

            $count = \App\Models\Quote::withoutGlobalScope('workspace')
                ->where('workspace_id', $workspace->id)
                ->count() + 1;

            $date = now()->format('Ymd');
            $padded = str_pad((string) $count, 4, '0', STR_PAD_LEFT);

            return 'Q-' . $date . '-' . $padded;
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

    /**
     * Sesuaikan stok produk yang dilacak. $direction -1 = kurangi (invoice
     * baru), +1 = kembalikan (hapus/ubah). Tanpa produk track_stock = no-op.
     * Boleh minus (backorder); badge "menipis" memberi peringatan.
     */
    public static function adjustStock(array $items, int $direction): void
    {
        $byProduct = [];
        foreach ($items as $item) {
            $pid = $item['product_id'] ?? null;
            if (! $pid) continue;
            $byProduct[$pid] = ($byProduct[$pid] ?? 0) + (float) ($item['quantity'] ?? 0);
        }
        foreach ($byProduct as $pid => $qty) {
            if ($qty <= 0) continue;
            $query = \App\Models\Product::where('id', $pid)->where('track_stock', true);
            if ($direction < 0) {
                $query->decrement('stock_qty', $qty);
            } else {
                $query->increment('stock_qty', $qty);
            }
        }
    }

    public function generatePdf(Invoice $invoice): string
    {
        return $this->pdfService->generate($invoice);
    }
}
