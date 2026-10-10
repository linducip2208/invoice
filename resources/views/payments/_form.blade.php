@props(['invoice'])

<x-modal name="add-payment-modal" maxWidth="lg">
    <form method="POST" action="{{ route('payments.store', $invoice) }}" class="p-6">
        @csrf

        <div class="flex items-center gap-3 mb-1">
            <span class="text-3xl">💖</span>
            <div>
                <h2 class="font-display text-xl font-bold text-purple-900">{{ __('Catat Pembayaran') }}</h2>
                <p class="text-sm text-purple-500">{{ __('Invoice') }} <span class="font-semibold">{{ $invoice->invoice_no }}</span></p>
            </div>
        </div>

        <div class="mt-5 space-y-4">
            <div>
                <x-input-label for="amount" :value="__('Jumlah')" />
                <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" max="{{ $invoice->balance_due }}" class="mt-1 block w-full" required placeholder="0.00" />
                <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                <p class="mt-1 text-xs text-purple-400">{{ __('Sisa tagihan:') }} <span class="font-semibold text-pink-500">{{ number_format($invoice->balance_due, 2) }}</span></p>
            </div>

            <div>
                <x-input-label for="payment_date" :value="__('Tanggal Pembayaran')" />
                <x-text-input id="payment_date" name="payment_date" type="date" class="mt-1 block w-full" required value="{{ now()->format('Y-m-d') }}" />
                <x-input-error :messages="$errors->get('payment_date')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="payment_method" :value="__('Metode Pembayaran')" />
                <select id="payment_method" name="payment_method" class="mt-1 block w-full input-unicorn rounded-xl px-4 py-2" required onchange="handleMethodChange(this)">
                    <option value="">{{ __('Pilih metode') }}</option>
                    <optgroup label="━━━ Manual ━━━">
                        <option value="cash">💵 Tunai</option>
                        <option value="bank_transfer">🏦 Transfer Bank (Manual)</option>
                        <option value="credit_card">💳 Kartu Kredit (Manual)</option>
                        <option value="other">✦ Lainnya</option>
                    </optgroup>
                    @php $activeGateways = \App\Models\PaymentGateway::where('is_active', true)->orderBy('sort_order')->get(); @endphp
                    @if($activeGateways->isNotEmpty())
                        <optgroup label="━━━ Gateway ━━━">
                            @foreach($activeGateways as $gw)
                                <option value="gateway" data-gateway-id="{{ $gw->id }}" data-channels="{{ json_encode($gw->supported_channels) }}">
                                    ⚡ {{ $gw->name }} @if($gw->is_sandbox)(Sandbox)@endif
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
                <x-input-error :messages="$errors->get('payment_method')" class="mt-1" />
            </div>

            <div id="gateway-fields" class="hidden space-y-3">
                <div>
                    <x-input-label :value="__('Gateway')" />
                    <input type="hidden" name="payment_gateway_id" id="payment_gateway_id">
                    <p class="mt-1 text-sm text-purple-600 font-semibold" id="gateway-name-display"></p>
                </div>
                <div>
                    <x-input-label for="channel" :value="__('Channel Pembayaran')" />
                    <select id="channel" name="channel" class="mt-1 block w-full input-unicorn rounded-xl px-4 py-2">
                        <option value="">{{ __('Pilih channel') }}</option>
                    </select>
                </div>
            </div>

            <div>
                <x-input-label for="reference_no" :value="__('No. Referensi')" />
                <x-text-input id="reference_no" name="reference_no" type="text" class="mt-1 block w-full" placeholder="e.g. ID Transaksi" />
                <x-input-error :messages="$errors->get('reference_no')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="notes" :value="__('Catatan')" />
                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full input-unicorn rounded-xl px-4 py-2" placeholder="{{ __('Catatan opsional...') }}"></textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-1" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <x-secondary-button x-on:click="$dispatch('close-modal', 'add-payment-modal')">
                {{ __('Batal') }}
            </x-secondary-button>
            <x-primary-button>
                ✦ {{ __('Catat Pembayaran') }}
            </x-primary-button>
        </div>
    </form>
</x-modal>

<script>
    const gatewayData = @json($activeGateways->map(fn($g) => ['id' => $g->id, 'name' => $g->name, 'channels' => $g->supported_channels ?? []]));

    function handleMethodChange(select) {
        const option = select.options[select.selectedIndex];
        const gatewayFields = document.getElementById('gateway-fields');
        const gatewayId = document.getElementById('payment_gateway_id');
        const gatewayName = document.getElementById('gateway-name-display');
        const channelSelect = document.getElementById('channel');
        const refInput = document.getElementById('reference_no');

        if (select.value === 'gateway' && option.dataset.gatewayId) {
            const id = parseInt(option.dataset.gatewayId);
            const gw = gatewayData.find(g => g.id === id);
            gatewayFields.classList.remove('hidden');
            gatewayId.value = id;
            gatewayName.textContent = gw ? gw.name : '';

            // Populate channels
            channelSelect.innerHTML = '<option value="">{{ __("Pilih channel") }}</option>';
            const channels = gw ? (gw.channels || []) : [];
            const channelLabels = {
                bank_transfer:'🏦 Transfer Bank', bca:'🏦 BCA VA', bni:'🏦 BNI VA', bri:'🏦 BRI VA',
                mandiri:'🏦 Mandiri VA', permata:'🏦 Permata VA', cimb:'🏦 CIMB Niaga VA',
                credit_card:'💳 Kartu Kredit', gopay:'🔵 GoPay', ovo:'🟣 OVO',
                dana:'🔷 DANA', shopeepay:'🟠 ShopeePay', linkaja:'🔴 LinkAja',
                qris:'📱 QRIS', va:'🏦 Virtual Account', convenience_store:'🏪 Alfamart/Indomaret',
                other:'✦ Lainnya'
            };
            channels.forEach(ch => {
                const label = channelLabels[ch] || ch.toUpperCase();
                channelSelect.innerHTML += `<option value="${ch}">${label}</option>`;
            });

            refInput.placeholder = 'Auto-generated oleh gateway';
        } else {
            gatewayFields.classList.add('hidden');
            gatewayId.value = '';
            refInput.placeholder = 'e.g. ID Transaksi';
        }
    }
</script>
