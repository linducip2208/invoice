<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Tax Summary') }} <span class="text-2xl">🏛️</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Rekap PPN keluaran & PPh 23 untuk SPT') }}</p>
            </div>
            <a href="{{ route('reports.index') }}" class="btn-unicorn-secondary text-sm">← {{ __('Reports') }}</a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <form method="GET" class="card-unicorn !p-4 flex flex-wrap items-end gap-3">
            <div>
                <x-input-label :value="__('Bulan')" />
                <input type="month" name="period" value="{{ $period }}" class="input-unicorn rounded-xl px-4 py-2 mt-1">
            </div>
            <button type="submit" class="btn-unicorn">{{ __('Update') }}</button>
            <p class="text-xs text-purple-400 ml-auto">📅 {{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}</p>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="card-unicorn !p-5 bg-gradient-to-br from-sky-50 to-blue-50">
                <p class="text-xs uppercase tracking-wider text-sky-700">PPN Keluaran</p>
                <p class="font-display text-2xl font-bold text-sky-700 mt-1">{{ number_format($totalPpn, 0) }}</p>
                <p class="text-xs text-sky-600 mt-1">Total tax_amount untuk faktur output</p>
            </div>
            <div class="card-unicorn !p-5 bg-gradient-to-br from-amber-50 to-yellow-50">
                <p class="text-xs uppercase tracking-wider text-amber-700">PPh 23 Dipotong</p>
                <p class="font-display text-2xl font-bold text-amber-700 mt-1">{{ number_format($totalPph23, 0) }}</p>
                <p class="text-xs text-amber-600 mt-1">Klien wajib setor ke kas negara</p>
            </div>
            <div class="card-unicorn !p-5">
                <p class="text-xs uppercase tracking-wider text-purple-400">Status e-Faktur</p>
                <div class="mt-2 space-y-1 text-sm">
                    @foreach(['approved' => '✓ Approved', 'draft' => '📝 Draft', 'rejected' => '✗ Rejected', 'not_applicable' => '— N/A'] as $key => $label)
                        @if(($efakturStats[$key] ?? 0) > 0)
                            <div class="flex justify-between">
                                <span class="text-purple-600">{{ $label }}</span>
                                <span class="font-bold text-purple-900">{{ $efakturStats[$key] ?? 0 }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card-unicorn !p-0 overflow-hidden">
            <div class="p-6 border-b border-purple-100">
                <h3 class="font-display font-bold text-lg text-purple-900">{{ __('Detail Faktur') }} ({{ $taxDetails->count() }})</h3>
            </div>
            @if($taxDetails->isEmpty())
                <p class="p-8 text-center text-purple-400">Tidak ada faktur dengan tax details di periode ini.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-purple-50/60">
                            <tr>
                                <th class="px-3 py-2 text-left text-[11px] font-bold text-purple-700 uppercase">No Invoice</th>
                                <th class="px-3 py-2 text-left text-[11px] font-bold text-purple-700 uppercase">Klien</th>
                                <th class="px-3 py-2 text-left text-[11px] font-bold text-purple-700 uppercase">NPWP</th>
                                <th class="px-3 py-2 text-left text-[11px] font-bold text-purple-700 uppercase">Tgl</th>
                                <th class="px-3 py-2 text-right text-[11px] font-bold text-purple-700 uppercase">DPP</th>
                                <th class="px-3 py-2 text-right text-[11px] font-bold text-sky-700 uppercase">PPN</th>
                                <th class="px-3 py-2 text-right text-[11px] font-bold text-amber-700 uppercase">PPh 23</th>
                                <th class="px-3 py-2 text-center text-[11px] font-bold text-purple-700 uppercase">e-Faktur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($taxDetails as $td)
                                <tr class="border-t border-purple-100">
                                    <td class="px-3 py-2"><a href="{{ route('invoices.show', $td->invoice) }}" class="text-pink-600 font-semibold">{{ $td->invoice?->invoice_no }}</a></td>
                                    <td class="px-3 py-2 text-purple-700">{{ $td->invoice?->client?->name }}</td>
                                    <td class="px-3 py-2 text-purple-500 text-xs font-mono">{{ $td->invoice?->client?->tax_id ?: '—' }}</td>
                                    <td class="px-3 py-2 text-purple-500 text-xs">{{ $td->invoice?->issue_date?->format('d M Y') }}</td>
                                    <td class="px-3 py-2 text-right text-purple-900">{{ number_format($td->invoice?->subtotal ?? 0, 0) }}</td>
                                    <td class="px-3 py-2 text-right text-sky-700 font-semibold">{{ number_format($td->ppn_amount, 0) }}</td>
                                    <td class="px-3 py-2 text-right text-amber-700 font-semibold">{{ number_format($td->pph23_amount, 0) }}</td>
                                    <td class="px-3 py-2 text-center">
                                        @php $eb = match($td->efaktur_status){'approved'=>['mint','✓'],'draft'=>['gold','📝'],'rejected'=>['red','✗'],default=>['gray','—']}; @endphp
                                        <span class="badge-{{ $eb[0] }} text-[10px]">{{ $eb[1] }} {{ ucfirst($td->efaktur_status) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="border-t-2 border-purple-300 bg-purple-50/70 font-bold">
                                <td colspan="5" class="px-3 py-2 text-right">TOTAL</td>
                                <td class="px-3 py-2 text-right text-sky-700">{{ number_format($totalPpn, 0) }}</td>
                                <td class="px-3 py-2 text-right text-amber-700">{{ number_format($totalPph23, 0) }}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
