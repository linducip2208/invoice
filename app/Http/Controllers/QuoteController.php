<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quote;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Quote::with('client');
        if ($request->filled('status')) $query->where('status', $request->status);

        $quotes = $query->latest()->paginate(20)->withQueryString();
        return view('quotes.index', compact('quotes'));
    }

    public function create()
    {
        $clients  = Client::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('quotes.create', compact('clients', 'products'));
    }

    public function store(Request $request, InvoiceService $service)
    {
        $validated = $request->validate([
            'client_id'   => 'required|exists:clients,id',
            'issue_date'  => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'currency'    => 'required|string|size:3',
            'tax_rate'    => 'nullable|numeric|min:0|max:100',
            'notes'       => 'nullable|string',
            'terms'       => 'nullable|string',
            'items'       => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.tax_rate'    => 'nullable|numeric|min:0|max:100',
        ]);

        $taxRate = (float) ($validated['tax_rate'] ?? 0);
        $totals = InvoiceService::calculateTotals($validated['items'], $taxRate, 'none', 0);

        $quote = Quote::create([
            'client_id'   => $validated['client_id'],
            'quote_no'    => 'Q-' . now()->format('Ymd') . '-' . str_pad((string) (Quote::count() + 1), 4, '0', STR_PAD_LEFT),
            'status'      => 'draft',
            'issue_date'  => $validated['issue_date'],
            'expiry_date' => $validated['expiry_date'] ?? null,
            'currency'    => $validated['currency'],
            'tax_rate'    => $taxRate,
            'subtotal'    => $totals['subtotal'],
            'tax_amount'  => $totals['tax_amount'],
            'total'       => $totals['total'],
            'items'       => $validated['items'],
            'notes'       => $validated['notes'] ?? null,
            'terms'       => $validated['terms'] ?? null,
        ]);

        return redirect()->route('quotes.index')->with('success', 'Quote ' . $quote->quote_no . ' created.');
    }

    public function show(Quote $quote)
    {
        $quote->load('client');
        return view('quotes.show', compact('quote'));
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();
        return redirect()->route('quotes.index')->with('success', 'Quote deleted.');
    }

    public function convertToInvoice(Quote $quote, InvoiceService $service)
    {
        if ($quote->status === 'converted') return back()->with('error', 'Already converted.');

        $invoice = DB::transaction(function () use ($quote, $service) {
            $totals = InvoiceService::calculateTotals($quote->items, $quote->tax_rate, 'none', 0);

            $invoice = Invoice::create([
                'workspace_id' => $quote->workspace_id,
                'client_id'    => $quote->client_id,
                'invoice_no'   => $service->generateInvoiceNo(workspace()),
                'status'       => 'draft',
                'template'     => workspace()?->default_template ?? 'classic',
                'issue_date'   => now()->toDateString(),
                'due_date'     => now()->addDays(30)->toDateString(),
                'subtotal'     => $totals['subtotal'],
                'tax_rate'     => $quote->tax_rate,
                'tax_amount'   => $totals['tax_amount'],
                'discount_type' => 'none',
                'discount_value' => 0,
                'discount_amount' => 0,
                'total'        => $totals['total'],
                'amount_paid'  => 0,
                'balance_due'  => $totals['total'],
                'currency'     => $quote->currency,
                'notes'        => $quote->notes,
                'terms'        => $quote->terms,
            ]);

            foreach ($quote->items as $i => $item) {
                $qty = (float) $item['quantity'];
                $price = (float) $item['unit_price'];
                $itemTax = (float) ($item['tax_rate'] ?? 0);
                $sub = round($qty * $price, 2);
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity'    => $qty,
                    'unit_price'  => $price,
                    'tax_rate'    => $itemTax,
                    'tax_amount'  => round($sub * $itemTax / 100, 2),
                    'subtotal'    => $sub,
                    'total'       => round($sub + ($sub * $itemTax / 100), 2),
                    'sort_order'  => $i,
                ]);
            }

            $quote->update(['status' => 'converted', 'converted_invoice_id' => $invoice->id]);
            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Quote converted to invoice.');
    }
}
