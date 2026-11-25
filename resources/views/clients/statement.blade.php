<x-tabler.layout :title="__('Mutasi') . ' — ' . $client->name" :pretitle="__('Customers')" :breadcrumbs="[['label' => __('Customers'), 'url' => route('clients.index')], ['label' => $client->name, 'url' => route('clients.show', $client)], ['label' => __('Mutasi')]]">
    <x-slot name="actions">
        <button class="btn btn-primary" onclick="window.print()"><x-ti name="printer" /> {{ __('Print / PDF') }}</button>
    </x-slot>

    <div class="row row-decks row-cards mb-3">
        <x-tabler.stat :label="__('Total Ditagih')" :value="number_format($totals['invoiced'], 0)" icon="file-invoice" />
        <x-tabler.stat :label="__('Sudah Dibayar')" :value="number_format($totals['paid'], 0)" icon="check" />
        <x-tabler.stat :label="__('Sisa Tagihan')" :value="number_format($totals['balance'], 0)" icon="hourglass" />
    </div>

    <div class="card mb-3">
        <div class="card-header"><h3 class="card-title">{{ __('Invoice') }} ({{ $invoices->count() }})</h3></div>
        <div class="table-responsive"><table class="table table-vcenter card-table">
            <thead><tr><th>{{ __('No') }}</th><th>{{ __('Tanggal') }}</th><th>{{ __('Jatuh tempo') }}</th><th class="text-end">{{ __('Total') }}</th><th class="text-end">{{ __('Dibayar') }}</th><th class="text-end">{{ __('Sisa') }}</th><th class="text-center">{{ __('Status') }}</th></tr></thead>
            <tbody>
                @forelse($invoices as $inv)
                    <tr>
                        <td class="fw-semibold">{{ $inv->invoice_no }}</td>
                        <td class="text-muted">{{ $inv->issue_date?->format('d M Y') }}</td>
                        <td class="text-muted">{{ $inv->due_date?->format('d M Y') }}</td>
                        <td class="text-end">{{ number_format($inv->total, 0) }}</td>
                        <td class="text-end text-success">{{ number_format($inv->amount_paid, 0) }}</td>
                        <td class="text-end fw-bold">{{ number_format($inv->balance_due, 0) }}</td>
                        <td class="text-center"><x-tabler.status-badge :status="$inv->status" /></td>
                    </tr>
                @empty
                    <tr><td colspan="7"><x-tabler.empty icon="file-invoice" :title="__('Belum ada invoice.')" /></td></tr>
                @endforelse
            </tbody>
        </table></div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">{{ __('Pembayaran') }} ({{ $payments->count() }})</h3></div>
        <div class="table-responsive"><table class="table table-vcenter card-table">
            <thead><tr><th>{{ __('Tanggal') }}</th><th>{{ __('Invoice') }}</th><th>{{ __('Metode') }}</th><th>{{ __('Referensi') }}</th><th class="text-end">{{ __('Jumlah') }}</th></tr></thead>
            <tbody>
                @forelse($payments as $p)
                    <tr>
                        <td class="text-muted">{{ $p->payment_date?->format('d M Y') }}</td>
                        <td>{{ $p->invoice?->invoice_no ?? '—' }}</td>
                        <td class="text-muted">{{ $p->payment_gateway_id ? __('Gateway') : $p->payment_method }}</td>
                        <td class="text-muted small">{{ $p->reference_no }}</td>
                        <td class="text-end fw-bold text-success">{{ number_format($p->amount, 0) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-tabler.empty icon="credit-card" :title="__('Belum ada pembayaran.')" /></td></tr>
                @endforelse
            </tbody>
        </table></div>
    </div>
</x-tabler.layout>
