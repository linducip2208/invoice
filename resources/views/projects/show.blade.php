<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <span class="w-3 h-12 rounded-full" style="background: {{ $project->color ?? '#A78BFA' }};"></span>
                <div>
                    <h2 class="heading-unicorn flex items-center gap-2">{{ $project->name }}</h2>
                    <p class="text-sm text-purple-500 mt-1">👤 {{ $project->client?->name }} · <span class="badge-{{ match($project->status){'active'=>'mint','on_hold'=>'gold','completed'=>'blue','cancelled'=>'gray',default=>'gray'} }} text-xs">{{ ucfirst(str_replace('_',' ', $project->status)) }}</span></p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('projects.edit', $project) }}" class="btn-unicorn-secondary text-sm">✏️ {{ __('Edit') }}</a>
                <a href="{{ route('projects.index') }}" class="text-sm font-semibold text-purple-500 hover:text-pink-500">← {{ __('Semua Project') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800">✓ {{ session('success') }}</div>
        @endif

        {{-- Stats grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="card-unicorn !p-5">
                <p class="text-xs uppercase tracking-wider text-purple-400">{{ __('Total Tagihan') }}</p>
                <p class="font-display text-2xl font-bold text-purple-900 mt-1">{{ number_format($stats['total_invoiced'], 0) }}</p>
                <p class="text-xs text-purple-400 mt-1">{{ $stats['invoice_count'] }} invoice</p>
            </div>
            <div class="card-unicorn !p-5 bg-gradient-to-br from-emerald-50 to-teal-50">
                <p class="text-xs uppercase tracking-wider text-emerald-700">{{ __('Sudah Lunas') }}</p>
                <p class="font-display text-2xl font-bold text-emerald-700 mt-1">{{ number_format($stats['total_paid'], 0) }}</p>
                <p class="text-xs text-emerald-600 mt-1">Sisa: {{ number_format($stats['total_unpaid'], 0) }}</p>
            </div>
            <div class="card-unicorn !p-5 bg-gradient-to-br from-amber-50 to-yellow-50">
                <p class="text-xs uppercase tracking-wider text-amber-700">{{ __('Total Jam') }}</p>
                <p class="font-display text-2xl font-bold text-amber-700 mt-1">{{ number_format($stats['total_hours'], 1) }}</p>
                <p class="text-xs text-amber-600 mt-1">{{ $stats['time_count'] }} entries</p>
            </div>
            <div class="card-unicorn !p-5 bg-gradient-to-br from-rose-50 to-pink-50">
                <p class="text-xs uppercase tracking-wider text-rose-700">{{ __('Pengeluaran') }}</p>
                <p class="font-display text-2xl font-bold text-rose-700 mt-1">{{ number_format($stats['total_expense'], 0) }}</p>
                <p class="text-xs text-rose-600 mt-1">{{ $stats['expense_count'] }} item</p>
            </div>
        </div>

        {{-- Margin --}}
        @if($stats['total_invoiced'] > 0)
            <div class="card-unicorn-glow !p-6">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-purple-400">{{ __('Margin') }}</p>
                        <p class="font-display text-3xl font-bold {{ $stats['margin'] >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                            {{ $stats['margin'] >= 0 ? '+' : '' }}{{ number_format($stats['margin'], 0) }}
                        </p>
                        <p class="text-sm text-purple-500 mt-1">{{ $stats['margin_pct'] }}% margin</p>
                    </div>
                    @if($project->budget_amount)
                        <div class="flex-1 max-w-md ml-6">
                            <div class="flex items-center justify-between text-xs text-purple-500 mb-1">
                                <span>Budget Used</span>
                                <span>{{ number_format($stats['total_invoiced'], 0) }} / {{ number_format($project->budget_amount, 0) }}</span>
                            </div>
                            <div class="h-3 bg-purple-100 rounded-full overflow-hidden">
                                @php $pct = min(100, $stats['total_invoiced'] / $project->budget_amount * 100); @endphp
                                <div class="h-full rounded-full" style="width: {{ $pct }}%; background: linear-gradient(90deg, #FF7AC6, #A78BFA);"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if($project->description)
            <div class="card-unicorn !p-6">
                <p class="text-xs uppercase tracking-wider text-purple-400 mb-2">{{ __('Deskripsi') }}</p>
                <p class="text-purple-700 whitespace-pre-line">{{ $project->description }}</p>
            </div>
        @endif

        {{-- Invoices --}}
        <div class="card-unicorn !p-0 overflow-hidden">
            <div class="p-6 flex items-center justify-between border-b border-purple-100">
                <h3 class="font-display font-bold text-lg text-purple-900">📄 {{ __('Invoice di Project Ini') }}</h3>
                <a href="{{ route('invoices.create', ['project_id' => $project->id]) }}" class="btn-unicorn text-sm">+ Invoice</a>
            </div>
            @if($invoices->isEmpty())
                <p class="p-8 text-center text-purple-400 text-sm">Belum ada invoice di project ini.</p>
            @else
                <div class="divide-y divide-purple-100">
                    @foreach($invoices as $inv)
                        <a href="{{ route('invoices.show', $inv) }}" class="flex items-center justify-between p-4 hover:bg-purple-50/40 no-underline">
                            <div>
                                <p class="font-bold text-purple-900">{{ $inv->invoice_no }}</p>
                                <p class="text-xs text-purple-500">{{ $inv->issue_date->format('d M Y') }} · <span class="badge-{{ $inv->status_color }} text-[10px]">{{ ucfirst($inv->status) }}</span></p>
                            </div>
                            <p class="font-display font-bold text-purple-900">{{ number_format($inv->total, 0) }} {{ $inv->currency }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
