<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Bank Reconcile') }} <span class="text-2xl">🏦</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ Upload mutasi bank → auto-match invoice</p>
            </div>
            <a href="{{ route('bank-reconcile.create') }}" class="btn-unicorn">+ {{ __('Upload CSV') }}</a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto">
        @if(session('success'))<div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800 mb-4">✓ {{ session('success') }}</div>@endif
        @if(session('error'))<div class="rounded-2xl bg-rose-50 border-2 border-rose-200 p-4 text-rose-800 mb-4">⚠️ {{ session('error') }}</div>@endif

        <div class="card-unicorn !p-0 overflow-hidden">
            @if($batches->isEmpty())
                <div class="p-12 text-center">
                    <p class="text-5xl mb-3">🏦</p>
                    <p class="font-display font-bold text-lg text-purple-700">Belum ada batch reconcile</p>
                </div>
            @else
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-pink-50 via-purple-50 to-sky-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">Bank</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">Akun</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">Tgl</th>
                            <th class="px-4 py-3 text-right text-[11px] font-bold text-purple-700 uppercase">Lines</th>
                            <th class="px-4 py-3 text-right text-[11px] font-bold text-emerald-700 uppercase">Matched</th>
                            <th class="px-4 py-3 text-center text-[11px] font-bold text-purple-700 uppercase">Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batches as $b)
                            <tr class="border-t border-purple-100">
                                <td class="px-4 py-3 font-bold text-purple-900">{{ $b->bank_name }}</td>
                                <td class="px-4 py-3 text-sm font-mono text-purple-700">{{ $b->account_no }}</td>
                                <td class="px-4 py-3 text-xs text-purple-500">{{ $b->created_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 text-right text-purple-900 font-bold">{{ $b->total_lines }}</td>
                                <td class="px-4 py-3 text-right text-emerald-700 font-bold">{{ $b->matched_count }} / {{ $b->total_lines }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="badge-{{ $b->status === 'completed' ? 'mint' : ($b->status === 'review' ? 'gold' : 'gray') }} text-xs">{{ ucfirst($b->status) }}</span>
                                </td>
                                <td class="px-4 py-3"><a href="{{ route('bank-reconcile.show', $b) }}" class="text-pink-600 text-xs font-semibold">Detail →</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4">{{ $batches->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
