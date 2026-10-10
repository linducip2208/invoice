<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\Ai\Features\InvoiceTranslator;
use Illuminate\Http\Request;

class InvoiceTranslateController extends Controller
{
    public function translate(Request $request, Invoice $invoice, InvoiceTranslator $translator)
    {
        $request->validate([
            'locale' => 'required|in:en,zh,ja,ko,es,fr',
        ]);

        try {
            $translated = $translator->translate($invoice, $request->locale);
            return back()->with('success', "✓ Invoice diterjemahkan ke " . InvoiceTranslator::LOCALES[$request->locale]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal terjemah: ' . $e->getMessage());
        }
    }
}
