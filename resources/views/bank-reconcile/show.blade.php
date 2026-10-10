<x-tabler.layout :title="$batch->bank_name . ' ' . $batch->account_no" :pretitle="__('Bank Reconcile')">
    <x-slot name="actions">
        <a href="{{ route('bank-reconcile.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Semua Batch') }}
        </a>
    </x-slot>

    <p class="text-muted small mb-3">{{ $batch->total_lines }} {{ __('baris') }} · {{ $batch->matched_count }} {{ __('matched') }} · {{ __('status') }} {{ $batch->status }}</p>

    @if($batch->status !== 'completed' && $batch->matched_count > 0)
        <form method="POST" action="{{ route('bank-reconcile.finalize', $batch) }}" onsubmit="return confirm({{ Js::from(__('Buat') . ' ' . $batch->matched_count . ' ' . __('payment dari baris yang sudah matched? Tindakan ini tidak bisa dibatalkan.')) }});" class="card mb-3">
            @csrf
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <strong><x-ti name="check" /> {{ __('Finalisasi') }} {{ $batch->matched_count }} {{ __('match') }}</strong>
                        <div class="text-muted small">{{ __('Akan auto-create payment record + update balance invoice') }}</div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary"><x-ti name="check" /> {{ __('Finalisasi & Buat Payment') }}</button>
                    </div>
                </div>
            </div>
        </form>
    @endif

    <form method="POST" action="{{ route('bank-reconcile.match', $batch) }}" class="card">
        @csrf
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Tgl') }}</th>
                        <th class="text-end">{{ __('Nominal') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Match Invoice') }}</th>
                        <th class="text-center">{{ __('Confidence') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batch->lines as $line)
                        <tr class="{{ $line->matched_payment_id ? 'table-success' : '' }}">
                            <td class="text-muted small">{{ $line->trx_date->format('d/m/y') }}</td>
                            <td class="text-end fw-bold">{{ number_format($line->amount, 0) }}</td>
                            <td class="text-muted small text-truncate" style="max-width: 24rem;">{{ $line->description }}</td>
                            <td>
                                @if($line->matched_payment_id)
                                    <span class="text-success small fw-bold"><x-ti name="check" /> {{ __('Payment created') }}</span>
                                @else
                                    <select name="matches[{{ $line->id }}]" class="form-select form-select-sm">
                                        <option value="">— {{ __('pilih') }} —</option>
                                        @foreach($candidates as $inv)
                                            <option value="{{ $inv->id }}" {{ $line->matched_invoice_id === $inv->id ? 'selected' : '' }}>{{ $inv->invoice_no }} — {{ $inv->client?->name }} ({{ number_format($inv->balance_due, 0) }})</option>
                                        @endforeach
                                    </select>
                                @endif
                            </td>
                            <td class="text-center">
                                @php $confColor = match ($line->match_confidence) {'high' => 'success', 'medium' => 'warning', 'low' => 'danger', 'manual' => 'info', default => 'secondary'}; @endphp
                                <span class="badge bg-{{ $confColor }}">{{ $line->match_confidence }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($batch->status !== 'completed')
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><x-ti name="check" /> {{ __('Update Match') }}</button>
            </div>
        @endif
    </form>
</x-tabler.layout>
