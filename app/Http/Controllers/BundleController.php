<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\ProductBundle;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BundleController extends Controller
{
    public function index(): View
    {
        $bundles = ProductBundle::with('items.product')->latest()->paginate(15);
        return view('bundles.index', compact('bundles'));
    }

    public function create(): View
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('bundles.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $workspaceId = workspace()?->id;
        abort_unless($workspaceId, 403);

        // Produk harus milik workspace yang sama.
        $pids = collect($validated['items'])->pluck('product_id');
        if (Product::whereIn('id', $pids)->where('workspace_id', '!=', $workspaceId)->exists()) {
            return back()->with('error', __('Produk lintas workspace tidak diizinkan.'))->withInput();
        }

        $bundle = DB::transaction(function () use ($validated) {
            $b = ProductBundle::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'] ?? null,
                'is_active' => ! empty($validated['is_active']),
            ]);
            foreach ($validated['items'] as $item) {
                $b->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
            return $b;
        });

        return redirect()->route('bundles.show', $bundle)->with('success', __('Paket dibuat.'));
    }

    public function show(ProductBundle $bundle): View
    {
        abort_unless($bundle->workspace_id === workspace()?->id, 403);
        $bundle->load('items.product');
        $clients = Client::orderBy('name')->get();
        return view('bundles.show', compact('bundle', 'clients'));
    }

    public function edit(ProductBundle $bundle): View
    {
        abort_unless($bundle->workspace_id === workspace()?->id, 403);
        $bundle->load('items');
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('bundles.edit', compact('bundle', 'products'));
    }

    public function update(Request $request, ProductBundle $bundle): RedirectResponse
    {
        abort_unless($bundle->workspace_id === workspace()?->id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($bundle, $validated) {
            $bundle->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'] ?? null,
                'is_active' => ! empty($validated['is_active']),
            ]);
            $bundle->items()->delete();
            foreach ($validated['items'] as $item) {
                $bundle->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
        });

        return redirect()->route('bundles.show', $bundle)->with('success', __('Paket disimpan.'));
    }

    public function destroy(ProductBundle $bundle): RedirectResponse
    {
        abort_unless($bundle->workspace_id === workspace()?->id, 403);
        $bundle->items()->delete();
        $bundle->delete();
        return redirect()->route('bundles.index')->with('success', __('Paket dihapus.'));
    }

    /**
     * Buat draft invoice dari paket: 1 baris per komponen,
     * atau 1 baris harga paket bila harga paket diisi.
     */
    public function toInvoice(Request $request, ProductBundle $bundle, InvoiceService $service): RedirectResponse
    {
        abort_unless($bundle->workspace_id === workspace()?->id, 403);

        $validated = $request->validate(['client_id' => 'required|exists:clients,id']);
        $client = Client::findOrFail($validated['client_id']);
        abort_unless($client->workspace_id === $bundle->workspace_id, 403);

        $bundle->load('items.product');
        if ($bundle->items->isEmpty()) {
            return back()->with('error', __('Paket belum punya komponen.'));
        }

        $workspace = workspace();
        $lines = [];
        if ($bundle->price !== null) {
            $lines[] = [
                'description' => __('Paket: :name (:n item)', ['name' => $bundle->name, 'n' => $bundle->items->count()]),
                'quantity' => 1, 'unit_price' => (float) $bundle->price, 'tax_rate' => 0,
            ];
        } else {
            foreach ($bundle->items as $i => $item) {
                $p = $item->product;
                $lines[] = [
                    'description' => ($p?->name ?? __('Item')) . ' × ' . $item->quantity,
                    'product_id' => $item->product_id,
                    'quantity' => (float) $item->quantity,
                    'unit_price' => (float) ($p?->unit_price ?? 0),
                    'tax_rate' => (float) ($p?->tax_rate ?? 0),
                ];
            }
        }

        $totals = InvoiceService::calculateTotals($lines, 0, 'none', 0);

        $invoice = DB::transaction(function () use ($lines, $totals, $client, $workspace, $service, $bundle) {
            $inv = Invoice::create([
                'workspace_id' => $workspace->id,
                'client_id' => $client->id,
                'invoice_no' => $service->generateInvoiceNo($workspace),
                'status' => 'draft',
                'template' => $workspace->default_template ?? 'classic',
                'currency' => $workspace->currency ?? 'IDR',
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'tax_rate' => 0,
                'tax_amount' => $totals['tax_amount'],
                'discount_type' => 'none',
                'discount_value' => 0,
                'discount_amount' => $totals['discount_amount'],
                'subtotal' => $totals['subtotal'],
                'total' => $totals['total'],
                'amount_paid' => 0,
                'balance_due' => $totals['total'],
                'notes' => __('Dari paket: :name', ['name' => $bundle->name]),
            ]);

            foreach ($lines as $i => $line) {
                $lineSub = round($line['quantity'] * $line['unit_price'], 2);
                $lineTax = round($lineSub * ($line['tax_rate'] / 100), 2);
                $inv->items()->create([
                    'description' => $line['description'],
                    'product_id' => $line['product_id'] ?? null,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'tax_rate' => $line['tax_rate'],
                    'tax_amount' => $lineTax,
                    'subtotal' => $lineSub,
                    'total' => round($lineSub + $lineTax, 2),
                    'sort_order' => $i,
                ]);
            }

            \App\Services\InvoiceService::adjustStock($lines, -1);

            return $inv;
        });

        return redirect()->route('invoices.show', $invoice)
            ->with('success', __('Draft invoice :no dibuat dari paket.', ['no' => $invoice->invoice_no]));
    }
}
