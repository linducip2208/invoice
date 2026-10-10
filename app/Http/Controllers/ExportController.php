<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function index()
    {
        return view('exports.index');
    }

    public function invoices(): StreamedResponse
    {
        return $this->csv('invoices-' . now()->format('Ymd') . '.csv',
            ['Invoice No', 'Client', 'Status', 'Issue Date', 'Due Date', 'Subtotal', 'Tax', 'Discount', 'Total', 'Paid', 'Balance', 'Currency'],
            Invoice::with('client')->cursor()->map(fn($i) => [
                $i->invoice_no, $i->client?->name, $i->status,
                $i->issue_date?->format('Y-m-d'), $i->due_date?->format('Y-m-d'),
                $i->subtotal, $i->tax_amount, $i->discount_amount, $i->total,
                $i->amount_paid, $i->balance_due, $i->currency,
            ])
        );
    }

    public function payments(): StreamedResponse
    {
        return $this->csv('payments-' . now()->format('Ymd') . '.csv',
            ['Payment Date', 'Invoice No', 'Amount', 'Method', 'Reference', 'Status'],
            Payment::with('invoice')->cursor()->map(fn($p) => [
                $p->payment_date?->format('Y-m-d'), $p->invoice?->invoice_no, $p->amount,
                $p->payment_method, $p->reference_no, $p->gateway_status ?? 'manual',
            ])
        );
    }

    public function clients(): StreamedResponse
    {
        return $this->csv('clients-' . now()->format('Ymd') . '.csv',
            ['Name', 'Company', 'Email', 'Phone', 'City', 'Country', 'Active'],
            Client::cursor()->map(fn($c) => [
                $c->name, $c->company_name, $c->email, $c->phone,
                $c->city, $c->country, $c->is_active ? 'yes' : 'no',
            ])
        );
    }

    protected function csv(string $filename, array $headers, $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, array_map([$this, 'safeCell'], $headers));
            foreach ($rows as $row) {
                fputcsv($out, array_map([$this, 'safeCell'], (array) $row));
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * OWASP CSV formula-injection defense: a cell starting with = + - @
     * (or tab/CR) can execute as a spreadsheet formula. Prefixing with a
     * single quote forces plain-text treatment in Excel/LibreOffice.
     */
    public function safeCell(mixed $value): mixed
    {
        if (! is_string($value) || $value === '') {
            return $value;
        }
        return match (true) {
            in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true) => "'" . $value,
            default => $value,
        };
    }
}
