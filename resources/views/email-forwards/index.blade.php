<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Email Inbox Forward') }} <span class="text-2xl">📬</span></h2>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <div class="card-unicorn-glow !p-6 sparkles-bg">
            <p class="font-display font-bold text-purple-900 mb-2">📨 Forward Address Kamu</p>
            <p class="text-sm text-purple-600 mb-3">Forward email vendor / supplier ke alamat ini → AI auto-extract → buat expense draft.</p>
            <div class="flex items-center gap-3">
                <code class="flex-1 bg-white/80 px-4 py-3 rounded-xl font-mono text-sm text-purple-900 select-all border border-purple-200">{{ $inboundAddress }}</code>
                <button onclick="navigator.clipboard.writeText('{{ $inboundAddress }}'); this.textContent='✓ Copied'" class="btn-unicorn text-xs">📋 Copy</button>
            </div>
            <p class="text-xs text-purple-400 mt-2">⚠️ Setup inbound email di Postmark/Mailgun/SES dengan webhook URL: <code>POST /webhooks/email-inbox</code></p>
        </div>

        <div class="card-unicorn !p-0 overflow-hidden">
            <div class="p-5 border-b border-purple-100">
                <h3 class="font-display font-bold text-purple-900">{{ __('Riwayat Forward') }} ({{ $forwards->count() }})</h3>
            </div>
            @if($forwards->isEmpty())
                <p class="p-8 text-center text-purple-400">Belum ada email yang diforward.</p>
            @else
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-pink-50 via-purple-50 to-sky-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">From</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">Subject</th>
                            <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">Tgl</th>
                            <th class="px-4 py-3 text-center text-[11px] font-bold text-purple-700 uppercase">Status</th>
                            <th class="px-4 py-3 text-right text-[11px] font-bold text-purple-700 uppercase">Expense</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($forwards as $f)
                            <tr class="border-t border-purple-100">
                                <td class="px-4 py-3 text-sm text-purple-700">{{ $f->from_email }}</td>
                                <td class="px-4 py-3 text-sm text-purple-900 max-w-xs truncate">{{ $f->subject }}</td>
                                <td class="px-4 py-3 text-xs text-purple-500">{{ $f->forwarded_at->diffForHumans() }}</td>
                                <td class="px-4 py-3 text-center">
                                    @php $bg = match($f->status){'converted'=>'mint','manual_review'=>'gold','failed'=>'red','duplicate'=>'gray',default=>'blue'}; @endphp
                                    <span class="badge-{{ $bg }} text-[10px]">{{ ucfirst(str_replace('_',' ', $f->status)) }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($f->expense_id)
                                        <a href="{{ route('expenses.index') }}" class="text-pink-600 text-xs font-semibold">Lihat →</a>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4">{{ $forwards->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
