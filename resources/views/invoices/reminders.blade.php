<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Reminders') }} <span class="text-2xl">⏰</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Pengingat otomatis untuk') }} <strong>{{ $invoice->invoice_no }}</strong></p>
            </div>
            <a href="{{ route('invoices.show', $invoice) }}" class="btn-unicorn-secondary text-sm">← {{ __('Kembali ke Invoice') }}</a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">

        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800">✓ {{ session('success') }}</div>
        @endif

        {{-- Invoice summary --}}
        <div class="card-unicorn !p-5 flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-xs uppercase tracking-wider text-purple-400">{{ __('Invoice') }}</p>
                <p class="font-display font-bold text-xl text-purple-900">{{ $invoice->invoice_no }}</p>
                <p class="text-sm text-purple-500 mt-1">{{ $invoice->client?->name }} · Jatuh tempo {{ $invoice->due_date->format('d M Y') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs uppercase tracking-wider text-purple-400">{{ __('Sisa Tagihan') }}</p>
                <p class="font-display font-bold text-2xl text-rainbow">{{ $invoice->currency }} {{ number_format($invoice->balance_due ?? $invoice->total, 0) }}</p>
                <span class="badge-{{ $invoice->status_color }} text-xs">{{ ucfirst($invoice->status) }}</span>
            </div>
        </div>

        {{-- Auto-schedule action --}}
        @if(! in_array($invoice->status, ['paid', 'cancelled']))
            <div class="card-unicorn-glow !p-6 flex items-center justify-between flex-wrap gap-4 sparkles-bg">
                <div>
                    <h3 class="font-display font-bold text-lg text-purple-900 flex items-center gap-2">
                        <span class="text-pink-400">✦</span> {{ __('Auto-Schedule Reminder') }}
                    </h3>
                    <p class="text-sm text-purple-600 mt-1">
                        {{ __('Schedule otomatis 4 reminder: H-3, hari-H, H+3, dan H+7 setelah jatuh tempo. Reminder yang ada akan diganti.') }}
                    </p>
                </div>
                <form method="POST" action="{{ route('invoices.reminders.schedule', $invoice) }}">
                    @csrf
                    <button type="submit" class="btn-unicorn">⚡ {{ __('Jadwalkan Otomatis') }}</button>
                </form>
            </div>
        @endif

        {{-- Manual add --}}
        @if(! in_array($invoice->status, ['paid', 'cancelled']))
            <details class="card-unicorn !p-6">
                <summary class="cursor-pointer font-display font-bold text-purple-900 flex items-center gap-2">
                    <span class="text-purple-400">✦</span> {{ __('Tambah Reminder Manual') }}
                </summary>
                <form method="POST" action="{{ route('invoices.reminders.store', $invoice) }}" class="mt-5 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label :value="__('Kapan dikirim')" />
                            <x-text-input name="scheduled_at" type="datetime-local" required class="mt-1 w-full" />
                            @error('scheduled_at') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-input-label :value="__('Subjek email')" />
                            <x-text-input name="template_subject" required class="mt-1 w-full"
                                          placeholder="Pengingat: Invoice {{invoice_no}}" />
                            @error('template_subject') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <x-input-label :value="__('Isi pesan')" />
                        <textarea name="template_body" rows="6" required
                                  class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full font-mono text-sm">Halo {{client_name}},

Invoice {{invoice_no}} sebesar {{currency}} {{amount_due}} masih belum lunas. Mohon segera diselesaikan.

Link: {{pay_link}}</textarea>
                        @error('template_body') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-purple-400 mt-1">
                            {{ __('Variabel:') }}
                            <code>{{client_name}}</code>, <code>{{invoice_no}}</code>, <code>{{amount_due}}</code>,
                            <code>{{currency}}</code>, <code>{{due_date}}</code>, <code>{{pay_link}}</code>,
                            <code>{{workspace_name}}</code>
                        </p>
                    </div>
                    <button type="submit" class="btn-unicorn">+ {{ __('Tambah Reminder') }}</button>
                </form>
            </details>
        @endif

        {{-- Timeline reminders --}}
        <div class="card-unicorn !p-0 overflow-hidden">
            <div class="p-6 border-b border-purple-100">
                <h3 class="font-display font-bold text-lg text-purple-900 flex items-center gap-2">
                    <span class="text-sky-400">✦</span> {{ __('Riwayat Reminder') }}
                    <span class="text-sm font-normal text-purple-500">({{ $reminders->count() }})</span>
                </h3>
            </div>

            @if($reminders->isEmpty())
                <div class="p-12 text-center">
                    <p class="text-4xl mb-3">📭</p>
                    <p class="font-display font-bold text-purple-700">{{ __('Belum ada reminder') }}</p>
                    <p class="text-sm text-purple-400 mt-1">Klik "Jadwalkan Otomatis" untuk setup 4 reminder default.</p>
                </div>
            @else
                <div class="divide-y divide-purple-100">
                    @foreach($reminders as $r)
                        @php
                            $statusBadge = match($r->status) {
                                'sent'      => ['emerald-100','emerald-700','✓ Terkirim'],
                                'pending'   => ['amber-100','amber-700','⏳ Menunggu'],
                                'failed'    => ['rose-100','rose-700','✗ Gagal'],
                                'cancelled' => ['gray-100','gray-600','— Dibatalkan'],
                            };
                            $offsetLabel = $r->schedule_offset < 0
                                ? 'H' . $r->schedule_offset
                                : ($r->schedule_offset === 0 ? 'Hari-H' : 'H+' . $r->schedule_offset);
                        @endphp
                        <div class="p-5 flex items-start gap-4 hover:bg-purple-50/40">
                            <div class="shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center font-display font-bold text-sm text-white shadow-unicorn-pink"
                                 style="background: linear-gradient(135deg, #FF7AC6, #A78BFA);">
                                {{ $offsetLabel }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-bold text-purple-900 truncate">{{ $r->template_subject }}</p>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $statusBadge[0] }} text-{{ $statusBadge[1] }} font-semibold">{{ $statusBadge[2] }}</span>
                                </div>
                                <p class="text-xs text-purple-500 mt-1">
                                    {{ __('Dijadwalkan') }}: {{ $r->scheduled_at->format('d M Y, H:i') }}
                                    @if($r->sent_at) · {{ __('Dikirim') }}: {{ $r->sent_at->format('d M Y, H:i') }} @endif
                                </p>
                                @if($r->fail_reason)
                                    <p class="text-xs text-rose-600 mt-1 bg-rose-50 px-2 py-1 rounded">⚠️ {{ $r->fail_reason }}</p>
                                @endif
                            </div>
                            @if($r->status === 'pending')
                                <form method="POST" action="{{ route('invoices.reminders.cancel', $r) }}" onsubmit="return confirm('Batalkan reminder ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 font-semibold">{{ __('Batalkan') }}</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
