<x-tabler.public :title="__('Invoice :no', ['no' => $invoice->invoice_no])" :meta-description="__('Invoice :no from :ws', ['no' => $invoice->invoice_no, 'ws' => $invoice->workspace->name])">
    @push('head')
        <meta name="robots" content="noindex, nofollow">
        <style>
            @media print {
                .d-print-none { display: none !important; }
                .card { box-shadow: none !important; }
            }
        </style>
    @endpush

    <x-tabler.alert />

    <div class="card mb-3 d-print-none">
        <div class="card-body">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar">{{ strtoupper(substr($invoice->workspace->name, 0, 1)) }}</span>
                        <div>
                            <div class="subheader">{{ __('From') }}</div>
                            <div class="fw-semibold">{{ $invoice->workspace->name }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="btn-list">
                        <button onclick="window.print()" class="btn"><x-ti name="printer" /> {{ __('Print') }}</button>
                        <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-primary"><x-ti name="download" /> {{ __('Download PDF') }}</a>
                    </div>
                </div>
            </div>
        </div>

        @php $gateways = \App\Models\PaymentGateway::where('workspace_id', $invoice->workspace_id)->where('is_active', true)->orderBy('sort_order')->get(); @endphp
        @if($invoice->balance_due > 0 && !in_array($invoice->status, ['draft', 'cancelled', 'paid']))
            @if($gateways->isNotEmpty())
                <div class="card-body border-top">
                    <h4 class="subheader"><x-ti name="bolt" /> {{ __('Pay Online') }}</h4>
                    <form method="POST" action="{{ route('payments.gateway', $invoice) }}">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <x-tabler.field :label="__('Gateway')" name="gateway_id">
                                    <select name="gateway_id" class="form-select" required id="public-gateway">
                                        <option value="">{{ __('Choose Gateway') }}</option>
                                        @foreach($gateways as $gw)
                                            <option value="{{ $gw->id }}" data-channels="{{ json_encode($gw->supported_channels) }}">{{ $gw->name }}</option>
                                        @endforeach
                                    </select>
                                </x-tabler.field>
                            </div>
                            <div class="col-md-3">
                                <x-tabler.field :label="__('Channel')" name="channel">
                                    <select name="channel" class="form-select" required id="public-channel">
                                        <option value="">{{ __('Choose Channel') }}</option>
                                    </select>
                                </x-tabler.field>
                            </div>
                            <div class="col-md-3">
                                <x-tabler.field :label="__('Amount')" name="amount">
                                    <input type="number" name="amount" step="0.01" min="1000" max="{{ $invoice->balance_due }}" value="{{ $invoice->balance_due }}" class="form-control">
                                </x-tabler.field>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100"><x-ti name="bolt" /> {{ __('Pay Now') }}</button>
                            </div>
                        </div>
                    </form>
                    @if(session('payment_va'))
                        <div class="alert alert-success mt-3 mb-0"><strong>VA: {{ session('payment_va') }}</strong><div class="text-muted">{{ __('Please pay through the selected bank.') }}</div></div>
                    @endif
                </div>
            @endif
        @endif

        @if($invoice->amount_paid > 0)
            <div class="card-body border-top">
                <p class="mb-0"><span class="badge bg-green-lt"><x-ti name="check" /> {{ __('Paid') }}: {{ number_format($invoice->amount_paid, 2) }} {{ $invoice->currency }}</span>
                    @if($invoice->balance_due > 0)
                        <span class="badge bg-red-lt ms-1">{{ __('Balance') }}: {{ number_format($invoice->balance_due, 2) }}</span>
                    @endif
                </p>
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">{{ $invoice->workspace->name }}</h3>
                <div class="text-muted">
                    {{ $invoice->workspace->address }}@if($invoice->workspace->city), {{ $invoice->workspace->city }}@endif @if($invoice->workspace->state){{ $invoice->workspace->state }}@endif<br>
                    @if($invoice->workspace->phone){{ __('Phone:') }} {{ $invoice->workspace->phone }}<br>@endif
                    @if($invoice->workspace->email){{ __('Email:') }} {{ $invoice->workspace->email }}<br>@endif
                    @if($invoice->workspace->tax_id){{ __('Tax ID:') }} {{ $invoice->workspace->tax_id }}@endif
                </div>
            </div>
            <div class="card-actions text-end">
                <div class="subheader">{{ __('Invoice') }}</div>
                <div class="fw-bold">{{ $invoice->invoice_no }}</div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4 mb-3">
                <div class="col-md-6">
                    <h4 class="subheader">{{ __('Bill To') }}</h4>
                    <p class="fw-semibold mb-0">{{ $invoice->client->company_name ?: $invoice->client->name }}</p>
                    @if($invoice->client->company_name)<p class="text-muted mb-0">{{ $invoice->client->name }}</p>@endif
                    <p class="text-muted mb-0">{{ $invoice->client->address }}</p>
                    @if($invoice->client->city || $invoice->client->state)<p class="text-muted mb-0">{{ $invoice->client->city }}@if($invoice->client->city && $invoice->client->state), @endif{{ $invoice->client->state }} @if($invoice->client->postal_code){{ $invoice->client->postal_code }}@endif</p>@endif
                    @if($invoice->client->email)<p class="text-muted mb-0">{{ $invoice->client->email }}</p>@endif
                    @if($invoice->client->tax_id)<p class="text-muted mb-0">Tax ID: {{ $invoice->client->tax_id }}</p>@endif
                </div>
                <div class="col-md-6">
                    <h4 class="subheader">{{ __('Invoice Details') }}</h4>
                    <div class="datagrid">
                        <div class="datagrid-item">
                            <div class="datagrid-title">{{ __('Issue Date') }}</div>
                            <div class="datagrid-content fw-semibold">{{ $invoice->issue_date->format('M d, Y') }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">{{ __('Due Date') }}</div>
                            <div class="datagrid-content fw-semibold">{{ $invoice->due_date->format('M d, Y') }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">{{ __('Status') }}</div>
                            <div class="datagrid-content"><span class="badge bg-blue-lt">{{ strtoupper($invoice->status) }}</span></div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">{{ __('Currency') }}</div>
                            <div class="datagrid-content fw-semibold">{{ $invoice->currency }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Description') }}</th>
                            <th class="text-center">{{ __('Qty') }}</th>
                            <th class="text-end">{{ __('Unit') }}</th>
                            <th class="text-end">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoice->items as $index => $item)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td>{{ $item->description }}</td>
                                <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                                <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end fw-semibold">{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><x-tabler.empty icon="inbox" :title="__('No items')" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end mt-3">
                <div class="col-md-5">
                    <table class="table table-sm mb-0">
                        <tr><td class="text-end text-muted">{{ __('Subtotal') }}</td><td class="text-end fw-semibold">{{ number_format($invoice->subtotal, 2) }}</td></tr>
                        @if($invoice->tax_amount > 0)
                            <tr><td class="text-end text-muted">{{ __('Tax') }} ({{ number_format($invoice->tax_rate, 2) }}%)</td><td class="text-end fw-semibold">{{ number_format($invoice->tax_amount, 2) }}</td></tr>
                        @endif
                        @if($invoice->discount_amount > 0)
                            <tr><td class="text-end text-muted">{{ __('Discount') }}</td><td class="text-end text-danger">-{{ number_format($invoice->discount_amount, 2) }}</td></tr>
                        @endif
                        <tr><td class="text-end fw-bold">{{ __('Total') }}</td><td class="text-end fw-bold">{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td></tr>
                        @if($invoice->amount_paid > 0)
                            <tr><td class="text-end text-success">{{ __('Paid') }}</td><td class="text-end text-success">-{{ number_format($invoice->amount_paid, 2) }}</td></tr>
                            <tr><td class="text-end fw-bold">{{ __('Balance Due') }}</td><td class="text-end fw-bold">{{ number_format($invoice->balance_due, 2) }}</td></tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($invoice->notes || $invoice->terms)
                <div class="row g-3 mt-3">
                    @if($invoice->notes)
                        <div class="col-md-6">
                            <div class="card"><div class="card-body"><h4 class="subheader">{{ __('Notes') }}</h4><p class="mb-0">{{ $invoice->notes }}</p></div></div>
                        </div>
                    @endif
                    @if($invoice->terms)
                        <div class="col-md-6">
                            <div class="card"><div class="card-body"><h4 class="subheader">{{ __('Terms') }}</h4><p class="mb-0">{{ $invoice->terms }}</p></div></div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @unless($invoice->workspace?->planFeature('remove_watermark', false))
        <p class="text-center text-muted mt-3 d-print-none">{{ __('Powered by') }} <a href="/" class="fw-bold">Invocely</a></p>
    @endunless

    @push('scripts')
        <script>
            const gatewayData = @json($gateways->map(fn($g) => ['id' => $g->id, 'channels' => $g->supported_channels ?? []]));
            const channelLabels = {
                bank_transfer: 'Transfer Bank', bca: 'BCA VA', bni: 'BNI VA', bri: 'BRI VA',
                mandiri: 'Mandiri VA', permata: 'Permata VA', cimb: 'CIMB Niaga VA',
                credit_card: 'Kartu Kredit', gopay: 'GoPay', ovo: 'OVO',
                dana: 'DANA', shopeepay: 'ShopeePay', linkaja: 'LinkAja',
                qris: 'QRIS', va: 'Virtual Account', convenience_store: 'Alfamart/Indomaret',
                other: 'Lainnya'
            };
            document.getElementById('public-gateway')?.addEventListener('change', function() {
                const gwId = parseInt(this.value);
                const gw = gatewayData.find(g => g.id === gwId);
                const channelSelect = document.getElementById('public-channel');
                channelSelect.innerHTML = '<option value="">{{ __('Choose Channel') }}</option>';
                if (gw && gw.channels) {
                    gw.channels.forEach(ch => {
                        const label = channelLabels[ch] || ch.toUpperCase();
                        channelSelect.innerHTML += `<option value="${ch}">${label}</option>`;
                    });
                }
            });
        </script>
    @endpush
</x-tabler.public>
