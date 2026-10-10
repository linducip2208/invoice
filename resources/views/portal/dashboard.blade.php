<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Portal — {{ $client->name }}</title>
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
            <a href="{{ route('portal.invoices') }}" class="text-purple-700 hover:text-pink-500 font-semibold">Invoices</a>
            <a href="{{ route('portal.quotes') }}" class="text-purple-700 hover:text-pink-500 font-semibold">Quotes</a>
            <form method="POST" action="{{ route('portal.logout') }}" class="inline">@csrf
                <button type="submit" class="text-rose-500 hover:underline text-sm">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">
    <div class="card-unicorn-glow !p-6">
        <p class="text-xs uppercase tracking-wider text-purple-400">Hi, {{ $portalUser->email }}</p>
        <h1 class="font-display text-2xl font-bold text-purple-900 mt-1">{{ $client->company_name ?: $client->name }}</h1>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card-unicorn !p-5 bg-gradient-to-br from-rose-50 to-pink-50">
            <p class="text-xs uppercase tracking-wider text-rose-700">Outstanding</p>
            <p class="font-display text-2xl font-bold text-rose-700 mt-1">{{ number_format($stats['total_outstanding'], 0) }}</p>
            <p class="text-xs text-rose-600 mt-1">{{ $stats['unpaid_count'] }} invoice belum lunas</p>
        </div>
        <div class="card-unicorn !p-5 bg-gradient-to-br from-emerald-50 to-teal-50">
            <p class="text-xs uppercase tracking-wider text-emerald-700">Total Paid</p>
            <p class="font-display text-2xl font-bold text-emerald-700 mt-1">{{ number_format($stats['total_paid'], 0) }}</p>
        </div>
        <div class="card-unicorn !p-5">
            <p class="text-xs uppercase tracking-wider text-purple-400">Total Invoices</p>
            <p class="font-display text-2xl font-bold text-purple-900 mt-1">{{ $stats['invoice_count'] }}</p>
        </div>
        <div class="card-unicorn !p-5">
            <p class="text-xs uppercase tracking-wider text-purple-400">Belum Lunas</p>
            <p class="font-display text-2xl font-bold text-purple-900 mt-1">{{ $stats['unpaid_count'] }}</p>
        </div>
    </div>

    <div class="card-unicorn !p-0 overflow-hidden">
        <div class="p-6 flex items-center justify-between border-b border-purple-100">
            <h2 class="font-display font-bold text-lg text-purple-900">Invoice Terbaru</h2>
            <a href="{{ route('portal.invoices') }}" class="text-pink-500 text-sm font-semibold hover:underline">Lihat semua →</a>
        </div>
        @if($invoices->isEmpty())
            <p class="p-8 text-center text-purple-400">Belum ada invoice.</p>
        @else
            <div class="divide-y divide-purple-100">
                @foreach($invoices->take(10) as $inv)
                    <a href="{{ route('public.invoice.show', $inv->public_token) }}" class="flex items-center justify-between p-4 hover:bg-purple-50/40 no-underline">
                        <div>
                            <p class="font-bold text-purple-900">{{ $inv->invoice_no }}</p>
                            <p class="text-xs text-purple-500">{{ $inv->issue_date->format('d M Y') }} · <span class="badge-{{ $inv->status_color }} text-[10px]">{{ ucfirst($inv->status) }}</span></p>
                        </div>
                        <div class="text-right">
                            <p class="font-display font-bold text-purple-900">{{ number_format($inv->total, 0) }}</p>
                            @if($inv->balance_due > 0)
                                <p class="text-xs text-rose-600">Sisa {{ number_format($inv->balance_due, 0) }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
</body>
</html>
