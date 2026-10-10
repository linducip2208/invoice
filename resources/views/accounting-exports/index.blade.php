<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Accounting Exports') }} <span class="text-2xl">📊</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ Export ke Jurnal/QuickBooks/Xero/CSV generic</p>
            </div>
            <a href="{{ route('accounting-exports.create') }}" class="btn-unicorn">+ {{ __('Generate Export') }}</a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto">
        @if(session('success'))<div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800 mb-4">✓ {{ session('success') }}</div>@endif

        <div class="card-unicorn !p-0 overflow-hidden">
            @if($exports->isEmpty())
                <div class="p-12 text-center">
                    <p class="text-5xl mb-3">📊</p>
                    <p class="font-display font-bold text-purple-700">Belum ada export</p>
                </div>
            @else
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-pink-50 via-purple-50 to-sky-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">Format</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">Periode</th>
                            <th class="px-4 py-3 text-right text-[11px] font-bold text-purple-700 uppercase">Records</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">Generated</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exports as $e)
                            <tr class="border-t border-purple-100">
                                <td class="px-4 py-3"><span class="badge-purple text-xs">{{ ucfirst($e->format) }}</span></td>
                                <td class="px-4 py-3 text-sm text-purple-700">{{ $e->period_start->format('d M Y') }} - {{ $e->period_end->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-right font-bold text-purple-900">{{ $e->record_count }}</td>
                                <td class="px-4 py-3 text-xs text-purple-500">{{ $e->created_at->diffForHumans() }} · {{ $e->generator?->name }}</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('accounting-exports.download', $e) }}" class="text-pink-600 text-xs font-semibold">📥 Download</a>
                                    <form method="POST" action="{{ route('accounting-exports.destroy', $e) }}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')
                                        <button class="text-rose-500 text-xs">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4">{{ $exports->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
