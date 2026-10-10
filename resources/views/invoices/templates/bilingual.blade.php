<?php
    // Locale labels lookup
    $labels = [
        'id' => ['invoice'=>'Faktur','bill_to'=>'Ditagihkan kepada','from'=>'Dari','issue_date'=>'Tanggal Terbit','due_date'=>'Jatuh Tempo','currency'=>'Mata Uang','status'=>'Status','description'=>'Deskripsi','qty'=>'Qty','unit_price'=>'Harga','tax'=>'Pajak','total'=>'Total','subtotal'=>'Subtotal','discount'=>'Diskon','paid'=>'Dibayar','balance_due'=>'Sisa Tagihan','notes'=>'Catatan','terms'=>'Syarat & Ketentuan'],
        'en' => ['invoice'=>'Invoice','bill_to'=>'Bill To','from'=>'From','issue_date'=>'Issue Date','due_date'=>'Due Date','currency'=>'Currency','status'=>'Status','description'=>'Description','qty'=>'Qty','unit_price'=>'Unit Price','tax'=>'Tax','total'=>'Total','subtotal'=>'Subtotal','discount'=>'Discount','paid'=>'Paid','balance_due'=>'Balance Due','notes'=>'Notes','terms'=>'Terms & Conditions'],
        'zh' => ['invoice'=>'发票','bill_to'=>'账单接收方','from'=>'发件人','issue_date'=>'开票日期','due_date'=>'到期日','currency'=>'货币','status'=>'状态','description'=>'描述','qty'=>'数量','unit_price'=>'单价','tax'=>'税','total'=>'总计','subtotal'=>'小计','discount'=>'折扣','paid'=>'已支付','balance_due'=>'应付余额','notes'=>'备注','terms'=>'条款'],
        'ja' => ['invoice'=>'請求書','bill_to'=>'請求先','from'=>'差出人','issue_date'=>'発行日','due_date'=>'支払期日','currency'=>'通貨','status'=>'状態','description'=>'内容','qty'=>'数量','unit_price'=>'単価','tax'=>'税','total'=>'合計','subtotal'=>'小計','discount'=>'割引','paid'=>'支払済','balance_due'=>'残額','notes'=>'備考','terms'=>'条件'],
    ];
    $locale1 = 'id';
    $locale2 = $bilingualLocale ?? 'en';
    $L1 = $labels[$locale1];
    $L2 = $labels[$locale2] ?? $labels['en'];
    $tr = $invoice->translations->where('locale', $locale2)->first()?->fields ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        @page { margin: 30px 30px 50px 30px; }
        body { font-family: 'Helvetica Neue', sans-serif; color: #1f2937; font-size: 11px; line-height: 1.5; margin: 0; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid {{ $primaryColor }}; padding-bottom: 12px; margin-bottom: 18px; }
        .company { font-size: 18px; font-weight: 700; color: {{ $primaryColor }}; margin: 0; }
        .invoice-title { font-size: 22px; font-weight: 800; color: {{ $primaryColor }}; margin: 0; text-transform: uppercase; }
        .bilingual-row { display: flex; gap: 20px; margin-bottom: 16px; }
        .bilingual-col { flex: 1; }
        .lang-badge { display: inline-block; font-size: 8px; padding: 1px 6px; border-radius: 8px; background: #f3e8ff; color: #6d28d9; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
        .info-block h3 { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin: 0 0 4px 0; font-weight: 600; }
        .info-block p { margin: 1px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        thead th { background: {{ $primaryColor }}; color: #fff; padding: 8px; font-size: 9px; text-transform: uppercase; text-align: left; }
        tbody td { padding: 8px; border-bottom: 1px solid #e5e7eb; font-size: 10px; vertical-align: top; }
        .desc-bilingual { display: block; }
        .desc-bilingual .lang { display: inline-block; font-size: 7px; padding: 1px 4px; border-radius: 6px; background: #f3e8ff; color: #6d28d9; margin-right: 4px; }
        .totals { width: 280px; margin-left: auto; }
        .totals table { width: 100%; }
        .totals td { padding: 4px 8px; font-size: 10px; border-bottom: none; }
        .totals .grand-total td { font-size: 12px; font-weight: 800; color: {{ $primaryColor }}; border-top: 2px solid {{ $primaryColor }}; padding-top: 6px; }
        .footer { position: fixed; bottom: -25px; text-align: center; font-size: 8px; color: #9ca3af; left: 0; right: 0; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            @if($logoBase64)<img src="{{ $logoBase64 }}" alt="Logo" style="max-height:40px; max-width:130px; margin-bottom:6px;">@endif
            <h1 class="company">{{ $workspace->name }}</h1>
        </div>
        <div style="text-align:right;">
            <h2 class="invoice-title">{{ $L1['invoice'] }} / {{ $L2['invoice'] }}</h2>
            <p style="font-weight:600; margin:0;">{{ $invoice->invoice_no }}</p>
        </div>
    </div>

    <div class="bilingual-row">
        <div class="bilingual-col info-block">
            <span class="lang-badge">{{ strtoupper($locale1) }}</span>
            <h3>{{ $L1['bill_to'] }}</h3>
            <p style="font-weight:700; font-size:12px;">{{ $client->company_name ?: $client->name }}</p>
            <p>{{ $client->address }}</p>
        </div>
        <div class="bilingual-col info-block">
            <span class="lang-badge">{{ strtoupper($locale2) }}</span>
            <h3>{{ $L2['bill_to'] }}</h3>
            <p style="font-weight:700; font-size:12px;">{{ $client->company_name ?: $client->name }}</p>
            <p>{{ $client->address }}</p>
        </div>
    </div>

    <div class="bilingual-row">
        <div class="bilingual-col info-block">
            <h3>{{ $L1['issue_date'] }} / {{ $L2['issue_date'] }}</h3>
            <p>{{ $invoice->issue_date->format('d M Y') }}</p>
        </div>
        <div class="bilingual-col info-block">
            <h3>{{ $L1['due_date'] }} / {{ $L2['due_date'] }}</h3>
            <p>{{ $invoice->due_date->format('d M Y') }}</p>
        </div>
        <div class="bilingual-col info-block">
            <h3>{{ $L1['currency'] }} / {{ $L2['currency'] }}</h3>
            <p>{{ $invoice->currency }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th>{{ $L1['description'] }} / {{ $L2['description'] }}</th>
                <th style="width:10%;text-align:center;">{{ $L1['qty'] }}</th>
                <th style="width:15%;text-align:right;">{{ $L1['unit_price'] }}</th>
                <th style="width:20%;text-align:right;">{{ $L1['total'] }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $item)
                @php $trItems = $tr['items'] ?? []; @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <span class="desc-bilingual"><span class="lang">{{ strtoupper($locale1) }}</span>{{ $item->description }}</span>
                        @if(! empty($trItems[$i]))
                            <span class="desc-bilingual" style="margin-top:3px; color:#6b7280;"><span class="lang">{{ strtoupper($locale2) }}</span>{{ $trItems[$i] }}</span>
                        @endif
                    </td>
                    <td style="text-align:center;">{{ number_format($item->quantity, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr><td>{{ $L1['subtotal'] }} / {{ $L2['subtotal'] }}</td><td style="text-align:right;">{{ number_format($invoice->subtotal, 2) }}</td></tr>
            @if($invoice->tax_amount > 0)
                <tr><td>{{ $L1['tax'] }} / {{ $L2['tax'] }}</td><td style="text-align:right;">{{ number_format($invoice->tax_amount, 2) }}</td></tr>
            @endif
            <tr class="grand-total"><td>{{ $L1['total'] }} / {{ $L2['total'] }}</td><td style="text-align:right;">{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td></tr>
        </table>
    </div>

    @if($invoice->notes || $invoice->terms)
        <div style="margin-top:24px; display:flex; gap:24px;">
            @if($invoice->notes)
                <div style="flex:1;">
                    <p style="font-size:9px; text-transform:uppercase; letter-spacing:1px; color:#9ca3af; margin:0 0 4px 0;">{{ $L1['notes'] }} / {{ $L2['notes'] }}</p>
                    <p style="font-size:10px; margin:0;"><span class="lang-badge">{{ strtoupper($locale1) }}</span>{{ $invoice->notes }}</p>
                    @if(! empty($tr['notes']))
                        <p style="font-size:10px; margin:4px 0 0 0; color:#6b7280;"><span class="lang-badge">{{ strtoupper($locale2) }}</span>{{ $tr['notes'] }}</p>
                    @endif
                </div>
            @endif
            @if($invoice->terms)
                <div style="flex:1;">
                    <p style="font-size:9px; text-transform:uppercase; letter-spacing:1px; color:#9ca3af; margin:0 0 4px 0;">{{ $L1['terms'] }} / {{ $L2['terms'] }}</p>
                    <p style="font-size:10px; margin:0;"><span class="lang-badge">{{ strtoupper($locale1) }}</span>{{ $invoice->terms }}</p>
                    @if(! empty($tr['terms']))
                        <p style="font-size:10px; margin:4px 0 0 0; color:#6b7280;"><span class="lang-badge">{{ strtoupper($locale2) }}</span>{{ $tr['terms'] }}</p>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <div class="footer">{{ $workspace->name }}</div>
    <x-free-tier-watermark :invoice="$invoice" />
</body>
</html>
