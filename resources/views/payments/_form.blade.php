@props(['invoice'])

@php $activeGateways = \App\Models\PaymentGateway::where('is_active', true)->orderBy('sort_order')->get(); @endphp

<div class="modal modal-blur fade" id="add-payment-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('payments.store', $invoice) }}" data-prevent-double-submit>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><x-ti name="cash" /> {{ __('Catat Pembayaran') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">{{ __('Invoice') }} <span class="fw-semibold">{{ $invoice->invoice_no }}</span></p>

                    <x-tabler.field :label="__('Jumlah')" name="amount" :help="__('Sisa tagihan:') . ' ' . number_format($invoice->balance_due, 2)">
                        <input type="number" step="0.01" min="0.01" max="{{ $invoice->balance_due }}" id="amount" name="amount"
                            required placeholder="0.00" class="form-control @error('amount') is-invalid @enderror">
                    </x-tabler.field>

                    <x-tabler.field :label="__('Tanggal Pembayaran')" name="payment_date" type="date" :value="now()->format('Y-m-d')" attrs="required" />

                    <x-tabler.field :label="__('Metode Pembayaran')" name="payment_method">
                        <select id="payment_method" name="payment_method" required onchange="handleMethodChange(this)"
                            class="form-select @error('payment_method') is-invalid @enderror">
                            <option value="">{{ __('Pilih metode') }}</option>
                            <optgroup label="━━━ Manual ━━━">
                                <option value="cash">Tunai</option>
                                <option value="bank_transfer">Transfer Bank (Manual)</option>
                                <option value="credit_card">Kartu Kredit (Manual)</option>
                                <option value="other">{{ __('Lainnya') }}</option>
                            </optgroup>
                            @if($activeGateways->isNotEmpty())
                                <optgroup label="━━━ Gateway ━━━">
                                    @foreach($activeGateways as $gw)
                                        <option value="gateway" data-gateway-id="{{ $gw->id }}" data-channels="{{ json_encode($gw->supported_channels) }}">
                                            {{ $gw->name }} @if($gw->is_sandbox)(Sandbox)@endif
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                    </x-tabler.field>

                    <div id="gateway-fields" class="d-none">
                        <x-tabler.field :label="__('Gateway')" name="payment_gateway_id">
                            <input type="hidden" name="payment_gateway_id" id="payment_gateway_id">
                            <p class="mt-1 fw-semibold" id="gateway-name-display"></p>
                        </x-tabler.field>
                        <x-tabler.field :label="__('Channel Pembayaran')" name="channel">
                            <select id="channel" name="channel" class="form-select">
                                <option value="">{{ __('Pilih channel') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>

                    <x-tabler.field :label="__('No. Referensi')" name="reference_no">
                        <input type="text" id="reference_no" name="reference_no"
                            class="form-control @error('reference_no') is-invalid @enderror" placeholder="e.g. ID Transaksi">
                    </x-tabler.field>

                    <x-tabler.field :label="__('Catatan')" name="notes">
                        <textarea id="notes" name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="{{ __('Catatan opsional...') }}"></textarea>
                    </x-tabler.field>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-primary"><x-ti name="check" /> {{ __('Catat Pembayaran') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
            gatewayFields.classList.remove('d-none');
            gatewayId.value = id;
            gatewayName.textContent = gw ? gw.name : '';

            // Populate channels
            channelSelect.innerHTML = '<option value="">{{ __("Pilih channel") }}</option>';
            const channels = gw ? (gw.channels || []) : [];
            const channelLabels = {
                bank_transfer:'Transfer Bank', bca:'BCA VA', bni:'BNI VA', bri:'BRI VA',
                mandiri:'Mandiri VA', permata:'Permata VA', cimb:'CIMB Niaga VA',
                credit_card:'Kartu Kredit', gopay:'GoPay', ovo:'OVO',
                dana:'DANA', shopeepay:'ShopeePay', linkaja:'LinkAja',
                qris:'QRIS', va:'Virtual Account', convenience_store:'Alfamart/Indomaret',
                other:'Lainnya'
            };
            channels.forEach(ch => {
                const label = channelLabels[ch] || ch.toUpperCase();
                channelSelect.innerHTML += `<option value="${ch}">${label}</option>`;
            });

            refInput.placeholder = 'Auto-generated oleh gateway';
        } else {
            gatewayFields.classList.add('d-none');
            gatewayId.value = '';
            refInput.placeholder = 'e.g. ID Transaksi';
        }
    }
</script>
