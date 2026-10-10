<?php

namespace App\Services\Accounting;

use App\Models\AccountingExport;
use App\Models\Invoice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ExportRunner
{
    public function run(string $format, Carbon $start, Carbon $end): AccountingExport
    {
        $invoices = Invoice::with('client', 'payments', 'taxDetail')
            ->whereBetween('issue_date', [$start, $end])
            ->orderBy('issue_date')
            ->get();

        $content = match ($format) {
            'jurnal'        => $this->jurnalCsv($invoices),
            'mekari'        => $this->jurnalCsv($invoices), // alias
            'quickbooks'    => $this->quickbooksCsv($invoices),
            'xero'          => $this->xeroCsv($invoices),
            default         => $this->genericCsv($invoices),
        };

        $filename = sprintf('accounting-exports/%s-%s-%s.csv',
            $format,
            $start->format('Y-m-d'),
            $end->format('Y-m-d')
        );
        Storage::disk('public')->put($filename, $content);

        return AccountingExport::create([
            'format'       => $format,
            'period_start' => $start->toDateString(),
            'period_end'   => $end->toDateString(),
            'file_path'    => $filename,
            'record_count' => $invoices->count(),
            'generated_by' => auth()->id(),
        ]);
    }

    private function jurnalCsv($invoices): string
    {
        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['Tanggal', 'No Invoice', 'Customer', 'Description', 'Subtotal', 'PPN', 'Total', 'Status', 'Currency']);
        foreach ($invoices as $inv) {
            fputcsv($output, [
                $inv->issue_date->format('Y-m-d'),
                $inv->invoice_no,
                $inv->client?->name,
                'Invoice ' . $inv->invoice_no,
                $inv->subtotal,
                $inv->tax_amount,
                $inv->total,
                $inv->status,
                $inv->currency,
            ]);
        }
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);
        return $content;
    }

    private function quickbooksCsv($invoices): string
    {
        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['InvoiceNo', 'Customer', 'InvoiceDate', 'DueDate', 'Item', 'Description', 'Qty', 'Rate', 'Amount']);
        foreach ($invoices as $inv) {
            foreach ($inv->items ?? [] as $item) {
                fputcsv($output, [
                    $inv->invoice_no,
                    $inv->client?->name,
                    $inv->issue_date->format('m/d/Y'),
                    $inv->due_date->format('m/d/Y'),
                    'Service',
                    $item->description,
                    $item->quantity,
                    $item->unit_price,
                    $item->total,
                ]);
            }
        }
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);
        return $content;
    }

    private function xeroCsv($invoices): string
    {
        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['*ContactName', '*InvoiceNumber', '*InvoiceDate', '*DueDate', 'Description', '*Quantity', '*UnitAmount', '*AccountCode', 'TaxType', 'Currency']);
        foreach ($invoices as $inv) {
            foreach ($inv->items ?? [] as $item) {
                fputcsv($output, [
                    $inv->client?->name,
                    $inv->invoice_no,
                    $inv->issue_date->format('Y-m-d'),
                    $inv->due_date->format('Y-m-d'),
                    $item->description,
                    $item->quantity,
                    $item->unit_price,
                    '200',
                    $item->tax_rate > 0 ? 'OUTPUT' : 'NONE',
                    $inv->currency,
                ]);
            }
        }
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);
        return $content;
    }

    private function genericCsv($invoices): string
    {
        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['date', 'invoice_no', 'client', 'subtotal', 'tax', 'total', 'paid', 'balance', 'currency', 'status']);
        foreach ($invoices as $inv) {
            fputcsv($output, [
                $inv->issue_date->format('Y-m-d'),
                $inv->invoice_no,
                $inv->client?->name,
                $inv->subtotal,
                $inv->tax_amount,
                $inv->total,
                $inv->amount_paid,
                $inv->balance_due,
                $inv->currency,
                $inv->status,
            ]);
        }
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);
        return $content;
    }
}
