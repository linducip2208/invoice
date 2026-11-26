<p>Halo {{ $name }},</p>
<p>Pembayaran terakhir untuk langganan <strong>{{ $subscription->plan?->name }}</strong>
({{ $subscription->currency }} {{ number_format($subscription->plan?->priceFor($subscription->currency, $subscription->billing_period) ?? 0, 0) }})
<strong>belum berhasil</strong> (status: {{ $subscription->status }}).</p>
<p>Segera perbarui pembayaran agar layanan tidak terhenti:</p>
<p><a href="{{ $billingUrl }}">{{ $billingUrl }}</a></p>
<p>Terima kasih,<br>{{ config('app.name') }}</p>
