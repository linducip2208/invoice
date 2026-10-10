<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ $batch->bank_name }} {{ $batch->account_no }} <span class="text-2xl">🏦</span></h2>
                <p class="text-sm text-purple-500 mt-1">{{ $batch->total_lines }} baris · {{ $batch->matched_count }} matched · status {{ $batch->status }}</p>
            </div>
            <a href="{{ route('bank-reconcile.index') }}" class="btn-unicorn-secondary text-sm">← {{ __('Semua Batch') }}</a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-5">
        @if(session('success'))<div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800">✓ {{ session('success') }}</div>@endif

        @if($batch->status !== 'completed' && $batch->matched_count > 0)
            <form method="POST" action="{{ route('bank-reconcile.finalize', $batch) }}"
                  onsubmit="return confirm('Buat {{ $batch->matched_count }} payment dari baris yang sudah matched? Tindakan ini tidak bisa dibatalkan.')"
                  class="card-unicorn-glow !p-5 flex items-center justify-between flex-wrap gap-3">
                @csrf
                <div>
                    <p class="font-display font-bold text-purple-900">✓ Finalisasi {{ $batch->matched_count }} match</p>
                    <p class="text-sm text-purple-500">Akan auto-create payment record + update balance invoice</p>
                </div>
                <button type="submit" class="btn-unicorn">🚀 {{ __('Finalisasi & Buat Payment') }}</button>
            </form>
        @endif

        <form method="POST" action="{{ route('bank-reconcile.match', $batch) }}" class="card-unicorn !p-0 overflow-hidden">
            @csrf
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-pink-50 via-purple-50 to-sky-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-[11px] font-bold text-purple-700 uppercase">Tgl</th>
                        <th class="px-3 py-2 text-right text-[11px] font-bold text-purple-700 uppercase">Nominal</th>
                        <th class="px-3 py-2 text-left text-[11px] font-bold text-purple-700 uppercase">Description</th>
                        <th class="px-3 py-2 text-left text-[11px] font-bold text-purple-700 uppercase w-72">Match Invoice</th>
                        <th class="px-3 py-2 text-center text-[11px] font-bold text-purple-700 uppercase">Confidence</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batch->lines as $line)
                        <tr class="border-t border-purple-100 {{ $line->matched_payment_id ? 'bg-emerald-50/40' : '' }}">
                            <td class="px-3 py-2 text-xs text-purple-500">{{ $line->trx_date->format('d/m/y') }}</td>
                            <td class="px-3 py-2 text-right font-bold text-purple-900">{{ number_format($line->amount, 0) }}</td>
                            <td class="px-3 py-2 text-xs text-purple-700 max-w-md truncate">{{ $line->description }}</td>
                            <td class="px-3 py-2">
                                @if($line->matched_payment_id)
                                    <span class="text-xs text-emerald-700 font-bold">✓ Payment created</span>
                                @else
                                    <select name="matches[{{ $line->id }}]" class="input-unicorn rounded-lg px-2 py-1 text-xs w-full">
                                        <option value="">— pilih —</option>
                                        @foreach($candidates as $inv)
                                            <option value="{{ $inv->id }}" {{ $line->matched_invoice_id === $inv->id ? 'selected' : '' }}>
                                                {{ $inv->invoice_no }} — {{ $inv->client?->name }} ({{ number_format($inv->balance_due, 0) }})
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                @php $confColor = match($line->match_confidence){'high'=>'mint','medium'=>'gold','low'=>'red','manual'=>'blue',default=>'gray'}; @endphp
                                <span class="badge-{{ $confColor }} text-[10px]">{{ $line->match_confidence }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($batch->status !== 'completed')
                <div class="p-4 border-t border-purple-100 flex justify-end">
                    <button type="submit" class="btn-unicorn">💾 {{ __('Update Match') }}</button>
                </div>
            @endif
        </form>
    </div>
</x-app-layout>
