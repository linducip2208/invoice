<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        body { margin: 0; padding: 16px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #1f2937; background: #ffffff; }
        .embed-card { max-width: 600px; margin: 0 auto; border: 1px solid #e9d5ff; border-radius: 16px; padding: 24px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #f3e8ff; }
        h1 { font-size: 18px; margin: 0; color: #4c1d95; }
        .number { font-size: 14px; color: #7c3aed; font-weight: 600; margin-top: 4px; }
        .meta { font-size: 12px; color: #7c3aed; }
        .total { font-size: 24px; color: #4c1d95; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #faf5ff; padding: 8px; text-align: left; font-size: 11px; color: #6d28d9; text-transform: uppercase; }
        td { padding: 8px; border-bottom: 1px solid #f3e8ff; font-size: 13px; }
        .pay-btn { display: inline-block; background: linear-gradient(135deg, #ec4899, #a78bfa); color: #fff; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: bold; font-size: 14px; }
        .powered { text-align: center; font-size: 10px; color: #c4b5fd; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="embed-card">
        <div class="header">
            <div>
                <h1>{{ $invoice->workspace?->name }}</h1>
                <div class="number">{{ $invoice->invoice_no }}</div>
            </div>
            <div style="text-align:right;">
                <div class="meta">Due {{ $invoice->due_date->format('d M Y') }}</div>
                <div class="total">{{ number_format($invoice->total, 0) }} {{ $invoice->currency }}</div>
                <span style="display:inline-block; padding:2px 10px; border-radius:10px; font-size:10px; background:{{ in_array($invoice->status,['paid'])?'#d1fae5':(in_array($invoice->status,['overdue'])?'#fee2e2':'#fef3c7') }}; color:{{ in_array($invoice->status,['paid'])?'#065f46':(in_array($invoice->status,['overdue'])?'#991b1b':'#92400e') }}; font-weight:600;">{{ strtoupper($invoice->status) }}</span>
            </div>
        </div>

        <p style="font-size:12px; color:#7c3aed; margin:0 0 12px 0;"><strong>{{ $invoice->client?->name }}</strong></p>

        <table>
            <thead>
                <tr><th>Description</th><th style="text-align:right">Qty</th><th style="text-align:right">Total</th></tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td style="text-align:right">{{ number_format($item->quantity, 2) }}</td>
                        <td style="text-align:right">{{ number_format($item->total, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($invoice->status !== 'paid')
            <div style="text-align:center; margin-top:20px;">
                <a href="{{ route('public.invoice.show', $invoice->public_token) }}" target="_top" class="pay-btn">💳 Bayar Sekarang</a>
            </div>
        @endif

        <div class="powered">Powered by <a href="{{ url('/') }}" target="_top" style="color:#a78bfa; text-decoration:none; font-weight:600;">Invocely</a></div>
    </div>
</body>
</html>
