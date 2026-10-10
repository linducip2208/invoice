<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✦ Invoice {{ $invoice->invoice_no }}</title>
</head>
<body style="font-family: 'Quicksand', system-ui, -apple-system, sans-serif; background-color: #FFF8FE; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background: #FFF8FE; background-image: linear-gradient(135deg, #FFE4F1 0%, #EDE9FE 50%, #E0F2FE 100%); padding: 32px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 600px; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 50px -12px rgba(167, 139, 250, 0.35);">
                    <!-- Rainbow ribbon -->
                    <tr>
                        <td style="height: 5px; background: linear-gradient(90deg, #FF7AC6 0%, #A78BFA 25%, #7DD3FC 50%, #86EFAC 75%, #FCD34D 100%);"></td>
                    </tr>

                    <!-- Header -->
                    <tr>
                        <td style="padding: 40px 40px 28px; text-align: center; background: linear-gradient(135deg, rgba(255,228,241,0.5), rgba(237,233,254,0.5));">
                            <p style="font-size: 28px; margin: 0 0 6px;">🦄</p>
                            <h1 style="color: #4C1D95; font-size: 22px; margin: 0; font-weight: 700; letter-spacing: -0.01em;">
                                {{ $workspace->name ?? config('app.name') }}
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 36px 40px;">
                            <p style="font-size: 11px; color: #A78BFA; text-transform: uppercase; letter-spacing: 0.15em; margin: 0 0 4px; font-weight: 700;">✦ Invoice</p>
                            <h2 style="font-size: 24px; font-weight: 700; color: #312E81; margin: 0 0 14px;">{{ $invoice->invoice_no }}</h2>

                            @if ($messageBody)
                                <p style="color: #6D28D9; font-size: 15px; line-height: 1.7; margin: 0 0 24px;">
                                    {{ $messageBody }}
                                </p>
                            @else
                                <p style="color: #6D28D9; font-size: 15px; line-height: 1.7; margin: 0 0 24px;">
                                    Hi ✨ — Berikut invoice kamu terlampir. Terima kasih atas pembayarannya!
                                </p>
                            @endif

                            <!-- Summary -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #FFE4F1 0%, #EDE9FE 50%, #E0F2FE 100%); border-radius: 16px; margin-bottom: 32px;">
                                <tr>
                                    <td style="padding: 18px 24px; border-bottom: 1px solid rgba(196,181,253,0.4);">
                                        <span style="font-size: 11px; color: #7C3AED; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700;">✦ {{ __('Balance Due') }}</span>
                                    </td>
                                    <td style="padding: 18px 24px; text-align: right; border-bottom: 1px solid rgba(196,181,253,0.4);">
                                        <span style="font-size: 22px; font-weight: 700; color: #312E81;">{{ $invoice->currency }} {{ number_format($invoice->balance_due, 2) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 24px; border-bottom: 1px solid rgba(196,181,253,0.3);">
                                        <span style="font-size: 11px; color: #7C3AED; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700;">{{ __('Due Date') }}</span>
                                    </td>
                                    <td style="padding: 14px 24px; text-align: right;">
                                        <span style="font-size: 15px; color: #4C1D95; font-weight: 600;">{{ $invoice->due_date->format('M d, Y') }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 24px;">
                                        <span style="font-size: 11px; color: #7C3AED; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700;">{{ __('Total') }}</span>
                                    </td>
                                    <td style="padding: 14px 24px; text-align: right;">
                                        <span style="font-size: 15px; color: #4C1D95; font-weight: 600;">{{ number_format($invoice->total, 2) }}</span>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $invoice->public_url }}"
                                            style="display: inline-block; background-image: linear-gradient(135deg, #FF7AC6 0%, #A78BFA 50%, #7DD3FC 100%); color: #ffffff; font-size: 15px; font-weight: 700; padding: 14px 36px; border-radius: 999px; text-decoration: none; box-shadow: 0 10px 25px -6px rgba(167, 139, 250, 0.45); letter-spacing: 0.02em;">
                                            ✦ Lihat &amp; Bayar Invoice ✦
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: linear-gradient(180deg, #ffffff 0%, #FFF8FE 100%); padding: 24px 40px; text-align: center; border-top: 1px solid rgba(196,181,253,0.3);">
                            <p style="color: #A78BFA; font-size: 12px; margin: 0; font-weight: 600;">
                                ✦ Dikirim oleh {{ $workspace->name ?? config('app.name') }} ✦
                            </p>
                        </td>
                    </tr>
                </table>

                <p style="color: #C4B5FD; font-size: 11px; margin-top: 16px; font-weight: 600;">🦄 Powered by Invocely</p>
            </td>
        </tr>
    </table>
</body>
</html>
