<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\Indonesia\Coretax\EfakturGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EFakturController extends Controller
{
    public function generate(Invoice $invoice, EfakturGenerator $generator)
    {
        $path = $generator->saveXml($invoice);
        return back()->with('success', '✓ XML e-Faktur generated. Download di tombol bawah.');
    }

    public function downloadXml(Invoice $invoice)
    {
        $detail = $invoice->taxDetail;
        if (! $detail || ! $detail->efaktur_xml_path) {
            return back()->with('error', 'Belum ada XML. Generate dulu.');
        }
        $absolutePath = Storage::disk('public')->path($detail->efaktur_xml_path);
        return response()->download($absolutePath, $invoice->invoice_no . '-efaktur.xml');
    }
}
