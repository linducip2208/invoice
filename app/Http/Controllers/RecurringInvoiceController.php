<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\InvoiceTemplate;
use App\Models\RecurringInvoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecurringInvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = RecurringInvoice::query()->with('client');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $recurringInvoices = $query->latest()->paginate(15)->withQueryString();

        return view('recurring.index', compact('recurringInvoices'));
    }

    public function create(): View
    {
        $clients = Client::query()->where('is_active', true)->orderBy('name')->get();
        $templates = InvoiceTemplate::query()
            ->where(function ($q) {
                $q->whereNull('workspace_id')
                    ->orWhere('workspace_id', workspace()->id);
            })
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        return view('recurring.create', compact('clients', 'templates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id'       => ['required', 'exists:clients,id'],
            'frequency'       => ['required', 'in:daily,weekly,monthly,yearly'],
            'interval_value'  => ['required', 'integer', 'min:1', 'max:365'],
            'next_run_date'   => ['required', 'date'],
            'end_date'        => ['nullable', 'date', 'after:next_run_date'],
            'max_occurrences' => ['nullable', 'integer', 'min:1'],
            'template'        => ['nullable', 'string', 'max:50'],
            'primary_color'   => ['nullable', 'string', 'max:7'],
            'accent_color'    => ['nullable', 'string', 'max:7'],
            'items'           => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity'    => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price'  => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_rate'        => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_type'   => ['nullable', 'in:fixed,percent,none'],
            'discount_value'  => ['nullable', 'numeric', 'min:0'],
            'notes'           => ['nullable', 'string'],
            'terms'           => ['nullable', 'string'],
            'currency'        => ['nullable', 'string', 'max:10'],
        ]);

        $items = $validated['items'];
        $taxRate = (float) ($validated['tax_rate'] ?? 0);
        $discountType = $validated['discount_type'] ?? 'none';
        $discountValue = (float) ($validated['discount_value'] ?? 0);

        $totals = \App\Services\InvoiceService::calculateTotals($items, $taxRate, $discountType, $discountValue);

        $invoiceData = [
            'items'         => $items,
            'tax_rate'      => $taxRate,
            'discount_type'  => $discountType,
            'discount_value' => $discountValue,
            'notes'          => $validated['notes'] ?? null,
            'terms'          => $validated['terms'] ?? null,
            'currency'       => $validated['currency'] ?? workspace()?->currency ?? 'IDR',
            'subtotal'       => $totals['subtotal'],
            'tax_amount'     => $totals['tax_amount'],
            'discount_amount' => $totals['discount_amount'],
            'total'          => $totals['total'],
        ];

        RecurringInvoice::create([
            'client_id'       => $validated['client_id'],
            'frequency'       => $validated['frequency'],
            'interval_value'  => $validated['interval_value'],
            'next_run_date'   => $validated['next_run_date'],
            'end_date'        => $validated['end_date'] ?? null,
            'max_occurrences' => $validated['max_occurrences'] ?? null,
            'template'        => $validated['template'] ?? workspace()?->default_template ?? 'classic',
            'primary_color'   => $validated['primary_color'] ?? null,
            'accent_color'    => $validated['accent_color'] ?? null,
            'status'          => 'active',
            'invoice_data'    => $invoiceData,
        ]);

        return redirect()->route('recurring.index')->with('success', 'Recurring invoice created successfully.');
    }

    public function edit(RecurringInvoice $recurringInvoice): View
    {
        $clients = Client::query()->where('is_active', true)->orderBy('name')->get();
        $templates = InvoiceTemplate::query()
            ->where(function ($q) {
                $q->whereNull('workspace_id')
                    ->orWhere('workspace_id', workspace()->id);
            })
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        return view('recurring.edit', compact('recurringInvoice', 'clients', 'templates'));
    }

    public function update(Request $request, RecurringInvoice $recurringInvoice): RedirectResponse
    {
        $validated = $request->validate([
            'client_id'       => ['required', 'exists:clients,id'],
            'frequency'       => ['required', 'in:daily,weekly,monthly,yearly'],
            'interval_value'  => ['required', 'integer', 'min:1', 'max:365'],
            'next_run_date'   => ['required', 'date'],
            'end_date'        => ['nullable', 'date', 'after:next_run_date'],
            'max_occurrences' => ['nullable', 'integer', 'min:1'],
            'template'        => ['nullable', 'string', 'max:50'],
            'primary_color'   => ['nullable', 'string', 'max:7'],
            'accent_color'    => ['nullable', 'string', 'max:7'],
            'items'           => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity'    => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price'  => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_rate'        => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_type'   => ['nullable', 'in:fixed,percent,none'],
            'discount_value'  => ['nullable', 'numeric', 'min:0'],
            'notes'           => ['nullable', 'string'],
            'terms'           => ['nullable', 'string'],
            'currency'        => ['nullable', 'string', 'max:10'],
        ]);

        $items = $validated['items'];
        $taxRate = (float) ($validated['tax_rate'] ?? 0);
        $discountType = $validated['discount_type'] ?? 'none';
        $discountValue = (float) ($validated['discount_value'] ?? 0);

        $totals = \App\Services\InvoiceService::calculateTotals($items, $taxRate, $discountType, $discountValue);

        $invoiceData = [
            'items'         => $items,
            'tax_rate'      => $taxRate,
            'discount_type'  => $discountType,
            'discount_value' => $discountValue,
            'notes'          => $validated['notes'] ?? null,
            'terms'          => $validated['terms'] ?? null,
            'currency'       => $validated['currency'] ?? workspace()?->currency ?? 'IDR',
            'subtotal'       => $totals['subtotal'],
            'tax_amount'     => $totals['tax_amount'],
            'discount_amount' => $totals['discount_amount'],
            'total'          => $totals['total'],
        ];

        $recurringInvoice->update([
            'client_id'       => $validated['client_id'],
            'frequency'       => $validated['frequency'],
            'interval_value'  => $validated['interval_value'],
            'next_run_date'   => $validated['next_run_date'],
            'end_date'        => $validated['end_date'] ?? null,
            'max_occurrences' => $validated['max_occurrences'] ?? null,
            'template'        => $validated['template'] ?? workspace()?->default_template ?? 'classic',
            'primary_color'   => $validated['primary_color'] ?? null,
            'accent_color'    => $validated['accent_color'] ?? null,
            'invoice_data'    => $invoiceData,
        ]);

        return redirect()->route('recurring.index')->with('success', 'Recurring invoice updated successfully.');
    }

    public function destroy(RecurringInvoice $recurringInvoice): RedirectResponse
    {
        $recurringInvoice->delete();

        return redirect()->route('recurring.index')->with('success', 'Recurring invoice deleted successfully.');
    }

    public function toggle(RecurringInvoice $recurringInvoice): RedirectResponse
    {
        $newStatus = $recurringInvoice->status === 'active' ? 'paused' : 'active';
        $recurringInvoice->update(['status' => $newStatus]);

        $action = $newStatus === 'paused' ? 'paused' : 'resumed';

        return redirect()->route('recurring.index')->with('success', "Recurring invoice {$action} successfully.");
    }
}
