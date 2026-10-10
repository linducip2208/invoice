<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quote;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        $workspaceId = workspace()?->id;
        abort_unless($workspaceId, 403);

        $validated = $request->validate([
            'client_id'   => ['required', Rule::exists('clients', 'id')->where('workspace_id', $workspaceId)],
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

        // Retry on unique(workspace_id, quote_no) collision: two requests
        // serialized on the workspace lock still generate different numbers,
        // but a stale read (e.g. HTTP retry) must not 500.
        $quote = null;
        foreach (range(1, 3) as $attempt) {
            try {
                $quote = Quote::create([
                    'client_id'   => $validated['client_id'],
                    'quote_no'    => $service->generateQuoteNo(workspace()),
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
                break;
            } catch (\Illuminate\Database\QueryException $e) {
                if ($attempt === 3 || ! str_contains($e->getMessage(), 'quote_no')) {
                    throw $e;
                }
            }
        }

        return redirect()->route('quotes.index')->with('success', 'Quote ' . $quote->quote_no . ' created.');
    }

    public function show(Quote $quote)
    {
        abort_unless($quote->workspace_id === workspace()?->id, 403);
        $quote->load('client');
        return view('quotes.show', compact('quote'));
    }

    public function destroy(Quote $quote)
    {
        abort_unless($quote->workspace_id === workspace()?->id, 403);
        $quote->delete();
        return redirect()->route('quotes.index')->with('success', 'Quote deleted.');
    }

    public function convertToInvoice(Quote $quote, InvoiceService $service)
    {
        abort_unless($quote->workspace_id === workspace()?->id, 403);

        // Lock the quote row inside the transaction so a double-click / retry
        // cannot convert the same quote twice (TOCTOU).
        $invoice = DB::transaction(function () use ($quote, $service) {
            $locked = Quote::where('id', $quote->id)->lockForUpdate()->firstOrFail();

            if ($locked->status === 'converted' || $locked->converted_invoice_id) {
                abort(409, __('Quote already converted.'));
            }

            $quoteWorkspace = $locked->workspace;
            abort_unless($quoteWorkspace, 403);

            $totals = InvoiceService::calculateTotals($locked->items, $locked->tax_rate, 'none', 0);

            $invoice = Invoice::create([
                'workspace_id' => $locked->workspace_id,
                'client_id'    => $locked->client_id,
                'invoice_no'   => $service->generateInvoiceNo($quoteWorkspace),
                'status'       => 'draft',
                'template'     => $quoteWorkspace->default_template ?? 'classic',
                'issue_date'   => now()->toDateString(),
                'due_date'     => now()->addDays(30)->toDateString(),
                'subtotal'     => $totals['subtotal'],
                'tax_rate'     => $locked->tax_rate,
                'tax_amount'   => $totals['tax_amount'],
                'discount_type' => 'none',
                'discount_value' => 0,
                'discount_amount' => 0,
                'total'        => $totals['total'],
                'amount_paid'  => 0,
                'balance_due'  => $totals['total'],
                'currency'     => $locked->currency,
                'notes'        => $locked->notes,
                'terms'        => $locked->terms,
            ]);

            foreach ($locked->items as $i => $item) {
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

            $locked->update(['status' => 'converted', 'converted_invoice_id' => $invoice->id]);
            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Quote converted to invoice.');
    }
}
