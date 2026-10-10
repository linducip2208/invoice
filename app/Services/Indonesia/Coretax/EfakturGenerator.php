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

        // Verified empirically (PHP 8.3): addChild() escapes < and > but
        // passes a raw & through, which corrupts the document (warning +
        // empty element). htmlspecialchars() is therefore REQUIRED here and
        // does NOT double-escape: '&' -> '&amp;' parses back to '&'.
        $tax->addChild('NomorFaktur', $td->efaktur_no ?: ('-/' . $invoice->invoice_no));
        $tax->addChild('TanggalFaktur', $invoice->issue_date->format('Y-m-d'));
        $tax->addChild('JenisFaktur', '01');

        $seller = $tax->addChild('Seller');
        $seller->addChild('NPWP', $this->cleanNpwp($workspace->tax_id ?? ''));
        $seller->addChild('Nama', htmlspecialchars((string) ($workspace->name ?? ''), ENT_XML1 | ENT_COMPAT, 'UTF-8'));
        $seller->addChild('Alamat', htmlspecialchars(trim(($workspace->address ?? '') . ' ' . ($workspace->city ?? '')), ENT_XML1 | ENT_COMPAT, 'UTF-8'));

        $buyer = $tax->addChild('Buyer');
        $buyer->addChild('NPWP', $this->cleanNpwp($client?->tax_id ?? '0000000000000000'));
        $buyer->addChild('Nama', htmlspecialchars((string) ($client?->company_name ?: $client?->name ?? ''), ENT_XML1 | ENT_COMPAT, 'UTF-8'));
        $buyer->addChild('Alamat', htmlspecialchars(trim(($client?->address ?? '') . ' ' . ($client?->city ?? '')), ENT_XML1 | ENT_COMPAT, 'UTF-8'));

        $items = $tax->addChild('Items');
        foreach ($invoice->items as $item) {
            $i = $items->addChild('Item');
            $i->addChild('Nama', htmlspecialchars((string) ($item->description ?? ''), ENT_XML1 | ENT_COMPAT, 'UTF-8'));
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
        // Sanitize: invoice_prefix is workspace-controlled, so allow only
        // alphanumerics and dash — this kills ../ traversal AND dotfiles.
        $safeNo = preg_replace('/[^A-Za-z0-9-]+/', '_', $invoice->invoice_no);
        $safeNo = trim($safeNo, '_') ?: 'invoice';
        $filename = 'efaktur/' . (int) $invoice->workspace_id . '/' . $safeNo . '.xml';
        Storage::disk('public')->put($filename, $xml);

        // Never regress an approved e-faktur back to draft on re-export.
        $detail = InvoiceTaxDetail::firstOrNew(['invoice_id' => $invoice->id]);
        $detail->workspace_id = $invoice->workspace_id;
        $detail->efaktur_xml_path = $filename;
        if (! $detail->exists) {
            $detail->efaktur_status = 'draft';
        }
        $detail->save();

        // generateXml() above loaded (and cached) a null taxDetail relation
        // on this instance — drop the cache so callers see the fresh row.
        $invoice->unsetRelation('taxDetail');

        return $filename;
    }

    private function cleanNpwp(string $npwp): string
    {
        $digits = preg_replace('/\D+/', '', $npwp);
        if (strlen($digits) === 15) $digits = '0' . $digits;
        return $digits ?: '0000000000000000';
    }
}
