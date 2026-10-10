<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Approval Rules') }} <span class="text-2xl">🛡️</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Atur invoice/quote/expense yang harus di-approve sebelum kirim') }}</p>
            </div>
            <a href="{{ route('approvals.index') }}" class="btn-unicorn-secondary text-sm">📥 Inbox Approval</a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800">✓ {{ session('success') }}</div>
        @endif

        <details class="card-unicorn !p-6">
            <summary class="cursor-pointer font-display font-bold text-purple-900 flex items-center gap-2">
                <span class="text-pink-400">+</span> {{ __('Tambah Rule Baru') }}
            </summary>
            <form method="POST" action="{{ route('approval-rules.store') }}" class="mt-5 space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label :value="__('Nama Rule *')" />
                        <x-text-input name="name" required class="mt-1 w-full" placeholder="Mis. Approval Invoice >50jt" />
                    </div>
                    <div>
                        <x-input-label :value="__('Berlaku untuk *')" />
                        <select name="applies_to" required class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                            <option value="invoice">Invoice</option>
                            <option value="quote">Quote / Estimate</option>
                            <option value="expense">Expense</option>
                            <option value="credit_note">Credit Note</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label :value="__('Kondisi *')" />
                        <select name="condition_type" required class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                            <option value="amount_gte">Jumlah ≥ batas tertentu</option>
                            <option value="always">Selalu butuh approval</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('Batas Jumlah (Rp)')" />
                        <x-text-input name="condition_value" type="number" step="0.01" min="0" class="mt-1 w-full" placeholder="50000000" />
                    </div>
                </div>

                <div>
                    <x-input-label :value="__('Approver(s) *')" />
                    <div class="grid grid-cols-2 gap-2 mt-1">
                        @foreach($users as $u)
                            <label class="flex items-center gap-2 px-3 py-2 rounded-xl border border-purple-200 hover:border-pink-300 cursor-pointer">
                                <input type="checkbox" name="approver_user_ids[]" value="{{ $u->id }}"
                                       class="rounded border-purple-300 text-pink-500 focus:ring-pink-300">
                                <span class="text-sm text-purple-700">{{ $u->name }}</span>
                                <span class="ml-auto text-xs text-purple-400">{{ $u->pivot?->role ?? '' }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="require_all" value="1" class="rounded border-purple-300 text-pink-500 focus:ring-pink-300">
                    <span class="text-sm text-purple-700">Butuh <strong>semua</strong> approver setuju (kalau tidak dicentang, cukup 1 yang approve)</span>
                </label>

                <button type="submit" class="btn-unicorn">+ {{ __('Buat Rule') }}</button>
            </form>
        </details>

        @if($rules->isEmpty())
            <div class="card-unicorn !p-12 text-center">
                <p class="text-5xl mb-3">🛡️</p>
                <p class="font-display font-bold text-lg text-purple-700">{{ __('Belum ada approval rule') }}</p>
                <p class="text-sm text-purple-400 mt-1">Tambah rule untuk control siapa boleh approve invoice/quote/expense.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($rules as $r)
                    <div class="card-unicorn !p-5 flex items-start gap-4">
                        <div class="shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center text-2xl"
                             style="background: linear-gradient(135deg, #FFE4F1, #EDE9FE);">
                            {{ ['invoice'=>'📄','quote'=>'📋','expense'=>'🧾','credit_note'=>'↩️'][$r->applies_to] ?? '📋' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-display font-bold text-purple-900">{{ $r->name }}</p>
                                @if($r->is_active)
                                    <span class="badge-mint text-[10px]">Active</span>
                                @else
                                    <span class="badge-gray text-[10px]">Disabled</span>
                                @endif
                            </div>
                            <p class="text-sm text-purple-600 mt-1">
                                {{ ucfirst($r->applies_to) }}
                                @if($r->condition_type === 'amount_gte')
                                    ≥ <strong>{{ number_format($r->condition_value, 0) }}</strong>
                                @endif
                                · {{ count($r->approver_user_ids ?? []) }} approver
                                · {{ $r->require_all ? 'butuh semua' : 'cukup 1' }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('approval-rules.destroy', $r) }}" onsubmit="return confirm('Hapus rule?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:underline text-sm font-semibold">Hapus</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
