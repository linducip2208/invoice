<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quotes — Customer Portal</title>
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
            <a href="{{ route('portal.quotes') }}" class="text-pink-500 font-bold">Quotes</a>
            <form method="POST" action="{{ route('portal.logout') }}" class="inline">@csrf
                <button type="submit" class="text-rose-500 hover:underline text-sm">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="font-display text-2xl font-bold text-purple-900 mb-6">📋 Quote Saya</h1>

    @if($quotes->isEmpty())
        <div class="card-unicorn !p-12 text-center">
            <p class="text-5xl mb-3">📋</p>
            <p class="font-display font-bold text-lg text-purple-700">Belum ada quote</p>
        </div>
    @else
        <div class="card-unicorn !p-0 overflow-hidden">
            <div class="divide-y divide-purple-100">
                @foreach($quotes as $q)
                    <a href="{{ route('public.quote.show', $q->public_token) }}" class="flex items-center justify-between p-5 hover:bg-purple-50/40 no-underline">
                        <div>
                            <p class="font-bold text-purple-900">{{ $q->quote_no }}</p>
                            <p class="text-xs text-purple-500">{{ $q->issue_date->format('d M Y') }} · {{ ucfirst($q->status) }}</p>
                        </div>
                        <p class="font-display font-bold text-purple-900">{{ number_format($q->total, 0) }} {{ $q->currency }}</p>
                    </a>
                @endforeach
            </div>
            <div class="p-4">{{ $quotes->links() }}</div>
        </div>
    @endif
</div>
</body>
</html>
