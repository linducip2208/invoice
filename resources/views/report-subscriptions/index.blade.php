<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Email Report Subscriptions') }} <span class="text-2xl">📨</span></h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-5">
        @if(session('success'))<div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800">✓ {{ session('success') }}</div>@endif

        <details class="card-unicorn !p-6">
            <summary class="cursor-pointer font-display font-bold text-purple-900">+ {{ __('Subscribe ke Report') }}</summary>
            <form method="POST" action="{{ route('report-subscriptions.store') }}" class="mt-5 space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label :value="__('Report')" />
                        <select name="report_type" required class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                            <option value="weekly_summary">📊 Weekly Summary</option>
                            <option value="monthly_pl">💰 Monthly P&L per Klien</option>
                            <option value="quarterly_tax">🏛️ Quarterly Tax Summary</option>
                            <option value="aging_report">⏳ Aging Report</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('Frekuensi')" />
                        <select name="frequency" required class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                            <option value="weekly">Mingguan</option>
                            <option value="monthly">Bulanan</option>
                            <option value="quarterly">Per kuartal</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input-label :value="__('Hari (mingguan)')" />
                        <select name="day_of_week" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                            @foreach(['1'=>'Senin','2'=>'Selasa','3'=>'Rabu','4'=>'Kamis','5'=>'Jumat','6'=>'Sabtu','7'=>'Minggu'] as $d => $name)
                                <option value="{{ $d }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('Tanggal (bulanan)')" />
                        <x-text-input name="day_of_month" type="number" min="1" max="28" :value="1" class="mt-1 w-full" />
                    </div>
                    <div>
                        <x-input-label :value="__('Jam')" />
                        <x-text-input name="hour" type="number" min="0" max="23" :value="8" class="mt-1 w-full" />
                    </div>
                </div>

                <button type="submit" class="btn-unicorn">+ {{ __('Subscribe') }}</button>
            </form>
        </details>

        <div class="card-unicorn !p-0 overflow-hidden">
            <div class="p-5 border-b border-purple-100">
                <h3 class="font-display font-bold text-purple-900">Active Subscriptions ({{ $subscriptions->count() }})</h3>
            </div>
            @if($subscriptions->isEmpty())
                <p class="p-8 text-center text-purple-400">Belum ada subscription.</p>
            @else
                <div class="divide-y divide-purple-100">
                    @foreach($subscriptions as $s)
                        <div class="p-4 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-purple-900">{{ str_replace('_', ' ', ucfirst($s->report_type)) }}</p>
                                <p class="text-xs text-purple-500">{{ ucfirst($s->frequency) }} · jam {{ $s->hour }}:00 · next: {{ $s->next_send_at?->format('d M Y') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <form method="POST" action="{{ route('report-subscriptions.toggle', $s) }}">@csrf
                                    <button class="text-xs font-semibold {{ $s->is_active ? 'text-emerald-600' : 'text-gray-400' }}">
                                        {{ $s->is_active ? '● Active' : '○ Paused' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('report-subscriptions.destroy', $s) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')
                                    <button class="text-rose-500 text-xs hover:underline">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
