<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoices — Customer Portal</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans min-h-screen" style="background: linear-gradient(135deg, #FFF8FE 0%, #FFE4F1 50%, #EDE9FE 100%);">
<nav class="bg-white/80 backdrop-blur border-b border-purple-100 sticky top-0 z-40">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ route('portal.dashboard') }}" class="flex items-center gap-2">
            <span class="text-xl">🪟</span>
            <span class="font-display font-bold text-rainbow">Customer Portal</span>
        </a>
        <div class="flex items-center gap-4 text-sm">
            <a href="{{ route('portal.dashboard') }}" class="text-purple-700 hover:text-pink-500 font-semibold">Dashboard</a>
            <a href="{{ route('portal.invoices') }}" class="text-pink-500 font-bold">Invoices</a>
            <a href="{{ route('portal.quotes') }}" class="text-purple-700 hover:text-pink-500 font-semibold">Quotes</a>
            <form method="POST" action="{{ route('portal.logout') }}" class="inline">@csrf
                <button type="submit" class="text-rose-500 hover:underline text-sm">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="font-display text-2xl font-bold text-purple-900 mb-6">📄 Invoice Saya</h1>

    <div class="card-unicorn !p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-pink-50 via-purple-50 to-sky-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">Tgl</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">Jatuh Tempo</th>
                        <th class="px-4 py-3 text-right text-[11px] font-bold text-purple-700 uppercase">Total</th>
                        <th class="px-4 py-3 text-right text-[11px] font-bold text-purple-700 uppercase">Sisa</th>
                        <th class="px-4 py-3 text-center text-[11px] font-bold text-purple-700 uppercase">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $inv)
                        <tr class="border-t border-purple-100 hover:bg-purple-50/40">
                            <td class="px-4 py-3 font-bold text-purple-900">{{ $inv->invoice_no }}</td>
                            <td class="px-4 py-3 text-sm text-purple-500">{{ $inv->issue_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-sm text-purple-500">{{ $inv->due_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right font-bold text-purple-900">{{ number_format($inv->total, 0) }} {{ $inv->currency }}</td>
                            <td class="px-4 py-3 text-right text-rose-600">{{ $inv->balance_due > 0 ? number_format($inv->balance_due, 0) : '—' }}</td>
                            <td class="px-4 py-3 text-center"><span class="badge-{{ $inv->status_color }} text-xs">{{ ucfirst($inv->status) }}</span></td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('public.invoice.show', $inv->public_token) }}" class="text-pink-600 text-xs font-semibold hover:underline">Lihat →</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $invoices->links() }}</div>
    </div>
</div>
</body>
</html>
