<?php

namespace App\Services\Indonesia\Coretax;

use App\Models\Invoice;
use App\Models\InvoiceTaxDetail;
use Illuminate\Support\Facades\Storage;

class EfakturGenerator
{
    /**
     * Generate Coretax-compatible XML for an invoice.
     * Schema based on DJP Coretax 2025 spec (simplified).
     */
    public function generateXml(Invoice $invoice): string
    {
        $invoice->loadMissing('client', 'workspace', 'items', 'taxDetail');
        $td = $invoice->taxDetail ?? new InvoiceTaxDetail();

        $workspace = $invoice->workspace;
        $client = $invoice->client;

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><TaxInvoiceBundle/>');
        $tax = $xml->addChild('TaxInvoice');

        $tax->addChild('NomorFaktur', $td->efaktur_no ?: ('-/' . $invoice->invoice_no));
        $tax->addChild('TanggalFaktur', $invoice->issue_date->format('Y-m-d'));
        $tax->addChild('JenisFaktur', '01');

        $seller = $tax->addChild('Seller');
        $seller->addChild('NPWP', $this->cleanNpwp($workspace->tax_id ?? ''));
        $seller->addChild('Nama', htmlspecialchars($workspace->name ?? ''));
        $seller->addChild('Alamat', htmlspecialchars(trim(($workspace->address ?? '') . ' ' . ($workspace->city ?? ''))));

        $buyer = $tax->addChild('Buyer');
        $buyer->addChild('NPWP', $this->cleanNpwp($client?->tax_id ?? '0000000000000000'));
        $buyer->addChild('Nama', htmlspecialchars($client?->company_name ?: $client?->name ?? ''));
        $buyer->addChild('Alamat', htmlspecialchars(trim(($client?->address ?? '') . ' ' . ($client?->city ?? ''))));

        $items = $tax->addChild('Items');
        foreach ($invoice->items as $item) {
            $i = $items->addChild('Item');
            $i->addChild('Nama', htmlspecialchars($item->description ?? ''));
            $i->addChild('Quantity', number_format($item->quantity, 2, '.', ''));
            $i->addChild('UnitPrice', number_format($item->unit_price, 2, '.', ''));
            $i->addChild('Subtotal', number_format($item->subtotal ?? ($item->quantity * $item->unit_price), 2, '.', ''));
        }

        $totals = $tax->addChild('Totals');
        $totals->addChild('DPP', number_format($invoice->subtotal, 2, '.', ''));
        $totals->addChild('PPNRate', number_format($td->ppn_rate ?? 11, 2, '.', ''));
        $totals->addChild('PPNAmount', number_format($td->ppn_amount ?? $invoice->tax_amount, 2, '.', ''));
        $totals->addChild('PPh23Rate', number_format($td->pph23_rate ?? 0, 2, '.', ''));
        $totals->addChild('PPh23Amount', number_format($td->pph23_amount ?? 0, 2, '.', ''));
        $totals->addChild('Total', number_format($invoice->total, 2, '.', ''));
        $totals->addChild('Currency', $invoice->currency);

        return $xml->asXML();
    }

    public function saveXml(Invoice $invoice): string
    {
        $xml = $this->generateXml($invoice);
        $filename = 'efaktur/' . $invoice->workspace_id . '/' . $invoice->invoice_no . '.xml';
        Storage::disk('public')->put($filename, $xml);

        InvoiceTaxDetail::updateOrCreate(
            ['invoice_id' => $invoice->id],
            ['workspace_id' => $invoice->workspace_id, 'efaktur_xml_path' => $filename, 'efaktur_status' => 'draft']
        );

        return $filename;
    }

    private function cleanNpwp(string $npwp): string
    {
        $digits = preg_replace('/\D+/', '', $npwp);
        if (strlen($digits) === 15) $digits = '0' . $digits;
        return $digits ?: '0000000000000000';
    }
}
