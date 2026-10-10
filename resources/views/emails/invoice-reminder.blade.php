<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #1f2937; background: #f9fafb; margin: 0; padding: 24px; }
        .container { max-width: 560px; margin: 0 auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(167,139,250,0.15); }
        .header { background: linear-gradient(135deg, #FF7AC6, #A78BFA, #7DD3FC); padding: 24px; color: #fff; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; }
        .body { padding: 32px 28px; line-height: 1.7; font-size: 14px; white-space: pre-line; }
        .summary { background: linear-gradient(135deg, rgba(255,228,241,0.5), rgba(237,233,254,0.5)); border-radius: 12px; padding: 16px 20px; margin: 20px 0; }
        .summary .row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 13px; }
        .summary .row.total { font-weight: 700; font-size: 16px; border-top: 1px solid rgba(167,139,250,0.3); padding-top: 8px; margin-top: 8px; color: #6D28D9; }
        .cta { text-align: center; margin: 24px 0; }
        .cta a { display: inline-block; background: linear-gradient(135deg, #FF7AC6, #A78BFA); color: #fff; padding: 14px 36px; border-radius: 12px; text-decoration: none; font-weight: 700; box-shadow: 0 4px 14px rgba(167,139,250,0.4); }
        .footer { padding: 16px; text-align: center; font-size: 11px; color: #9ca3af; background: #f9fafb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $invoice->workspace->name ?? 'Invocely' }}</h1>
        </div>
        <div class="body">{{ $bodyText }}

            <div class="summary">
                <div class="row"><span>Invoice</span><span>{{ $invoice->invoice_no }}</span></div>
                <div class="row"><span>Tanggal Terbit</span><span>{{ $invoice->issue_date->format('d M Y') }}</span></div>
                <div class="row"><span>Jatuh Tempo</span><span>{{ $invoice->due_date->format('d M Y') }}</span></div>
                <div class="row total"><span>Sisa Tagihan</span><span>{{ $invoice->currency }} {{ number_format($invoice->balance_due ?? $invoice->total, 0) }}</span></div>
            </div>

            <div class="cta">
                <a href="{{ route('public.invoice.show', $invoice->public_token) }}">💳 Lihat & Bayar Invoice</a>
            </div>
        </div>
        <div class="footer">
            Pengingat dari {{ $invoice->workspace->name ?? 'Invocely' }} · Dikirim via Invocely
        </div>
    </div>
</body>
</html>
