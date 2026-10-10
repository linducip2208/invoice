<x-tabler.layout :title="$cn->credit_no" :pretitle="__('Credit Note untuk') . ' ' . ($cn->invoice?->invoice_no ?? '')">
    <x-slot name="actions">
        <a href="{{ route('credit-notes.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Semua Credit Note') }}
        </a>
        @if($cn->status === 'draft')
            <form method="POST" action="{{ route('credit-notes.issue', $cn) }}" class="d-inline">@csrf
                <button type="submit" class="btn btn-sm btn-primary"><x-ti name="send" /> {{ __('Issue') }}</button>
            </form>
        @endif
        @if($cn->status === 'issued')
            <form method="POST" action="{{ route('credit-notes.apply', $cn) }}" class="d-inline" onsubmit="return confirm('Apply credit ini ke invoice? Sisa tagihan akan berkurang.')">@csrf
                <button type="submit" class="btn btn-sm btn-primary"><x-ti name="check" /> {{ __('Apply ke Invoice') }}</button>
            </form>
        @endif
        @if(in_array($cn->status, ['issued','applied']))
            <form method="POST" action="{{ route('credit-notes.cancel', $cn) }}" class="d-inline" onsubmit="return confirm('Batalkan credit note ini?')">@csrf
                <button type="submit" class="btn btn-sm"><x-ti name="x" /> {{ __('Cancel') }}</button>
            </form>
        @endif
        @if($cn->status === 'draft')
            <x-tabler.delete-button :action="route('credit-notes.destroy', $cn)"
                :confirm="__('Hapus draft permanen?')" :label="__('Hapus Draft')" :title="__('Delete')" />
        @endif
    </x-slot>

    {{-- Status & Actions --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row align-items-center g-3">
                <div class="col">
                    <p class="subheader mb-1">{{ __('Status') }}</p>
                    <x-tabler.status-badge :status="$cn->status" />
                </div>
            </div>
        </div>
    </div>

    {{-- Detail card --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <p class="subheader mb-1">{{ __('Klien') }}</p>
                    <p class="h3 mb-0">{{ $cn->invoice?->client?->name }}</p>
                    @if($cn->invoice?->client?->company_name)
                        <p class="text-muted small mb-0">{{ $cn->invoice->client->company_name }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <p class="subheader mb-1">{{ __('Tanggal Issued') }}</p>
                    <p class="h3 mb-0">{{ $cn->issued_at->format('d M Y') }}</p>
                </div>
                <div class="col-md-6">
                    <p class="subheader mb-1">{{ __('Jumlah Kredit') }}</p>
                    <p class="h2 text-danger mb-0">-{{ number_format($cn->amount, 0) }} {{ $cn->currency }}</p>
                </div>
                <div class="col-md-6">
                    <p class="subheader mb-1">{{ __('Dibuat oleh') }}</p>
                    <p class="fw-semibold mb-0">{{ $cn->creator?->name ?? '—' }}</p>
                    <p class="small text-muted mt-0 mb-0">{{ $cn->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>

            <hr>
            <p class="subheader mb-1">{{ __('Alasan') }}</p>
            <p class="mb-0">{{ $cn->reason }}</p>

            @if($cn->notes)
                <hr>
                <p class="subheader mb-1">{{ __('Catatan Internal') }}</p>
                <p class="text-muted small mb-0" style="white-space: pre-line;">{{ $cn->notes }}</p>
            @endif
        </div>
    </div>

    {{-- Invoice context --}}
    <div class="card">
        <div class="card-body">
            <p class="subheader mb-3">{{ __('Konteks Invoice') }}</p>
            <div class="row align-items-center g-3">
                <div class="col">
                    <a href="{{ route('invoices.show', $cn->invoice) }}" class="h3">{{ $cn->invoice?->invoice_no }}</a>
                    <p class="text-muted small mb-0">Total: {{ $cn->invoice?->currency }} {{ number_format($cn->invoice?->total, 0) }}</p>
                </div>
                <div class="col-auto text-end">
                    <p class="small text-muted mb-1">{{ __('Sisa Tagihan saat ini') }}</p>
                    <p class="h2 mb-0">{{ $cn->invoice?->currency }} {{ number_format($cn->invoice?->balance_due, 0) }}</p>
                </div>
            </div>
        </div>
    </div>
</x-tabler.layout>
