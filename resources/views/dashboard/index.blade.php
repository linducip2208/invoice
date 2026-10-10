<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">
                    {{ __('Dashboard') }}
                    <span class="animate-unicorn-wiggle text-2xl">🦄</span>
                </h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('A magical overview of your business') }}</p>
            </div>
            <a href="{{ route('invoices.create') }}" class="btn-unicorn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                {{ __('New Invoice') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        @php
            $currency = workspace()?->currency ?? 'IDR';
            $stats = [
                [
                    'label' => __('Total Revenue'),
                    'value' => number_format($totalRevenue, 0),
                    'prefix'=> $currency,
                    'icon'  => '💰',
                    'gradient' => 'linear-gradient(135deg, #86EFAC, #34D399)',
                    'glow'  => 'shadow-unicorn-mint',
                ],
                [
                    'label' => __('Outstanding'),
                    'value' => number_format($outstandingAmount, 0),
                    'prefix'=> $currency,
                    'icon'  => '⏳',
                    'gradient' => 'linear-gradient(135deg, #FDBA74, #FB923C)',
                    'glow'  => 'shadow-unicorn-gold',
                ],
                [
                    'label' => __('Paid'),
                    'value' => $paidCount,
                    'icon'  => '✨',
                    'gradient' => 'linear-gradient(135deg, #7DD3FC, #38BDF8)',
                    'glow'  => 'shadow-unicorn-blue',
                ],
                [
                    'label' => __('Overdue'),
                    'value' => $overdueCount,
                    'icon'  => '🚨',
                    'gradient' => 'linear-gradient(135deg, #FB7185, #F43F5E)',
                    'glow'  => 'shadow-unicorn-pink',
                ],
                [
                    'label' => __('Clients'),
                    'value' => $clientCount,
                    'icon'  => '🦄',
                    'gradient' => 'linear-gradient(135deg, #FF7AC6, #A78BFA)',
                    'glow'  => 'shadow-unicorn-pink',
                ],
            ];
        @endphp

        <!-- KPI Stats — startup unicorn tier -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            @foreach($stats as $i => $stat)
                <div class="kpi-card hover-lift" style="animation-delay: {{ $i * 0.06 }}s;">
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shadow-md" style="background: {{ $stat['gradient'] }};">
                                {{ $stat['icon'] }}
                            </div>
                            <span class="pill pill-emerald text-[10px]">●  Live</span>
                        </div>
                        <p class="kpi-label">{{ $stat['label'] }}</p>
                        <p class="kpi-value mt-1">
                            @isset($stat['prefix'])<span class="text-sm font-medium text-purple-400 mr-1">{{ $stat['prefix'] }}</span>@endisset{{ $stat['value'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Recent Invoices -->
        <div class="card-unicorn p-0 overflow-hidden">
            <div class="rainbow-bar"></div>
            <div class="px-6 py-5 flex items-center justify-between" style="background: linear-gradient(90deg, rgba(255,228,241,0.4), rgba(237,233,254,0.4), rgba(224,242,254,0.3));">
                <h3 class="font-display text-lg font-bold text-purple-900 flex items-center gap-2">
                    <span class="text-pink-400">✦</span>
                    {{ __('Recent Invoices') }}
                </h3>
                <a href="{{ route('invoices.index') }}" class="text-sm font-semibold text-purple-600 hover:text-pink-500 transition flex items-center gap-1">
                    {{ __('View All') }} <span>→</span>
                </a>
            </div>

            @if ($recentInvoices->isEmpty())
                <div class="text-center py-16 px-6 sparkles-bg">
                    <div class="text-7xl mb-4 inline-block animate-unicorn-float">🦄</div>
                    <h3 class="font-display text-xl font-bold text-purple-900">{{ __('No invoices yet!') }}</h3>
                    <p class="mt-2 text-sm text-purple-500">{{ __('Conjure your first sparkly invoice to get started.') }}</p>
                    <div class="mt-5">
                        <a href="{{ route('invoices.create') }}" class="btn-unicorn">
                            ✦ {{ __('Create First Invoice') }}
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table-unicorn">
                        <thead>
                            <tr>
                                <th>{{ __('Invoice #') }}</th>
                                <th>{{ __('Client') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Due Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentInvoices as $invoice)
                                <tr>
                                    <td>
                                        <a href="{{ route('invoices.show', $invoice) }}" class="font-bold text-purple-700 hover:text-pink-500 transition">{{ $invoice->invoice_no }}</a>
                                    </td>
                                    <td class="text-purple-700 font-medium">
                                        {{ $invoice->client?->name ?? '—' }}
                                    </td>
                                    <td class="font-bold text-purple-900">
                                        <span class="text-xs text-purple-400 mr-1">{{ $invoice->currency }}</span>{{ number_format($invoice->total, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge-{{ $invoice->status_color }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td class="text-purple-500">
                                        {{ $invoice->due_date?->format('M d, Y') ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            @php
                $quickActions = [
                    ['label' => __('Add Client'),    'href' => route('clients.create'),  'icon' => '🦄', 'color' => 'from-pink-100 to-purple-100'],
                    ['label' => __('New Product'),   'href' => route('products.create'), 'icon' => '📦', 'color' => 'from-purple-100 to-sky-100'],
                    ['label' => __('Recurring'),     'href' => route('recurring.create'),'icon' => '🔄', 'color' => 'from-sky-100 to-emerald-100'],
                    ['label' => __('Workspace'),     'href' => route('workspace.settings'),'icon' => '⚙️', 'color' => 'from-yellow-100 to-pink-100'],
                ];
            @endphp
            @foreach($quickActions as $action)
                <a href="{{ $action['href'] }}" class="card-unicorn flex items-center gap-4 hover:shadow-unicorn-glow group">
                    <span class="text-3xl bg-gradient-to-br {{ $action['color'] }} w-14 h-14 rounded-xl flex items-center justify-center group-hover:animate-unicorn-bounce">{{ $action['icon'] }}</span>
                    <div>
                        <p class="font-semibold text-purple-900">{{ $action['label'] }}</p>
                        <p class="text-xs text-purple-400">→ {{ __('Get started') }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</x-app-layout>
