<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>✦ Invoice {{ $invoice->invoice_no }} — {{ $invoice->workspace->name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=quicksand:400,500,600,700|comfortaa:400,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background: #FFF8FE;
            background-image:
                radial-gradient(at 0% 0%, rgba(255,177,216,0.35) 0px, transparent 45%),
                radial-gradient(at 100% 0%, rgba(196,181,253,0.30) 0px, transparent 45%),
                radial-gradient(at 50% 100%, rgba(186,230,253,0.30) 0px, transparent 50%);
            background-attachment: fixed;
            min-height: 100vh;
        }
        .invoice-container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .rainbow-bar {
            height: 5px;
            background: linear-gradient(90deg, #FF7AC6, #A78BFA, #7DD3FC, #86EFAC, #FCD34D);
        }
        @media print {
            body { background: #fff !important; background-image: none !important; }
            .no-print { display: none !important; }
            .invoice-container { max-width: 100%; margin: 0; padding: 0; }
            .invoice-card { box-shadow: none !important; border: none !important; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Top action bar -->
        <div class="bg-white rounded-2xl mb-5 no-print" style="box-shadow: 0 12px 30px -10px rgba(167,139,250,0.3); border: 1px solid rgba(196,181,253,0.4);">
            <div class="rainbow-bar rounded-t-2xl"></div>
            <div class="px-5 py-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white font-bold text-lg shadow-unicorn-pink" style="background: linear-gradient(135deg, #FF7AC6, #A78BFA);">
                        {{ strtoupper(substr($invoice->workspace->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs font-bold text-purple-400 uppercase tracking-wider">{{ __('Dari') }}</p>
                        <p class="font-display font-bold text-purple-900">{{ $invoice->workspace->name }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="window.print()" class="btn-unicorn-secondary">🖨️ {{ __('Cetak') }}</button>
                    <a href="{{ route('invoices.pdf', $invoice) }}" class="btn-unicorn">📥 {{ __('Unduh PDF') }}</a>
                </div>
            </div>

            @php $gateways = \App\Models\PaymentGateway::where('workspace_id', $invoice->workspace_id)->where('is_active',true)->orderBy('sort_order')->get(); @endphp
            @if($invoice->balance_due > 0 && !in_array($invoice->status, ['draft','cancelled','paid']))
                @if($gateways->isNotEmpty())
                <div class="px-5 py-4" style="border-top: 1px solid rgba(196,181,253,0.3); background: linear-gradient(180deg, rgba(255,122,198,0.05), rgba(167,139,250,0.05));">
                    <p class="text-xs font-bold text-purple-400 uppercase tracking-wider mb-3">⚡ {{ __('Bayar Online') }}</p>
                    <form method="POST" action="{{ route('payments.gateway', $invoice) }}" class="flex flex-wrap items-end gap-3">
                        @csrf
                        <div>
                            <x-input-label :value="__('Gateway')" />
                            <select name="gateway_id" class="mt-1 input-unicorn rounded-xl px-3 py-1.5 text-sm" required id="public-gateway">
                                <option value="">{{ __('Pilih Gateway') }}</option>
                                @foreach($gateways as $gw)
                                    <option value="{{ $gw->id }}" data-channels="{{ json_encode($gw->supported_channels) }}">
                                        ⚡ {{ $gw->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label :value="__('Channel')" />
                            <select name="channel" class="mt-1 input-unicorn rounded-xl px-3 py-1.5 text-sm" required id="public-channel">
                                <option value="">{{ __('Pilih Channel') }}</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label :value="__('Jumlah')" />
                            <x-text-input type="number" name="amount" step="0.01" min="1000" max="{{ $invoice->balance_due }}" value="{{ $invoice->balance_due }}" class="mt-1 w-40 text-sm" />
                        </div>
                        <button type="submit" class="btn-unicorn text-sm px-5 py-2">
                            ⚡ {{ __('Bayar Sekarang') }}
                        </button>
                    </form>
                    @if(session('payment_va'))
                        <div class="mt-3 p-3 rounded-xl text-sm" style="background: rgba(220,252,231,0.5); border: 1px solid rgba(134,239,172,0.4);">
                            <span class="font-bold text-green-700">VA: {{ session('payment_va') }}</span>
                            <p class="text-green-600 text-xs mt-1">Silakan bayar melalui bank yang dipilih.</p>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mt-3 p-3 rounded-xl text-sm" style="background: rgba(254,226,226,0.5); border: 1px solid rgba(248,113,113,0.4);">
                            <p class="text-rose-700 text-xs">{{ session('error') }}</p>
                        </div>
                    @endif
                </div>
                @endif
            @endif

            @if($invoice->amount_paid > 0)
                <div class="px-5 py-3" style="border-top: 1px solid rgba(196,181,253,0.3); background: rgba(220,252,231,0.3);">
                    <p class="text-xs font-bold text-green-600 uppercase tracking-wider">
                        ✅ {{ __('Dibayar') }}: {{ number_format($invoice->amount_paid, 2) }} {{ $invoice->currency }}
                        @if($invoice->balance_due > 0)
                            | ⏳ {{ __('Sisa') }}: <span class="text-rose-500">{{ number_format($invoice->balance_due, 2) }}</span>
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <!-- Invoice card -->
        <div class="bg-white rounded-2xl invoice-card overflow-hidden" style="box-shadow: 0 20px 50px -12px rgba(167,139,250,0.3); border: 1px solid rgba(196,181,253,0.3);">
            <div class="rainbow-bar"></div>
            <div class="p-8">
                <div style="border-bottom:2px solid {{ $primaryColor }}; padding-bottom:16px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:flex-start; gap:16px;">
                    <div>
                        <h1 style="font-family:'Comfortaa',sans-serif; font-size:24px; font-weight:700; color:{{ $primaryColor }}; margin:0 0 6px 0;">{{ $invoice->workspace->name }}</h1>
                        <p style="font-size:12px; color:#6b7280; line-height:1.5; margin:0;">
                            {{ $invoice->workspace->address }}@if($invoice->workspace->city), {{ $invoice->workspace->city }}@endif @if($invoice->workspace->state){{ $invoice->workspace->state }}@endif<br>
                            @if($invoice->workspace->phone){{ __('Phone:') }} {{ $invoice->workspace->phone }}<br>@endif
                            @if($invoice->workspace->email){{ __('Email:') }} {{ $invoice->workspace->email }}<br>@endif
                            @if($invoice->workspace->tax_id){{ __('Tax ID:') }} {{ $invoice->workspace->tax_id }}@endif
                        </p>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                        <h2 style="font-family:'Comfortaa',sans-serif; font-size:30px; font-weight:800; color:{{ $primaryColor }}; margin:0; text-transform:uppercase; letter-spacing:2px;">✦ INVOICE</h2>
                        <p style="font-size:15px; color:#374151; font-weight:700; margin:4px 0 0;">{{ $invoice->invoice_no }}</p>
                    </div>
                </div>

                <div style="display:flex; gap:40px; margin-bottom:28px; flex-wrap:wrap;">
                    <div style="flex:1; min-width:200px;">
                        <h3 style="font-size:10px; text-transform:uppercase; letter-spacing:1.5px; color:#A78BFA; font-weight:700; margin:0 0 8px 0;">✦ {{ __('Bill To') }}</h3>
                        <p style="font-weight:700; font-size:14px; margin:0; color:#312E81;">{{ $invoice->client->company_name ?: $invoice->client->name }}</p>
                        @if($invoice->client->company_name)<p style="margin:2px 0; font-size:12px; color:#6b7280;">{{ $invoice->client->name }}</p>@endif
                        <p style="margin:2px 0; font-size:11px; color:#6b7280;">{{ $invoice->client->address }}</p>
                        @if($invoice->client->city || $invoice->client->state)<p style="margin:2px 0; font-size:11px; color:#6b7280;">{{ $invoice->client->city }}@if($invoice->client->city && $invoice->client->state), @endif{{ $invoice->client->state }} @if($invoice->client->postal_code){{ $invoice->client->postal_code }}@endif</p>@endif
                        @if($invoice->client->email)<p style="margin:2px 0; font-size:11px; color:#6b7280;">{{ $invoice->client->email }}</p>@endif
                        @if($invoice->client->tax_id)<p style="margin:2px 0; font-size:11px; color:#6b7280;">Tax ID: {{ $invoice->client->tax_id }}</p>@endif
                    </div>
                    <div style="flex:1; min-width:200px;">
                        <h3 style="font-size:10px; text-transform:uppercase; letter-spacing:1.5px; color:#A78BFA; font-weight:700; margin:0 0 8px 0;">✦ {{ __('Invoice Details') }}</h3>
                        <div style="display:flex; gap:24px; margin-bottom:12px; flex-wrap:wrap;">
                            <div>
                                <p style="font-size:10px; color:#9ca3af; margin:0; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">{{ __('Issue Date') }}</p>
                                <p style="font-size:13px; font-weight:700; margin:2px 0 0; color:#312E81;">{{ $invoice->issue_date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p style="font-size:10px; color:#9ca3af; margin:0; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">{{ __('Due Date') }}</p>
                                <p style="font-size:13px; font-weight:700; margin:2px 0 0; color:#312E81;">{{ $invoice->due_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div style="display:flex; gap:24px; flex-wrap:wrap;">
                            <div>
                                <p style="font-size:10px; color:#9ca3af; margin:0; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">{{ __('Status') }}</p>
                                <p style="font-size:13px; font-weight:700; margin:2px 0 0; color:{{ $primaryColor }}; text-transform:uppercase;">{{ $invoice->status }}</p>
                            </div>
                            <div>
                                <p style="font-size:10px; color:#9ca3af; margin:0; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">{{ __('Currency') }}</p>
                                <p style="font-size:13px; font-weight:700; margin:2px 0 0; color:#312E81;">{{ $invoice->currency }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <table style="width:100%; border-collapse:collapse; margin-bottom:24px;">
                    <thead>
                        <tr style="background:{{ $primaryColor }};">
                            <th style="color:#fff; padding:12px; font-size:11px; text-transform:uppercase; text-align:left; letter-spacing:0.05em;">#</th>
                            <th style="color:#fff; padding:12px; font-size:11px; text-transform:uppercase; text-align:left; letter-spacing:0.05em;">{{ __('Description') }}</th>
                            <th style="color:#fff; padding:12px; font-size:11px; text-transform:uppercase; text-align:center; letter-spacing:0.05em;">{{ __('Qty') }}</th>
                            <th style="color:#fff; padding:12px; font-size:11px; text-transform:uppercase; text-align:right; letter-spacing:0.05em;">{{ __('Unit') }}</th>
                            <th style="color:#fff; padding:12px; font-size:11px; text-transform:uppercase; text-align:right; letter-spacing:0.05em;">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoice->items as $index => $item)
                            <tr style="border-bottom:1px solid #f3e8ff;">
                                <td style="padding:12px; font-size:12px; color:#9ca3af;">{{ $index + 1 }}</td>
                                <td style="padding:12px; font-size:12px; color:#312E81; font-weight:500;">{{ $item->description }}</td>
                                <td style="padding:12px; font-size:12px; text-align:center; color:#4C1D95;">{{ number_format($item->quantity, 2) }}</td>
                                <td style="padding:12px; font-size:12px; text-align:right; color:#4C1D95;">{{ number_format($item->unit_price, 2) }}</td>
                                <td style="padding:12px; font-size:12px; text-align:right; font-weight:700; color:#312E81;">{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="padding:32px; text-align:center; color:#9ca3af;">{{ __('No items') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div style="width:340px; margin-left:auto; max-width:100%;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td style="padding:6px 10px; font-size:13px; text-align:right; color:#6b7280;">{{ __('Subtotal') }}</td>
                            <td style="padding:6px 10px; font-size:13px; font-weight:600; text-align:right; color:#312E81;">{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        @if($invoice->tax_amount > 0)
                        <tr>
                            <td style="padding:6px 10px; font-size:13px; text-align:right; color:#6b7280;">{{ __('Tax') }} ({{ number_format($invoice->tax_rate, 2) }}%)</td>
                            <td style="padding:6px 10px; font-size:13px; font-weight:600; text-align:right; color:#312E81;">{{ number_format($invoice->tax_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($invoice->discount_amount > 0)
                        <tr>
                            <td style="padding:6px 10px; font-size:13px; text-align:right; color:#6b7280;">{{ __('Discount') }}</td>
                            <td style="padding:6px 10px; font-size:13px; font-weight:600; text-align:right; color:#F43F5E;">-{{ number_format($invoice->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr style="border-top:2px solid {{ $primaryColor }};">
                            <td style="padding:10px; font-size:15px; font-weight:800; color:{{ $primaryColor }}; text-align:right;">✦ {{ __('Total') }}</td>
                            <td style="padding:10px; font-size:15px; font-weight:800; color:{{ $primaryColor }}; text-align:right;">{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td>
                        </tr>
                        @if($invoice->amount_paid > 0)
                        <tr>
                            <td style="padding:6px 10px; font-size:13px; text-align:right; color:#22C55E;">{{ __('Paid') }}</td>
                            <td style="padding:6px 10px; font-size:13px; font-weight:600; text-align:right; color:#22C55E;">-{{ number_format($invoice->amount_paid, 2) }}</td>
                        </tr>
                        <tr style="border-top:2px solid {{ $primaryColor }};">
                            <td style="padding:10px; font-size:15px; font-weight:800; color:{{ $primaryColor }}; text-align:right;">{{ __('Balance Due') }}</td>
                            <td style="padding:10px; font-size:15px; font-weight:800; color:{{ $primaryColor }}; text-align:right;">{{ number_format($invoice->balance_due, 2) }}</td>
                        </tr>
                        @endif
                    </table>
                </div>

                @if($invoice->notes || $invoice->terms)
                <div style="margin-top:32px; display:flex; gap:32px; flex-wrap:wrap;">
                    @if($invoice->notes)
                    <div style="flex:1; min-width:200px; background:linear-gradient(135deg, #FFE4F1, #EDE9FE); border-radius:14px; padding:14px;">
                        <h4 style="font-size:10px; text-transform:uppercase; letter-spacing:1px; color:#7C3AED; font-weight:700; margin:0 0 6px 0;">✦ {{ __('Notes') }}</h4>
                        <p style="font-size:12px; color:#4C1D95; margin:0; line-height:1.5;">{{ $invoice->notes }}</p>
                    </div>
                    @endif
                    @if($invoice->terms)
                    <div style="flex:1; min-width:200px; background:linear-gradient(135deg, #E0F2FE, #EDE9FE); border-radius:14px; padding:14px;">
                        <h4 style="font-size:10px; text-transform:uppercase; letter-spacing:1px; color:#0369A1; font-weight:700; margin:0 0 6px 0;">✦ {{ __('Terms') }}</h4>
                        <p style="font-size:12px; color:#0C4A6E; margin:0; line-height:1.5;">{{ $invoice->terms }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        @unless($invoice->workspace?->planFeature('remove_watermark', false))
        <div class="text-center mt-6 no-print">
            <p class="text-sm font-semibold text-purple-400">✦ Powered by <a href="/" class="text-rainbow font-bold">Invocely</a> · 🦄 Magical invoicing ✦</p>
        </div>
        @endunless
    </div>

    <script>
        const gatewayData = @json($gateways->map(fn($g) => ['id' => $g->id, 'channels' => $g->supported_channels ?? []]));

        const channelLabels = {
            bank_transfer:'🏦 Transfer Bank', bca:'🏦 BCA VA', bni:'🏦 BNI VA', bri:'🏦 BRI VA',
            mandiri:'🏦 Mandiri VA', permata:'🏦 Permata VA', cimb:'🏦 CIMB Niaga VA',
            credit_card:'💳 Kartu Kredit', gopay:'🔵 GoPay', ovo:'🟣 OVO',
            dana:'🔷 DANA', shopeepay:'🟠 ShopeePay', linkaja:'🔴 LinkAja',
            qris:'📱 QRIS', va:'🏦 Virtual Account', convenience_store:'🏪 Alfamart/Indomaret',
            other:'✦ Lainnya'
        };

        document.getElementById('public-gateway').addEventListener('change', function() {
            const gwId = parseInt(this.value);
            const gw = gatewayData.find(g => g.id === gwId);
            const channelSelect = document.getElementById('public-channel');
            channelSelect.innerHTML = '<option value="">{{ __('Pilih Channel') }}</option>';
            if (gw && gw.channels) {
                gw.channels.forEach(ch => {
                    const label = channelLabels[ch] || ch.toUpperCase();
                    channelSelect.innerHTML += `<option value="${ch}">${label}</option>`;
                });
            }
        });
    </script>
</body>
</html>
