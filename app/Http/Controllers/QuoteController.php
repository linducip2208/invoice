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

        $result = app(\App\Services\QuoteConversionService::class)
            ->convert($quote, 'user:' . auth()->id());

        if (isset($result['error'])) {
            return back()->with('error', __('Quote cannot be converted in its current state.'));
        }

        return redirect()
            ->route('invoices.show', $result['invoice'])
            ->with('success', $result['duplicate'] ?? false
                ? __('Conversion already exists — showing the existing invoice.')
                : __('Quote converted to invoice.'));
    }

    public function send(Quote $quote, \App\Services\QuoteApprovalService $approvals)
    {
        abort_unless($quote->workspace_id === workspace()?->id, 403);
        $result = $approvals->send($quote);
        if (isset($result['error'])) {
            return back()->with('error', __('Only draft quotes can be sent.'));
        }
        return back()->with('success', __('Quote sent to client.'));
    }

    public function withdraw(Request $request, Quote $quote, \App\Services\QuoteApprovalService $approvals)
    {
        abort_unless($quote->workspace_id === workspace()?->id, 403);
        $validated = $request->validate(['reason' => ['nullable', 'string', 'max:500']]);
        $result = $approvals->withdraw($quote, 'user:' . auth()->id(), $validated['reason'] ?? null);
        if (isset($result['error'])) {
            return back()->with('error', __('Accepted quotes cannot be withdrawn.'));
        }
        return back()->with('success', __('Quote withdrawn. Outstanding approval links are now invalid.'));
    }

    public function reopen(Request $request, Quote $quote, \App\Services\QuoteApprovalService $approvals)
    {
        abort_unless($quote->workspace_id === workspace()?->id, 403);
        $validated = $request->validate(['reason' => ['nullable', 'string', 'max:500']]);
        $result = $approvals->reopen($quote, 'user:' . auth()->id(), $validated['reason'] ?? null);
        if (isset($result['error'])) {
            return back()->with('error', __('Only rejected, withdrawn or expired quotes can be reopened.'));
        }
        return back()->with('success', __('Quote reopened as revision :rev. History is preserved.', ['rev' => $result['revision']]));
    }

    public function convert(Quote $quote)
    {
        abort_unless($quote->workspace_id === workspace()?->id, 403);
        return $this->convertToInvoice($quote, app(InvoiceService::class));
    }

    public function rotateLink(Quote $quote, \App\Services\QuoteApprovalService $approvals)
    {
        abort_unless($quote->workspace_id === workspace()?->id, 403);
        $approvals->rotatePublicToken($quote);
        return back()->with('success', __('Public link rotated. Old links and pending codes are now invalid.'));
    }
}
