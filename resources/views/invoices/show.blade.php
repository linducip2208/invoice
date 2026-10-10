<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="heading-unicorn flex items-center gap-2">
                    {{ __('Invoice') }} {{ $invoice->invoice_no }}
                    <span class="text-2xl">📜</span>
                </h2>
                <span class="badge-{{ $invoice->status_color }} text-sm">{{ ucfirst($invoice->status) }}</span>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($invoice->status === 'draft')
                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn-unicorn-secondary">✏️ {{ __('Edit') }}</a>
                @endif
                @if(in_array($invoice->status, ['draft', 'sent', 'partial', 'overdue']))
                    <button type="button" x-data x-on:click="$dispatch('open-modal', 'send-invoice-modal')" class="btn-unicorn">📨 {{ __('Send') }}</button>
                @endif
                <a href="{{ route('invoices.pdf', $invoice) }}" class="btn-unicorn-secondary">📥 {{ __('PDF') }}</a>
                <form method="POST" action="{{ route('invoices.duplicate', $invoice) }}" class="inline">
                    @csrf
                    <button class="btn-unicorn-secondary">🧬 {{ __('Duplicate') }}</button>
                </form>
                @if(in_array($invoice->status, ['draft', 'cancelled']))
                    <form method="POST" action="{{ route('invoices.update-status', $invoice) }}" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="sent">
                        <button class="btn-unicorn-secondary">✅ {{ __('Mark Sent') }}</button>
                    </form>
                @endif
                @if(!in_array($invoice->status, ['paid', 'cancelled']))
                    <form method="POST" action="{{ route('invoices.update-status', $invoice) }}" class="inline" onsubmit="return confirm('{{ __('Cancel this invoice?') }}')">
                        @csrf
                        <input type="hidden" name="status" value="cancelled">
                        <button class="btn-unicorn-secondary text-rose-600 border-rose-200">{{ __('Cancel') }}</button>
                    </form>
                @endif
                @if($invoice->status === 'draft')
                    <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" class="inline" onsubmit="return confirm('{{ __('Delete this invoice?') }}')">
                        @csrf @method('DELETE')
                        <x-danger-button>🗑️ {{ __('Delete') }}</x-danger-button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        @php $ws = workspace(); @endphp

        <!-- Invoice header card -->
        <div class="card-unicorn">
            <div class="rainbow-bar -mx-6 -mt-6 mb-6 rounded-t-2xl"></div>
            <div class="flex flex-col md:flex-row md:justify-between gap-6">
                <div>
                    @if($ws?->logo_path)
                        <img src="{{ Storage::disk('public')->url($ws->logo_path) }}" alt="Logo" class="h-12 mb-3">
                    @else
                        <span class="logo-glow inline-flex mb-3"><x-application-logo size="md" /></span>
                    @endif
                    <h3 class="font-display font-bold text-lg text-purple-900">{{ $ws?->name ?? config('app.name') }}</h3>
                    @if($ws?->address) <p class="text-sm text-purple-500">{{ $ws->address }}</p> @endif
                    @if($ws?->email)   <p class="text-sm text-purple-500">{{ $ws->email }}</p> @endif
                    @if($ws?->phone)   <p class="text-sm text-purple-500">{{ $ws->phone }}</p> @endif
                </div>
                <div class="text-left md:text-right">
                    <p class="text-xs font-semibold text-purple-400 uppercase tracking-wider">{{ __('Invoice #') }}</p>
                    <p class="font-display text-2xl font-bold text-purple-900">{{ $invoice->invoice_no }}</p>
                    <div class="mt-3 space-y-0.5 text-sm">
                        <p class="text-purple-500"><span class="font-semibold text-purple-700">{{ __('Issue:') }}</span> {{ $invoice->issue_date?->format('M d, Y') }}</p>
                        <p class="text-purple-500"><span class="font-semibold text-purple-700">{{ __('Due:') }}</span> {{ $invoice->due_date?->format('M d, Y') }}</p>
                        @if($invoice->invoice_layout_id)
                            <p class="text-purple-500"><span class="font-semibold text-purple-700">{{ __('Layout:') }}</span> 🎨 {{ $invoice->layout?->name ?? 'Custom' }} <span class="pill pill-pink ml-1 text-[9px]">CUSTOM</span></p>
                        @elseif($invoice->template)
                            <p class="text-purple-500"><span class="font-semibold text-purple-700">{{ __('Template:') }}</span> {{ ucfirst($invoice->template) }}</p>
                        @endif
                        <p class="text-purple-500"><span class="font-semibold text-purple-700">{{ __('Currency:') }}</span> {{ $invoice->currency }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-purple-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-xs font-semibold text-purple-400 uppercase tracking-wider mb-2">✦ {{ __('Bill To') }}</h4>
                        <p class="font-display font-bold text-purple-900">{{ $invoice->client?->name ?? '—' }}</p>
                        @if($invoice->client?->company_name) <p class="text-sm text-purple-700">{{ $invoice->client->company_name }}</p> @endif
                        @if($invoice->client?->address)     <p class="text-sm text-purple-500">{{ $invoice->client->address }}</p> @endif
                        @if($invoice->client && ($invoice->client->city || $invoice->client->state || $invoice->client->postal_code))
                            <p class="text-sm text-purple-500">{{ collect([$invoice->client->city, $invoice->client->state, $invoice->client->postal_code])->filter()->join(', ') }}</p>
                        @endif
                        @if($invoice->client?->country) <p class="text-sm text-purple-500">{{ $invoice->client->country }}</p> @endif
                        @if($invoice->client?->email)   <p class="text-sm text-purple-500">{{ $invoice->client->email }}</p> @endif
                        @if($invoice->client?->tax_id)  <p class="text-sm text-purple-500">{{ __('Tax ID:') }} {{ $invoice->client->tax_id }}</p> @endif
                    </div>
                    <div class="text-left md:text-right">
                        @if($invoice->primary_color || $invoice->accent_color)
                            <div class="flex gap-2 md:justify-end mb-2">
                                @if($invoice->primary_color)
                                    <span class="inline-flex items-center gap-1 text-xs text-purple-500">
                                        <span class="w-4 h-4 rounded border border-purple-200 inline-block" style="background-color: {{ $invoice->primary_color }}"></span>
                                        {{ __('Primary') }}
                                    </span>
                                @endif
                                @if($invoice->accent_color)
                                    <span class="inline-flex items-center gap-1 text-xs text-purple-500">
                                        <span class="w-4 h-4 rounded border border-purple-200 inline-block" style="background-color: {{ $invoice->accent_color }}"></span>
                                        {{ __('Accent') }}
                                    </span>
                                @endif
                            </div>
                        @endif
                        <a href="{{ $invoice->public_url }}" target="_blank" class="text-sm text-pink-500 hover:text-pink-600 font-semibold">
                            🔗 {{ __('Open public link →') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="card-unicorn p-0 overflow-hidden">
            <div class="px-6 py-4">
                <h3 class="font-display font-bold text-lg text-purple-900 flex items-center gap-2">
                    <span class="text-pink-400">✦</span> {{ __('Items') }}
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="table-unicorn">
                    <thead>
                        <tr>
                            <th class="w-12">#</th>
                            <th>{{ __('Description') }}</th>
                            <th class="text-right">{{ __('Qty') }}</th>
                            <th class="text-right">{{ __('Unit') }}</th>
                            <th class="text-right">{{ __('Tax %') }}</th>
                            <th class="text-right">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->items as $index => $item)
                            <tr>
                                <td class="text-purple-400">{{ $index + 1 }}</td>
                                <td class="text-purple-900 font-medium">
                                    {{ $item->description }}
                                    @if($item->product) <span class="text-xs text-purple-400 block">{{ $item->product->name }}</span> @endif
                                </td>
                                <td class="text-right text-purple-700">{{ number_format((float) $item->quantity, 2) }}</td>
                                <td class="text-right text-purple-700">{{ number_format((float) $item->unit_price, 2) }}</td>
                                <td class="text-right text-purple-500">{{ number_format((float) $item->tax_rate, 2) }}%</td>
                                <td class="text-right font-bold text-purple-900">{{ number_format((float) $item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-5 flex justify-end" style="background: linear-gradient(90deg, rgba(255,228,241,0.4), rgba(237,233,254,0.4));">
                <div class="w-full max-w-xs space-y-1.5 text-sm">
                    <div class="flex justify-between"><span class="text-purple-500">{{ __('Subtotal') }}</span><span class="text-purple-900 font-semibold">{{ number_format((float) $invoice->subtotal, 2) }}</span></div>
                    @if((float) $invoice->discount_amount > 0)
                        <div class="flex justify-between">
                            <span class="text-purple-500">{{ __('Discount') }} @if($invoice->discount_type === 'percent')({{ number_format((float) $invoice->discount_value, 2) }}%)@endif</span>
                            <span class="text-rose-600 font-semibold">-{{ number_format((float) $invoice->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    @if((float) $invoice->tax_amount > 0)
                        <div class="flex justify-between"><span class="text-purple-500">{{ __('Tax') }} ({{ number_format((float) $invoice->tax_rate, 2) }}%)</span><span class="text-purple-900 font-semibold">{{ number_format((float) $invoice->tax_amount, 2) }}</span></div>
                    @endif
                    <div class="flex justify-between text-base font-display font-bold border-t border-purple-200 pt-2">
                        <span class="text-purple-900">{{ __('Total') }}</span>
                        <span class="text-rainbow">{{ number_format((float) $invoice->total, 2) }} {{ $invoice->currency }}</span>
                    </div>
                    @if((float) $invoice->amount_paid > 0)
                        <div class="flex justify-between"><span class="text-green-600">{{ __('Paid') }}</span><span class="text-green-600 font-semibold">{{ number_format((float) $invoice->amount_paid, 2) }}</span></div>
                    @endif
                    <div class="flex justify-between font-semibold border-t border-purple-200 pt-1.5">
                        <span class="text-purple-700">{{ __('Balance Due') }}</span>
                        <span class="{{ $invoice->balance_due > 0 ? 'text-pink-600' : 'text-green-600' }}">{{ number_format((float) $invoice->balance_due, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes & Terms -->
        @if($invoice->notes || $invoice->terms)
            <div class="card-unicorn">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($invoice->notes)
                        <div>
                            <h4 class="text-xs font-semibold text-purple-400 uppercase tracking-wider mb-2">✦ {{ __('Notes') }}</h4>
                            <p class="text-sm text-purple-700 whitespace-pre-line bg-purple-50/40 rounded-xl p-3 border border-purple-100">{{ $invoice->notes }}</p>
                        </div>
                    @endif
                    @if($invoice->terms)
                        <div>
                            <h4 class="text-xs font-semibold text-purple-400 uppercase tracking-wider mb-2">✦ {{ __('Terms') }}</h4>
                            <p class="text-sm text-purple-700 whitespace-pre-line bg-purple-50/40 rounded-xl p-3 border border-purple-100">{{ $invoice->terms }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Payments -->
        <div class="card-unicorn p-0 overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between">
                <h3 class="font-display font-bold text-lg text-purple-900 flex items-center gap-2">
                    <span class="text-pink-400">✦</span> {{ __('Payments') }}
                </h3>
                @if((float) $invoice->balance_due > 0 && !in_array($invoice->status, ['draft', 'cancelled']))
                    <button type="button" x-data x-on:click="$dispatch('open-modal', 'add-payment-modal')" class="btn-unicorn">
                        💖 {{ __('Record Payment') }}
                    </button>
                @endif
            </div>
            @include('payments._list', ['invoice' => $invoice])
        </div>

        <!-- View Tracking + Attachments — premium widgets -->
        @php
            $views = $invoice->publicViews ?? collect();
            $attachments = $invoice->attachments ?? collect();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- View tracking -->
            <div class="glass-card p-5">
                <h3 class="h-display text-base font-bold text-purple-900 flex items-center gap-2 mb-3">
                    <span class="text-sky-400">👁️</span> {{ __('Client Views') }}
                </h3>
                @if($views->isNotEmpty())
                    <p class="kpi-value text-3xl">{{ $views->count() }}<span class="text-sm font-normal text-purple-400 ml-2">{{ __('views') }}</span></p>
                    <p class="text-xs text-purple-500 mt-1">
                        {{ __('First viewed') }}: {{ $views->min('created_at')?->diffForHumans() }} ·
                        {{ __('Last') }}: {{ $views->max('created_at')?->diffForHumans() }}
                    </p>
                    <div class="mt-3 max-h-32 overflow-y-auto text-xs text-purple-600 space-y-1">
                        @foreach($views->sortByDesc('created_at')->take(5) as $v)
                            <div class="flex justify-between"><span class="font-mono">{{ $v->ip_address ?? '—' }}</span><span class="text-purple-400">{{ $v->created_at->diffForHumans() }}</span></div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-purple-400">🔕 {{ __('Belum dilihat klien.') }}</p>
                    <p class="text-xs text-purple-400 mt-2">{{ __('Akan tercatat otomatis saat klien buka link publik.') }}</p>
                @endif
            </div>

            <!-- Attachments -->
            <div class="glass-card p-5">
                <h3 class="h-display text-base font-bold text-purple-900 flex items-center gap-2 mb-3">
                    <span class="text-pink-400">📎</span> {{ __('Attachments') }}
                </h3>
                <form method="POST" action="{{ route('invoices.attachments.store', $invoice) }}" enctype="multipart/form-data" class="space-y-2 mb-3">
                    @csrf
                    <input type="file" name="file" required class="block w-full text-xs">
                    <button class="btn-ghost text-xs">+ {{ __('Upload') }}</button>
                </form>
                @if($attachments->isNotEmpty())
                    <div class="space-y-1.5 text-sm">
                        @foreach($attachments as $att)
                            <div class="flex items-center justify-between gap-2 p-2 rounded-lg bg-white/50">
                                <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="text-purple-700 hover:text-pink-500 font-medium truncate flex-1 text-xs">📄 {{ $att->file_name }}</a>
                                <span class="text-[10px] text-purple-400">{{ number_format($att->file_size / 1024, 0) }}KB</span>
                                <form method="POST" action="{{ route('invoices.attachments.destroy', $att) }}" onsubmit="return confirm('Hapus?');">@csrf @method('DELETE')<button class="text-rose-400 text-xs">×</button></form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-purple-400">{{ __('Belum ada lampiran.') }}</p>
                @endif
            </div>
        </div>

        <!-- Email Log -->
        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg text-purple-900 flex items-center gap-2 mb-3">
                <span class="text-purple-400">✦</span> {{ __('Email Log') }}
            </h3>
            @if($invoice->emailLogs->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="table-unicorn">
                        <thead>
                            <tr>
                                <th>{{ __('Sent At') }}</th>
                                <th>{{ __('To') }}</th>
                                <th>{{ __('Subject') }}</th>
                                <th class="text-center">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->emailLogs as $log)
                                <tr>
                                    <td class="whitespace-nowrap text-purple-700">{{ $log->sent_at?->format('M d, Y H:i') }}</td>
                                    <td class="text-purple-700">{{ $log->to_email }}</td>
                                    <td class="text-purple-500">{{ $log->subject }}</td>
                                    <td class="text-center">
                                        <span class="badge-{{ $log->status === 'sent' ? 'green' : ($log->status === 'failed' ? 'red' : 'blue') }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-purple-400">{{ __('No emails sent yet.') }}</p>
            @endif
        </div>

        <!-- Public Link -->
        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg text-purple-900 flex items-center gap-2 mb-3">
                <span class="text-sky-400">✦</span> {{ __('Public Link') }}
            </h3>
            <div class="flex items-center gap-2">
                <input type="text" readonly value="{{ $invoice->public_url }}" id="public-url-input" class="input-unicorn rounded-xl px-4 py-2 flex-1 text-sm bg-purple-50/40">
                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('public-url-input').value).then(() => { const b = event.target; b.textContent = '✓ Copied!'; setTimeout(() => b.textContent = '✦ Copy', 1500); })" class="btn-unicorn">
                    ✦ {{ __('Copy') }}
                </button>
            </div>
        </div>
    </div>

    @include('invoices._send-modal', ['invoice' => $invoice])
    @include('payments._form', ['invoice' => $invoice])
</x-app-layout>
