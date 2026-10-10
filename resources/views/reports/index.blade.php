<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Reports') }} <span class="text-2xl">📊</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Magical insights into your invoicing universe') }}</p>
            </div>
            <form method="GET" class="flex items-center gap-2">
                <select name="period" onchange="this.form.submit()" class="input-unicorn rounded-full px-4 py-2 text-sm font-semibold text-purple-700">
                    <option value="7"   @selected($period==='7')>{{ __('Last 7 days') }}</option>
                    <option value="30"  @selected($period==='30')>{{ __('Last 30 days') }}</option>
                    <option value="90"  @selected($period==='90')>{{ __('Last 90 days') }}</option>
                    <option value="365" @selected($period==='365')>{{ __('Last 12 months') }}</option>
                    <option value="ytd" @selected($period==='ytd')>{{ __('Year to date') }}</option>
                </select>
            </form>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        @php $cur = workspace()?->currency ?? 'IDR'; @endphp

        <!-- KPI -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['label' => __('Invoiced'),   'val' => number_format($totals['invoiced'], 0),    'sub' => $totals['invoiceCount'].' invoices', 'g' => 'linear-gradient(135deg,#A78BFA,#7DD3FC)', 'icon' => '📜'],
                ['label' => __('Collected'),  'val' => number_format($totals['collected'], 0),   'sub' => $totals['paymentCount'].' payments', 'g' => 'linear-gradient(135deg,#86EFAC,#34D399)', 'icon' => '💰'],
                ['label' => __('Outstanding'),'val' => number_format($totals['outstanding'], 0), 'sub' => __('All-time pending'),               'g' => 'linear-gradient(135deg,#FDBA74,#FB923C)', 'icon' => '⏳'],
                ['label' => __('Overdue'),    'val' => number_format($totals['overdue'], 0),     'sub' => __('Needs attention'),                'g' => 'linear-gradient(135deg,#FB7185,#F43F5E)', 'icon' => '🚨'],
            ] as $i => $kpi)
                <div class="card-unicorn relative overflow-hidden">
                    <div class="absolute -top-8 -right-8 w-24 h-24 rounded-full opacity-15" style="background: {{ $kpi['g'] }};"></div>
                    <div class="flex items-start justify-between relative">
                        <div>
                            <p class="text-xs font-semibold text-purple-400 uppercase tracking-wider">{{ $kpi['label'] }}</p>
                            <p class="font-display text-2xl font-bold text-purple-900 mt-1">
                                <span class="text-xs text-purple-400 mr-1">{{ $cur }}</span>{{ $kpi['val'] }}
                            </p>
                            <p class="text-xs text-purple-400 mt-1">{{ $kpi['sub'] }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shadow-unicorn-card" style="background: {{ $kpi['g'] }};">{{ $kpi['icon'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Monthly chart (CSS bars) -->
        @if($monthly->isNotEmpty())
            @php $maxMonthly = max($monthly->pluck('total')->toArray()) ?: 1; @endphp
            <div class="card-unicorn">
                <h3 class="font-display font-bold text-lg text-purple-900 mb-4 flex items-center gap-2">
                    <span class="text-pink-400">✦</span> {{ __('Monthly Invoiced (last 12 months)') }}
                </h3>
                <div class="flex items-end gap-2 h-44 overflow-x-auto pb-2">
                    @foreach($monthly as $m)
                        @php $h = max(8, ($m->total / $maxMonthly) * 100); @endphp
                        <div class="flex-1 min-w-[40px] flex flex-col items-center gap-1">
                            <span class="text-[10px] font-semibold text-purple-700">{{ number_format($m->total / 1000, 1) }}k</span>
                            <div class="w-full rounded-t-xl transition-all hover:opacity-80" style="height: {{ $h }}%; background: linear-gradient(180deg, #FF7AC6 0%, #A78BFA 50%, #7DD3FC 100%);" title="{{ $m->total }}"></div>
                            <span class="text-[10px] text-purple-400 mt-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($m->month.'-01')->format('M y') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid lg:grid-cols-2 gap-6">
            <!-- Status breakdown -->
            <div class="card-unicorn">
                <h3 class="font-display font-bold text-lg text-purple-900 mb-4 flex items-center gap-2">
                    <span class="text-purple-400">✦</span> {{ __('Invoice Status') }}
                </h3>
                <div class="space-y-3">
                    @foreach(['draft','sent','partial','paid','overdue','cancelled'] as $st)
                        @php $row = $statusBreakdown->get($st); $count = $row->count ?? 0; $total = $row->total ?? 0; @endphp
                        <div class="flex items-center gap-3">
                            <span class="badge-{{ match($st){'draft'=>'gray','sent'=>'blue','paid'=>'green','partial'=>'yellow','overdue'=>'red',default=>'gray'} }} min-w-[80px] justify-center">{{ ucfirst($st) }}</span>
                            <div class="flex-1 h-2 bg-purple-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full" style="width: {{ $count ? min(100, $count * 8) : 0 }}%; background: linear-gradient(90deg, #FF7AC6, #A78BFA);"></div>
                            </div>
                            <span class="text-sm font-bold text-purple-900 min-w-[40px] text-right">{{ $count }}</span>
                            <span class="text-xs text-purple-400 min-w-[80px] text-right">{{ number_format($total, 0) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Payment method breakdown -->
            <div class="card-unicorn">
                <h3 class="font-display font-bold text-lg text-purple-900 mb-4 flex items-center gap-2">
                    <span class="text-sky-400">✦</span> {{ __('Payments by Method') }}
                </h3>
                @if($methodBreakdown->isEmpty())
                    <p class="text-sm text-purple-400 text-center py-8">{{ __('No payments in this period.') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach($methodBreakdown as $m)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-pink-50 via-purple-50 to-sky-50">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">
                                        @php
                                            $icons = [
                                                'cash' => '💵', 'bank_transfer' => '🏦', 'credit_card' => '💳',
                                                'bca' => '🏦', 'bni' => '🏦', 'bri' => '🏦', 'mandiri' => '🏦',
                                                'permata' => '🏦', 'cimb' => '🏦', 'va' => '🏦',
                                                'gopay' => '🔵', 'ovo' => '🟣', 'dana' => '🔷',
                                                'shopeepay' => '🟠', 'linkaja' => '🔴',
                                                'qris' => '📱', 'convenience_store' => '🏪', 'other' => '✦',
                                            ];
                                        @endphp
                                        {{ $icons[$m->payment_method] ?? '✦' }}
                                    <div>
                                        <p class="font-semibold text-purple-900">{{ ucwords(str_replace('_',' ',$m->payment_method)) }}</p>
                                        <p class="text-xs text-purple-400">{{ $m->count }} {{ __('payments') }}</p>
                                    </div>
                                </div>
                                <p class="font-bold text-green-600">{{ number_format($m->total, 0) }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Top clients -->
        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-4 flex items-center gap-2">
                <span class="text-pink-400">✦</span> {{ __('Top Clients') }}
            </h3>
            @if($topClients->where('revenue','>',0)->isEmpty())
                <p class="text-sm text-purple-400 text-center py-8">{{ __('No client revenue yet — invite some sparkles!') }}</p>
            @else
                <div class="space-y-2">
                    @foreach($topClients as $client)
                        @if($client->revenue)
                            <a href="{{ route('clients.show', $client) }}" class="flex items-center gap-4 p-3 rounded-xl hover:bg-pink-50/40 transition border border-transparent hover:border-pink-100">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold shadow-unicorn-pink" style="background: linear-gradient(135deg, #FF7AC6, #A78BFA);">
                                    {{ strtoupper(substr($client->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-purple-900 truncate">{{ $client->name }}</p>
                                    <p class="text-xs text-purple-400">{{ $client->invoice_count }} {{ __('invoices') }}</p>
                                </div>
                                <p class="font-bold text-green-600">{{ number_format($client->revenue, 0) }}</p>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
