<x-tabler.layout :title="__('Payment Gateways')" :pretitle="__('Workspace')">
    <x-slot name="actions">
        <button class="btn btn-primary" onclick="document.getElementById('add-gateway-form').classList.toggle('d-none')">
            <x-ti name="plus" /> {{ __('Tambah Gateway') }}
        </button>
    </x-slot>

    <p class="text-muted small mb-3">{{ __('Atur gateway pembayaran Indonesia') }}</p>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title"><x-ti name="plug" /> {{ __('Gateway Terpasang') }}</h3>
        </div>
        @if($gateways->isEmpty())
            <div class="card-body">
                <x-tabler.empty icon="plug" :title="__('Belum ada gateway terpasang.')" :body="__('Klik &quot;Tambah Gateway&quot; atau pilih dari preset di bawah.')" />
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('Nama') }}</th>
                            <th>{{ __('Gateway') }}</th>
                            <th>{{ __('Format') }}</th>
                            <th>{{ __('Channel') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gateways as $gw)
                            <tr>
                                <td class="fw-semibold">{{ $gw->name }}</td>
                                <td><span class="badge bg-blue-lt">{{ ucfirst($gw->gateway) }}</span></td>
                                <td><span class="badge bg-purple-lt">{{ $gw->api_format }}</span></td>
                                <td class="text-muted small">
                                    {{ collect($gw->supported_channels)->take(3)->map(fn($c) => ucwords(str_replace('_', ' ', $c)))->join(', ') }}
                                    @if(count($gw->supported_channels ?? []) > 3)
                                        +{{ count($gw->supported_channels) - 3 }}
                                    @endif
                                </td>
                                <td>
                                    @if($gw->is_active)
                                        <span class="badge bg-success">{{ __('Aktif') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Nonaktif') }}</span>
                                    @endif
                                    @if($gw->is_sandbox)
                                        <span class="badge bg-warning ms-1">{{ __('Sandbox') }}</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <button onclick="toggleEditGateway({{ $gw->id }})" class="btn btn-sm">{{ __('Edit') }}</button>
                                    <form action="{{ route('gateways.toggle', $gw) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm {{ $gw->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">{{ $gw->is_active ? __('Nonaktifkan') : __('Aktifkan') }}</button>
                                    </form>
                                    <x-tabler.delete-button :action="route('gateways.destroy', $gw)" :confirm="__('Hapus gateway ini?')" :label="__('Hapus')" />
                                </td>
                            </tr>
                            <tr id="edit-gateway-{{ $gw->id }}" class="d-none">
                                <td colspan="6">
                                    <form action="{{ route('gateways.update', $gw) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <x-tabler.field :label="__('Nama')" name="name">
                                                    <input type="text" id="name" name="name" value="{{ $gw->name }}" required class="form-control">
                                                </x-tabler.field>
                                            </div>
                                            <div class="col-md-6">
                                                <x-tabler.field :label="__('Base URL')" name="base_url">
                                                    <input type="text" id="base_url" name="base_url" value="{{ $gw->base_url }}" class="form-control">
                                                </x-tabler.field>
                                            </div>
                                        </div>
                                        <x-tabler.field :label="__('API Format')" name="api_format">
                                            <select id="api_format" name="api_format" class="form-select">
                                                <option value="rest" {{ $gw->api_format === 'rest' ? 'selected' : '' }}>REST API</option>
                                                <option value="redirect" {{ $gw->api_format === 'redirect' ? 'selected' : '' }}>Redirect</option>
                                                <option value="qris" {{ $gw->api_format === 'qris' ? 'selected' : '' }}>QRIS</option>
                                                <option value="ewallets" {{ $gw->api_format === 'ewallets' ? 'selected' : '' }}>E-Wallets</option>
                                            </select>
                                        </x-tabler.field>
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <x-tabler.field :label="__('API Key (biarkan kosong jika tidak diubah)')" name="api_key" type="password" attrs='placeholder="••••••••"' />
                                            </div>
                                            <div class="col-md-4">
                                                <x-tabler.field :label="__('API Secret')" name="api_secret" type="password" attrs='placeholder="••••••••"' />
                                            </div>
                                            <div class="col-md-4">
                                                <x-tabler.field :label="__('Merchant ID')" name="merchant_id" type="password" attrs='placeholder="••••••••"' />
                                            </div>
                                            <div class="col-md-4">
                                                <x-tabler.field :label="__('Server Key (Midtrans)')" name="server_key" type="password" attrs='placeholder="••••••••"' />
                                            </div>
                                            <div class="col-md-4">
                                                <x-tabler.field :label="__('Client Key (Midtrans)')" name="client_key" type="password" attrs='placeholder="••••••••"' />
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-check mt-4">
                                                    <input type="checkbox" name="is_active" value="1" {{ $gw->is_active ? 'checked' : '' }} class="form-check-input">
                                                    <span class="form-check-label">{{ __('Aktif') }}</span>
                                                </label>
                                                <label class="form-check">
                                                    <input type="checkbox" name="is_sandbox" value="1" {{ $gw->is_sandbox ? 'checked' : '' }} class="form-check-input">
                                                    <span class="form-check-label">{{ __('Sandbox') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="btn-list justify-content-end">
                                            <button type="button" onclick="toggleEditGateway({{ $gw->id }})" class="btn">{{ __('Batal') }}</button>
                                            <button type="submit" class="btn btn-primary"><x-ti name="check" /> {{ __('Simpan') }}</button>
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

    <div id="add-gateway-form" class="card mb-3 d-none">
        <div class="card-header">
            <h3 class="card-title"><x-ti name="plus" /> {{ __('Tambah Gateway Baru') }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('gateways.store') }}" method="POST">
                @csrf
                <div class="row g-2">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Nama Gateway *')" name="name" type="text" attrs='required placeholder="Contoh: Midtrans Production"' />
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Jenis Gateway *')" name="gateway">
                            <select id="gateway-select" name="gateway" class="form-select" required>
                                <option value="">{{ __('Pilih...') }}</option>
                                @foreach($presets as $preset)
                                    <option value="{{ $preset['gateway'] }}">{{ $preset['name'] }}</option>
                                @endforeach
                                <option value="custom">{{ __('Custom') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Base URL')" name="base_url" type="text" attrs='id="base-url-input" placeholder="https://api.sandbox.midtrans.com/v2"' />
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('API Format')" name="api_format">
                            <select id="api_format" name="api_format" class="form-select">
                                <option value="rest">REST API</option>
                                <option value="redirect">Redirect</option>
                                <option value="qris">QRIS</option>
                                <option value="ewallets">E-Wallets</option>
                            </select>
                        </x-tabler.field>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <x-tabler.field :label="__('API Key / VA')" name="api_key" type="password" attrs='placeholder="••••••••"' />
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('API Secret')" name="api_secret" type="password" attrs='placeholder="••••••••"' />
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Merchant ID / Code')" name="merchant_id" type="password" attrs='placeholder="••••••••"' />
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Server Key (Midtrans)')" name="server_key" type="password" attrs='placeholder="••••••••"' />
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Client Key (Midtrans)')" name="client_key" type="password" attrs='placeholder="••••••••"' />
                    </div>
                    <div class="col-md-4">
                        <label class="form-check mt-4">
                            <input type="checkbox" name="is_active" value="1" checked class="form-check-input">
                            <span class="form-check-label">{{ __('Aktif') }}</span>
                        </label>
                        <label class="form-check">
                            <input type="checkbox" name="is_sandbox" value="1" checked class="form-check-input">
                            <span class="form-check-label">{{ __('Sandbox') }}</span>
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Channel yang Didukung (centang semua yang tersedia)') }}</label>
                    <div id="channel-checkboxes" class="row g-2"></div>
                </div>
                <div class="mb-3">
                    <div id="preset-help" class="alert alert-info" role="alert"></div>
                </div>
                <div class="btn-list justify-content-end">
                    <button type="button" onclick="document.getElementById('add-gateway-form').classList.add('d-none')" class="btn">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-primary"><x-ti name="plus" /> {{ __('Tambah Gateway') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><x-ti name="package" /> {{ __('Preset Gateway Indonesia') }}</h3>
        </div>
        <div class="card-body">
            <p class="text-muted small">{{ __('Klik preset untuk auto-fill form, lalu masukkan credential kamu sendiri.') }}</p>
            <div class="row row-decks row-cards">
                @foreach($presets as $preset)
                    <div class="col-md-6 col-lg-4">
                        <div class="card cursor-pointer" style="cursor: pointer;" onclick='fillPreset({{ json_encode($preset) }})'>
                            <div class="card-body">
                                <strong>{{ $preset['name'] }}</strong>
                                <div class="text-muted small mt-1">{{ $preset['description'] }}</div>
                                <div class="mt-2 d-flex flex-wrap gap-1">
                                    @foreach(array_slice($preset['supported_channels'] ?? [], 0, 4) as $ch)
                                        <span class="badge bg-blue-lt">{{ ucwords(str_replace('_', ' ', $ch)) }}</span>
                                    @endforeach
                                    @if(count($preset['supported_channels'] ?? []) > 4)
                                        <span class="text-muted small">+{{ count($preset['supported_channels']) - 4 }}</span>
                                    @endif
                                </div>
                            </div>
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
            document.getElementById('edit-gateway-' + id).classList.toggle('d-none');
        }

        function fillPreset(preset) {
            document.getElementById('add-gateway-form').classList.remove('d-none');
            document.querySelector('[name="name"]').value = preset.name;
            document.querySelector('#gateway-select').value = preset.gateway;
            document.querySelector('#base-url-input').value = preset.default_base_url || '';
            document.querySelector('[name="api_format"]').value = preset.api_format || 'rest';

            // Render channel checkboxes
            const container = document.getElementById('channel-checkboxes');
            const allChannels = [];
            presets.forEach(p => { if (p.supported_channels) { p.supported_channels.forEach(c => { if (!allChannels.includes(c)) allChannels.push(c); }); } });
            container.innerHTML = allChannels.map(ch => `
                <label class="form-check col-4 col-md-3">
                    <input type="checkbox" name="supported_channels[]" value="${ch}" ${(preset.supported_channels || []).includes(ch) ? 'checked' : ''} class="form-check-input">
                    <span class="form-check-label small">${ch.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</span>
                </label>
            `).join('');

            // Help text
            const helpDiv = document.getElementById('preset-help');
            const requiredKeys = (preset.required_keys || []).map(k => {
                const help = (preset.credentials_help || {})[k] || k;
                return `<strong>${k.replace(/_/g, ' ').toUpperCase()}:</strong> ${help}`;
            }).join('<br>');
            helpDiv.innerHTML = `
                <p class="fw-semibold mb-1">${preset.name} — credential yang dibutuhkan:</p>
                <p class="mb-1">${requiredKeys}</p>
                ${preset.website ? `<a href="${preset.website}" target="_blank">Website</a>` : ''}
                ${preset.docs ? `<a href="${preset.docs}" target="_blank" class="ms-2">Dokumentasi</a>` : ''}
            `;
        }

        // Initialize channels on page load
        document.addEventListener('DOMContentLoaded', () => {
            const allChannels = [];
            presets.forEach(p => { if (p.supported_channels) { p.supported_channels.forEach(c => { if (!allChannels.includes(c)) allChannels.push(c); }); }); });
            document.getElementById('channel-checkboxes').innerHTML = allChannels.map(ch => `
                <label class="form-check col-4 col-md-3">
                    <input type="checkbox" name="supported_channels[]" value="${ch}" class="form-check-input">
                    <span class="form-check-label small">${ch.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</span>
                </label>
            `).join('');
        });

        // Auto-fill base URL when gateway type changes
        document.getElementById('gateway-select').addEventListener('change', function() {
            const preset = presets.find(p => p.gateway === this.value);
            if (preset) fillPreset(preset);
        });
    </script>
</x-tabler.layout>
