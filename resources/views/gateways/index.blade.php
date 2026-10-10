<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Payment Gateways') }} <span class="text-2xl">⚡</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Atur gateway pembayaran Indonesia') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto">
        @if(session('success'))
            <div class="mb-6 px-5 py-3 rounded-2xl text-sm font-semibold text-green-800 flex items-center gap-2" style="background: linear-gradient(135deg, #DCFCE7, #BBF7D0); border: 1px solid rgba(134,239,172,0.5);">
                <span class="text-xl">🦄</span> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 px-5 py-3 rounded-2xl text-sm font-semibold text-rose-700 flex items-center gap-2" style="background: linear-gradient(135deg, #FEE2E2, #FECACA); border: 1px solid rgba(248,113,113,0.4);">
                <span class="text-xl">⚠️</span> {{ session('error') }}
            </div>
        @endif

        <!-- Gateway yang sudah terpasang -->
        <div class="card-unicorn mb-6">
            <div class="rainbow-bar -mx-6 -mt-6 mb-5 rounded-t-2xl"></div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display font-bold text-lg text-purple-900">✦ {{ __('Gateway Terpasang') }}</h3>
                <button onclick="document.getElementById('add-gateway-form').classList.toggle('hidden')" class="btn-unicorn text-sm">
                    + {{ __('Tambah Gateway') }}
                </button>
            </div>

            @if($gateways->isEmpty())
                <div class="text-center py-8">
                    <span class="text-5xl block">⚡</span>
                    <p class="mt-3 text-purple-500 font-semibold">{{ __('Belum ada gateway terpasang.') }}</p>
                    <p class="text-xs text-purple-400 mt-1">{{ __('Klik "Tambah Gateway" atau pilih dari preset di bawah.') }}</p>
                </div>
            @else
                <div class="overflow-x-auto rounded-xl">
                    <table class="table-unicorn">
                        <thead>
                            <tr>
                                <th>{{ __('Nama') }}</th>
                                <th>{{ __('Gateway') }}</th>
                                <th>{{ __('Format') }}</th>
                                <th>{{ __('Channel') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-right">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gateways as $gw)
                                <tr>
                                    <td class="font-semibold text-purple-900">{{ $gw->name }}</td>
                                    <td><span class="badge-unicorn">{{ ucfirst($gw->gateway) }}</span></td>
                                    <td><span class="badge-purple">{{ $gw->api_format }}</span></td>
                                    <td class="text-sm text-purple-500">
                                        {{ collect($gw->supported_channels)->take(3)->map(fn($c) => ucwords(str_replace('_',' ',$c)))->join(', ') }}
                                        @if(count($gw->supported_channels ?? []) > 3)
                                            +{{ count($gw->supported_channels) - 3 }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($gw->is_active)
                                            <span class="badge-mint">✦ Aktif</span>
                                        @else
                                            <span class="badge-gray">Nonaktif</span>
                                        @endif
                                        @if($gw->is_sandbox)
                                            <span class="badge-gold text-[10px] ml-1">Sandbox</span>
                                        @endif
                                    </td>
                                    <td class="text-right space-x-1">
                                        <button onclick="toggleEditGateway({{ $gw->id }})" class="text-sm font-semibold text-purple-600 hover:text-pink-500">{{ __('Edit') }}</button>
                                        <form action="{{ route('gateways.toggle', $gw) }}" method="POST" class="inline">
                                            @csrf
                                            <button class="text-sm font-semibold {{ $gw->is_active ? 'text-amber-600 hover:text-amber-700' : 'text-green-600 hover:text-green-700' }}">
                                                {{ $gw->is_active ? __('Nonaktifkan') : __('Aktifkan') }}
                                            </button>
                                        </form>
                                        <form action="{{ route('gateways.destroy', $gw) }}" method="POST" class="inline" onsubmit="return confirm('Hapus gateway ini?')">
                                            @csrf @method('DELETE')
                                            <button class="text-sm font-semibold text-rose-500 hover:text-rose-700">{{ __('Hapus') }}</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit form per gateway (hidden by default) -->
                                <tr id="edit-gateway-{{ $gw->id }}" class="hidden">
                                    <td colspan="6" class="p-4 bg-purple-50/40 rounded-b-xl">
                                        <form action="{{ route('gateways.update', $gw) }}" method="POST" class="grid sm:grid-cols-2 gap-3">
                                            @csrf @method('PATCH')
                                            <div>
                                                <x-input-label :value="__('Nama')" />
                                                <x-text-input class="block mt-1 w-full" type="text" name="name" value="{{ $gw->name }}" required />
                                            </div>
                                            <div>
                                                <x-input-label :value="__('Base URL')" />
                                                <x-text-input class="block mt-1 w-full" type="text" name="base_url" value="{{ $gw->base_url }}" />
                                            </div>
                                            <div>
                                                <x-input-label :value="__('API Format')" />
                                                <select name="api_format" class="block mt-1 w-full input-unicorn rounded-xl px-4 py-2">
                                                    <option value="rest" {{ $gw->api_format === 'rest' ? 'selected' : '' }}>REST API</option>
                                                    <option value="redirect" {{ $gw->api_format === 'redirect' ? 'selected' : '' }}>Redirect</option>
                                                    <option value="qris" {{ $gw->api_format === 'qris' ? 'selected' : '' }}>QRIS</option>
                                                    <option value="ewallets" {{ $gw->api_format === 'ewallets' ? 'selected' : '' }}>E-Wallets</option>
                                                </select>
                                            </div>
                                            <div class="sm:col-span-2 grid sm:grid-cols-3 gap-3">
                                                <div>
                                                    <x-input-label :value="__('API Key (biarkan kosong jika tidak diubah)')" />
                                                    <x-text-input class="block mt-1 w-full" type="password" name="api_key" placeholder="••••••••" />
                                                </div>
                                                <div>
                                                    <x-input-label :value="__('API Secret')" />
                                                    <x-text-input class="block mt-1 w-full" type="password" name="api_secret" placeholder="••••••••" />
                                                </div>
                                                <div>
                                                    <x-input-label :value="__('Merchant ID')" />
                                                    <x-text-input class="block mt-1 w-full" type="password" name="merchant_id" placeholder="••••••••" />
                                                </div>
                                                <div>
                                                    <x-input-label :value="__('Server Key (Midtrans)')" />
                                                    <x-text-input class="block mt-1 w-full" type="password" name="server_key" placeholder="••••••••" />
                                                </div>
                                                <div>
                                                    <x-input-label :value="__('Client Key (Midtrans)')" />
                                                    <x-text-input class="block mt-1 w-full" type="password" name="client_key" placeholder="••••••••" />
                                                </div>
                                                <div class="flex items-end gap-3">
                                                    <label class="flex items-center gap-2 text-sm text-purple-700">
                                                        <input type="checkbox" name="is_active" value="1" {{ $gw->is_active ? 'checked' : '' }} class="rounded border-purple-300 text-purple-600">
                                                        {{ __('Aktif') }}
                                                    </label>
                                                    <label class="flex items-center gap-2 text-sm text-purple-700">
                                                        <input type="checkbox" name="is_sandbox" value="1" {{ $gw->is_sandbox ? 'checked' : '' }} class="rounded border-purple-300 text-purple-600">
                                                        {{ __('Sandbox') }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2 flex justify-end gap-2 mt-1">
                                                <button type="button" onclick="toggleEditGateway({{ $gw->id }})" class="btn-unicorn-secondary text-sm">{{ __('Batal') }}</button>
                                                <button type="submit" class="btn-unicorn text-sm">✦ {{ __('Simpan') }}</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Tambah gateway form -->
        <div id="add-gateway-form" class="card-unicorn mb-6 hidden">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-4">✦ {{ __('Tambah Gateway Baru') }}</h3>
            <form action="{{ route('gateways.store') }}" method="POST" class="grid sm:grid-cols-2 gap-4">
                @csrf
                <div>
                    <x-input-label :value="__('Nama Gateway *')" />
                    <x-text-input class="block mt-1 w-full" type="text" name="name" required placeholder="Contoh: Midtrans Production" />
                </div>
                <div>
                    <x-input-label :value="__('Jenis Gateway *')" />
                    <select name="gateway" class="block mt-1 w-full input-unicorn rounded-xl px-4 py-2" required id="gateway-select">
                        <option value="">{{ __('Pilih...') }}</option>
                        @foreach($presets as $preset)
                            <option value="{{ $preset['gateway'] }}">{{ $preset['name'] }}</option>
                        @endforeach
                        <option value="custom">{{ __('Custom') }}</option>
                    </select>
                </div>
                <div>
                    <x-input-label :value="__('Base URL')" />
                    <x-text-input id="base-url-input" class="block mt-1 w-full" type="text" name="base_url" placeholder="https://api.sandbox.midtrans.com/v2" />
                </div>
                <div>
                    <x-input-label :value="__('API Format')" />
                    <select name="api_format" class="block mt-1 w-full input-unicorn rounded-xl px-4 py-2">
                        <option value="rest">REST API</option>
                        <option value="redirect">Redirect</option>
                        <option value="qris">QRIS</option>
                        <option value="ewallets">E-Wallets</option>
                    </select>
                </div>
                <div class="sm:col-span-2 grid sm:grid-cols-3 gap-3">
                    <div>
                        <x-input-label :value="__('API Key / VA')" />
                        <x-text-input class="block mt-1 w-full" type="password" name="api_key" placeholder="••••••••" />
                    </div>
                    <div>
                        <x-input-label :value="__('API Secret')" />
                        <x-text-input class="block mt-1 w-full" type="password" name="api_secret" placeholder="••••••••" />
                    </div>
                    <div>
                        <x-input-label :value="__('Merchant ID / Code')" />
                        <x-text-input class="block mt-1 w-full" type="password" name="merchant_id" placeholder="••••••••" />
                    </div>
                    <div>
                        <x-input-label :value="__('Server Key (Midtrans)')" />
                        <x-text-input class="block mt-1 w-full" type="password" name="server_key" placeholder="••••••••" />
                    </div>
                    <div>
                        <x-input-label :value="__('Client Key (Midtrans)')" />
                        <x-text-input class="block mt-1 w-full" type="password" name="client_key" placeholder="••••••••" />
                    </div>
                    <div class="flex items-end gap-3">
                        <label class="flex items-center gap-2 text-sm text-purple-700">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-purple-300 text-purple-600">
                            {{ __('Aktif') }}
                        </label>
                        <label class="flex items-center gap-2 text-sm text-purple-700">
                            <input type="checkbox" name="is_sandbox" value="1" checked class="rounded border-purple-300 text-purple-600">
                            {{ __('Sandbox') }}
                        </label>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <x-input-label :value="__('Channel yang Didukung (centang semua yang tersedia)')" />
                    <div id="channel-checkboxes" class="mt-2 grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-2"></div>
                </div>
                <div class="sm:col-span-2">
                    <div id="preset-help" class="p-3 rounded-xl text-sm" style="background: linear-gradient(135deg, rgba(255,122,198,0.08), rgba(167,139,250,0.1)); border: 1px solid rgba(167,139,250,0.2);"></div>
                </div>
                <div class="sm:col-span-2 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('add-gateway-form').classList.add('hidden')" class="btn-unicorn-secondary text-sm">{{ __('Batal') }}</button>
                    <button type="submit" class="btn-unicorn text-sm">✦ {{ __('Tambah Gateway') }}</button>
                </div>
            </form>
        </div>

        <!-- Preset templates -->
        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-4">📦 {{ __('Preset Gateway Indonesia') }}</h3>
            <p class="text-sm text-purple-500 mb-5">{{ __('Klik preset untuk auto-fill form, lalu masukkan credential kamu sendiri.') }}</p>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($presets as $preset)
                    <div class="rounded-2xl p-4 cursor-pointer transition-all hover:-translate-y-1 border-2 border-purple-100 hover:border-pink-300 hover:shadow-unicorn-pink"
                         style="background: rgba(255,255,255,0.7);"
                         onclick="fillPreset({{ json_encode($preset) }})">
                        <p class="font-display font-bold text-purple-900">{{ $preset['name'] }}</p>
                        <p class="text-xs text-purple-500 mt-1 line-clamp-3">{{ $preset['description'] }}</p>
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach(array_slice($preset['supported_channels'] ?? [], 0, 4) as $ch)
                                <span class="badge-purple text-[10px] px-2 py-0.5">{{ ucwords(str_replace('_',' ',$ch)) }}</span>
                            @endforeach
                            @if(count($preset['supported_channels'] ?? []) > 4)
                                <span class="text-[10px] text-purple-400">+{{ count($preset['supported_channels']) - 4 }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        const presets = @json($presets);
        const channels = @json($presets[0] ? collect($presets)->pluck('supported_channels')->flatten()->unique()->values()->toArray() : []);

        function toggleEditGateway(id) {
            document.getElementById('edit-gateway-' + id).classList.toggle('hidden');
        }

        function fillPreset(preset) {
            document.getElementById('add-gateway-form').classList.remove('hidden');
            document.querySelector('[name="name"]').value = preset.name;
            document.querySelector('#gateway-select').value = preset.gateway;
            document.querySelector('#base-url-input').value = preset.default_base_url || '';
            document.querySelector('[name="api_format"]').value = preset.api_format || 'rest';

            // Render channel checkboxes
            const container = document.getElementById('channel-checkboxes');
            const allChannels = [];
            presets.forEach(p => { if (p.supported_channels) { p.supported_channels.forEach(c => { if (!allChannels.includes(c)) allChannels.push(c); }); } });
            container.innerHTML = allChannels.map(ch => `
                <label class="flex items-center gap-1.5 text-xs text-purple-700">
                    <input type="checkbox" name="supported_channels[]" value="${ch}" ${(preset.supported_channels || []).includes(ch) ? 'checked' : ''} class="rounded border-purple-300 text-purple-600">
                    ${ch.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}
                </label>
            `).join('');

            // Help text
            const helpDiv = document.getElementById('preset-help');
            const requiredKeys = (preset.required_keys || []).map(k => {
                const help = (preset.credentials_help || {})[k] || k;
                return `<strong>${k.replace(/_/g, ' ').toUpperCase()}:</strong> ${help}`;
            }).join('<br>');
            helpDiv.innerHTML = `
                <p class="font-semibold text-purple-800 mb-1">📋 Credential yang dibutuhkan:</p>
                <p class="text-purple-600">${requiredKeys}</p>
                ${preset.website ? `<a href="${preset.website}" target="_blank" class="text-pink-500 text-xs mt-1 inline-block">🌐 ${preset.website}</a>` : ''}
                ${preset.docs ? `<a href="${preset.docs}" target="_blank" class="text-blue-500 text-xs mt-1 inline-block ml-2">📖 Dokumentasi</a>` : ''}
            `;
        }

        // Initialize channels on page load
        document.addEventListener('DOMContentLoaded', () => {
            const allChannels = [];
            presets.forEach(p => { if (p.supported_channels) { p.supported_channels.forEach(c => { if (!allChannels.includes(c)) allChannels.push(c); }); }); });
            document.getElementById('channel-checkboxes').innerHTML = allChannels.map(ch => `
                <label class="flex items-center gap-1.5 text-xs text-purple-700">
                    <input type="checkbox" name="supported_channels[]" value="${ch}" class="rounded border-purple-300 text-purple-600">
                    ${ch.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}
                </label>
            `).join('');
        });

        // Auto-fill base URL when gateway type changes
        document.getElementById('gateway-select').addEventListener('change', function() {
            const preset = presets.find(p => p.gateway === this.value);
            if (preset) fillPreset(preset);
        });
    </script>
</x-app-layout>
