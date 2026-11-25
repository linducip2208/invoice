<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService,
    ) {}

    /**
     * Validation rules shared by store/update. Ownership of the referenced
     * client/products is scoped to the current workspace so IDs from other
     * workspaces cannot be attached via request tampering.
     */
    protected function invoiceRules(): array
    {
        $workspaceId = workspace()?->id;

        return [
            'client_id'       => ['required', Rule::exists('clients', 'id')->where('workspace_id', $workspaceId)],
            'issue_date'      => 'required|date',
            'due_date'        => 'required|date|after_or_equal:issue_date',
            'template'         => 'required|string|in:classic,modern,minimal,bold,elegant,bilingual',
            'invoice_layout_id' => 'nullable|exists:invoice_layouts,id',
            'primary_color'   => 'nullable|string|max:7',
            'accent_color'    => 'nullable|string|max:7',
            'currency'        => 'required|string|size:3',
            'tax_rate'        => 'nullable|numeric|min:0|max:100',
            'discount_type'   => 'required|in:none,fixed,percent',
            'discount_value'  => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
            'terms'           => 'nullable|string',
            'items'           => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.tax_rate'    => 'nullable|numeric|min:0|max:100',
            'items.*.product_id'  => ['nullable', Rule::exists('products', 'id')->where('workspace_id', $workspaceId)],
        ];
    }

    public function index(Request $request)
    {
        $query = Invoice::query()->with('client');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('client', fn ($c) => $c->where('name', 'like', "%{$search}%")
                                                     ->orWhere('company_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        $allowedSorts = ['issue_date', 'due_date', 'total', 'balance_due', 'invoice_no', 'created_at'];
        $sort = in_array($request->get('sort'), $allowedSorts, true) ? $request->get('sort') : 'created_at';
        $order = $request->get('order') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort, $order);

        $invoices = $query->paginate(20)->withQueryString();
        $clients = Client::orderBy('name')->get(['id', 'name', 'company_name']);

        return view('invoices.index', compact('invoices', 'clients', 'sort', 'order'));
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate(['ids' => 'required|array|min:1', 'ids.*' => 'integer']);
        $deleted = 0;
        foreach (Invoice::whereIn('id', $validated['ids'])->get() as $inv) {
            if ($inv->workspace_id !== workspace()?->id) continue;
            if ($inv->status !== 'draft') continue;
            $inv->items()->delete();
            $inv->delete();
            $deleted++;
        }

        return back()->with('success', __(':n draft invoice(s) deleted.', ['n' => $deleted]));
    }

    /**
     * Unduh banyak PDF sekaligus sebagai ZIP (maks 10/invoice per aksi).
     */
    public function bulkPdf(Request $request, InvoiceService $invoiceService)
    {
        $validated = $request->validate(['ids' => 'required|array|min:1|max:10', 'ids.*' => 'integer']);
        $invoices = Invoice::whereIn('id', $validated['ids'])->get();
        if ($invoices->isEmpty()) {
            return back()->with('error', __('No invoices selected.'));
        }

        $zipPath = tempnam(sys_get_temp_dir(), 'inv_') . '.zip';
        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE);
        foreach ($invoices as $inv) {
            if ($inv->workspace_id !== workspace()?->id) continue;
            $pdfPath = $invoiceService->generatePdf($inv);
            $zip->addFile(storage_path('app/public/' . $pdfPath), $inv->invoice_no . '.pdf');
        }
        $zip->close();

        return response()->download($zipPath, 'invoices-' . now()->format('YmdHis') . '.zip')->deleteFileAfterSend(true);
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $layouts = \App\Models\InvoiceLayout::orderBy('name')->get();

        return view('invoices.create', compact('clients', 'products', 'layouts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->invoiceRules());

        $workspace = workspace();
        abort_unless($workspace, 403);
        $invoiceNo = $this->invoiceService->generateInvoiceNo($workspace);

        $taxRate = (float) ($validated['tax_rate'] ?? 0);
        $discountType = $validated['discount_type'];
        $discountValue = (float) ($validated['discount_value'] ?? 0);

        $totals = InvoiceService::calculateTotals($validated['items'], $taxRate, $discountType, $discountValue);

        $invoice = DB::transaction(function () use ($validated, $invoiceNo, $taxRate, $discountType, $discountValue, $totals, $workspace) {
            $invoice = Invoice::create([
                'workspace_id'   => $workspace->id,
                'client_id'      => $validated['client_id'],
                'invoice_no'     => $invoiceNo,
                'status'         => 'draft',
                'template'         => $validated['template'],
                'invoice_layout_id' => $validated['invoice_layout_id'] ?? null,
                'primary_color'    => $validated['primary_color'] ?? null,
                'accent_color'     => $validated['accent_color'] ?? null,
                'currency'         => $validated['currency'],
                'issue_date'       => $validated['issue_date'],
                'due_date'         => $validated['due_date'],
                'tax_rate'         => $taxRate,
                'tax_amount'       => $totals['tax_amount'],
                'discount_type'    => $discountType,
                'discount_value'   => $discountValue,
                'discount_amount'  => $totals['discount_amount'],
                'subtotal'         => $totals['subtotal'],
                'total'            => $totals['total'],
                'amount_paid'      => 0,
                'balance_due'      => $totals['total'],
                'notes'            => $validated['notes'] ?? null,
                'terms'            => $validated['terms'] ?? null,
            ]);

            foreach ($validated['items'] as $i => $item) {
                $itemTaxRate = (float) ($item['tax_rate'] ?? 0);
                $lineSubtotal = round((float) $item['quantity'] * (float) $item['unit_price'], 2);
                $lineTax = round($lineSubtotal * ($itemTaxRate / 100), 2);

                InvoiceItem::create([
                    'invoice_id'    => $invoice->id,
                    'product_id'    => $item['product_id'] ?? null,
                    'description'   => $item['description'],
                    'quantity'      => $item['quantity'],
                    'unit_price'    => $item['unit_price'],
                    'tax_rate'      => $itemTaxRate,
                    'tax_amount'    => $lineTax,
                    'subtotal'      => $lineSubtotal,
                    'total'         => round($lineSubtotal + $lineTax, 2),
                    'sort_order'    => $i,
                ]);
            }

            return $invoice;
        });

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', __('Invoice :no created successfully.', ['no' => $invoiceNo]));
    }

    public function show(Invoice $invoice)
    {
        abort_unless($invoice->workspace_id === workspace()?->id, 403);
        $invoice->load(['items.product', 'client', 'payments', 'emailLogs', 'publicViews', 'attachments']);
        $activity = \App\Models\AuditLog::with('user:id,name')
            ->where('subject_type', Invoice::class)
            ->where('subject_id', $invoice->id)
            ->where('workspace_id', $invoice->workspace_id)
            ->latest()
            ->take(10)
            ->get();

        return view('invoices.show', compact('invoice', 'activity'));
    }

    public function edit(Invoice $invoice)
    {
        abort_unless($invoice->workspace_id === workspace()?->id, 403);
        if ($invoice->status !== 'draft') {
            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', __('Only draft invoices can be edited.'));
        }

        $invoice->load('items');
        $clients = Client::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $layouts = \App\Models\InvoiceLayout::orderBy('name')->get();

        return view('invoices.edit', compact('invoice', 'clients', 'products', 'layouts'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        abort_unless($invoice->workspace_id === workspace()?->id, 403);
        if ($invoice->status !== 'draft') {
            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', __('Only draft invoices can be edited.'));
        }

        $validated = $request->validate($this->invoiceRules());

        $taxRate = (float) ($validated['tax_rate'] ?? 0);
        $discountType = $validated['discount_type'];
        $discountValue = (float) ($validated['discount_value'] ?? 0);

        $totals = InvoiceService::calculateTotals($validated['items'], $taxRate, $discountType, $discountValue);

        DB::transaction(function () use ($invoice, $validated, $taxRate, $discountType, $discountValue, $totals) {
            $invoice->update([
                'client_id'        => $validated['client_id'],
                'template'         => $validated['template'],
                'invoice_layout_id' => $validated['invoice_layout_id'] ?? null,
                'primary_color'    => $validated['primary_color'] ?? null,
                'accent_color'     => $validated['accent_color'] ?? null,
                'currency'       => $validated['currency'],
                'issue_date'     => $validated['issue_date'],
                'due_date'       => $validated['due_date'],
                'tax_rate'       => $taxRate,
                'tax_amount'     => $totals['tax_amount'],
                'discount_type'  => $discountType,
                'discount_value' => $discountValue,
                'discount_amount'=> $totals['discount_amount'],
                'subtotal'       => $totals['subtotal'],
                'total'          => $totals['total'],
                'balance_due'    => $totals['total'] - (float) $invoice->amount_paid,
                'notes'          => $validated['notes'] ?? null,
                'terms'          => $validated['terms'] ?? null,
            ]);

            $invoice->items()->delete();

            foreach ($validated['items'] as $i => $item) {
                $itemTaxRate = (float) ($item['tax_rate'] ?? 0);
                $lineSubtotal = round((float) $item['quantity'] * (float) $item['unit_price'], 2);
                $lineTax = round($lineSubtotal * ($itemTaxRate / 100), 2);

                InvoiceItem::create([
                    'invoice_id'    => $invoice->id,
                    'product_id'    => $item['product_id'] ?? null,
                    'description'   => $item['description'],
                    'quantity'      => $item['quantity'],
                    'unit_price'    => $item['unit_price'],
                    'tax_rate'      => $itemTaxRate,
                    'tax_amount'    => $lineTax,
                    'subtotal'      => $lineSubtotal,
                    'total'         => round($lineSubtotal + $lineTax, 2),
                    'sort_order'    => $i,
                ]);
            }
        });

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', __('Invoice :no updated successfully.', ['no' => $invoice->invoice_no]));
    }

    public function destroy(Invoice $invoice)
    {
        abort_unless($invoice->workspace_id === workspace()?->id, 403);
        if ($invoice->status !== 'draft') {
            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', __('Only draft invoices can be deleted.'));
        }

        $invoiceNo = $invoice->invoice_no;
        DB::transaction(fn () => $invoice->items()->delete());
        $invoice->delete();

        return redirect()
            ->route('invoices.index')
            ->with('success', __('Invoice :no deleted successfully.', ['no' => $invoiceNo]));
    }

    public function duplicate(Invoice $invoice)
    {
        $workspace = workspace();
        $newInvoiceNo = $this->invoiceService->generateInvoiceNo($workspace);

        $newInvoice = DB::transaction(function () use ($invoice, $newInvoiceNo, $workspace) {
            $clone = $invoice->replicate()->fill([
                'workspace_id'  => $workspace->id,
                'client_id'     => $invoice->client_id,
                'invoice_no'    => $newInvoiceNo,
                'status'        => 'draft',
                'amount_paid'   => 0,
                'balance_due'   => $invoice->total,
                'last_sent_at'  => null,
                'public_token'  => null,
            ]);
            $clone->save();

            foreach ($invoice->items as $item) {
                $clone->items()->create($item->replicate()->toArray());
            }

            return $clone;
        });

        return redirect()
            ->route('invoices.edit', $newInvoice)
            ->with('success', __('Invoice duplicated as :no.', ['no' => $newInvoiceNo]));
    }

    public function markAsSent(Invoice $invoice)
    {
        if (! in_array($invoice->status, ['draft', 'cancelled'])) {
            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', __('This invoice cannot be marked as sent.'));
        }

        $invoice->update([
            'status'       => 'sent',
            'last_sent_at' => now(),
        ]);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', __('Invoice marked as sent.'));
    }

    /**
     * Tandai terkirim massal (maks 20/aksi): hanya draft/cancelled,
     * tanpa kirim email — untuk yang sudah dikirim manual via WA/cetak.
     */
    public function bulkMarkSent(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1|max:20',
            'ids.*' => 'integer',
        ]);

        $n = 0;
        foreach (Invoice::whereIn('id', $validated['ids'])->get() as $inv) {
            if ($inv->workspace_id !== workspace()?->id) continue;
            if (! in_array($inv->status, ['draft', 'cancelled'])) continue;
            $inv->update(['status' => 'sent', 'last_sent_at' => $inv->last_sent_at ?? now()]);
            $n++;
        }

        return back()->with('success', __(':n invoice ditandai terkirim (tanpa email).', ['n' => $n]));
    }

    public function markAsCancelled(Invoice $invoice)
    {
        if (in_array($invoice->status, ['paid'])) {
            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', __('Paid invoices cannot be cancelled.'));
        }

        $invoice->update(['status' => 'cancelled']);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', __('Invoice cancelled.'));
    }

    public function rotateLink(Invoice $invoice)
    {
        abort_unless($invoice->workspace_id === workspace()?->id, 403);
        $invoice->update(['public_token' => (string) \Illuminate\Support\Str::uuid()]);

        return back()->with('success', __('Public invoice link rotated. The old link no longer works.'));
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,paid,partial,overdue,cancelled',
        ]);

        $newStatus = $validated['status'];
        $currentStatus = $invoice->status;

        if ($currentStatus === 'paid' && $newStatus !== 'paid') {
            return back()->with('error', __('Cannot change status of a paid invoice.'));
        }

        if ($currentStatus === 'cancelled' && $newStatus !== 'cancelled') {
            return back()->with('error', __('Please use "Mark as Sent" to reopen a cancelled invoice.'));
        }

        $data = ['status' => $newStatus];

        if ($newStatus === 'sent' && empty($invoice->last_sent_at)) {
            $data['last_sent_at'] = now();
        }

        $invoice->update($data);

        return back()->with('success', __('Status updated to :status.', ['status' => $newStatus]));
    }

    public function pdf(Invoice $invoice, InvoiceService $invoiceService)
    {
        $path = $invoiceService->generatePdf($invoice);

        return response()->download(
            storage_path('app/public/' . $path),
            $invoice->invoice_no . '.pdf'
        );
    }
}
