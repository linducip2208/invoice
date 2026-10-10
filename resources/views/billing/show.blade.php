<x-tabler.layout :title="__('Billing & Plan')" :pretitle="__('Billing')" :subtitle="__('Kelola subscription kamu')">
    @php
        $plan = $subscription->plan;
        $features = $plan->features ?? [];
        $isFree = $plan->isFree();
        $cancelling = $subscription->cancel_at_period_end;
    @endphp

    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between flex-wrap gap-3">
                <div>
                    <div class="subheader">{{ __('Plan saat ini') }}</div>
                    <h3 class="mb-1">{{ $plan->name }}</h3>
                    <div class="text-muted">{{ $plan->tagline }}</div>
                </div>
                <div class="text-end">
                    @if($isFree)
                        <span class="badge bg-green-lt">{{ __('Free') }}</span>
                    @else
                        <div class="h2 mb-0">
                            @if($subscription->currency === 'IDR')
                                Rp{{ number_format($plan->priceFor('IDR', $subscription->billing_period === 'yearly' ? 'yearly' : 'monthly'), 0, ',', '.') }}
                            @else
                                ${{ number_format($plan->priceFor('USD', $subscription->billing_period === 'yearly' ? 'yearly' : 'monthly'), 0) }}
                            @endif
                        </div>
                        <div class="text-muted small">/{{ $subscription->billing_period === 'yearly' ? __('tahun') : __('bulan') }}</div>
                    @endif
                </div>
            </div>

            <div class="datagrid mt-4">
                <div class="datagrid-item">
                    <div class="datagrid-title">Status</div>
                    <div class="datagrid-content text-capitalize">{{ $subscription->status }}</div>
                </div>
                <div class="datagrid-item">
                    <div class="datagrid-title">Currency</div>
                    <div class="datagrid-content">{{ $subscription->currency }}</div>
                </div>
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Period') }}</div>
                    <div class="datagrid-content text-capitalize">{{ __($subscription->billing_period) }}</div>
                </div>
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Next billing') }}</div>
                    <div class="datagrid-content">{{ $subscription->current_period_ends_at?->format('d M Y') ?? '—' }}</div>
                </div>
            </div>

            @if($subscription->status === 'pending')
                <div class="alert alert-warning mt-3 d-flex justify-content-between flex-wrap gap-2">
                    <div><x-ti name="hourglass" /> {{ __('Subscription menunggu verifikasi pembayaran manual.') }}</div>
                    <a href="{{ route('billing.manual', $subscription) }}" class="btn btn-sm btn-warning">{{ __('Lihat Instruksi / Upload Bukti') }}</a>
                </div>
            @endif

            @if($cancelling)
                <div class="alert alert-warning mt-3 d-flex justify-content-between flex-wrap gap-2">
                    <div><x-ti name="alert-triangle" /> {{ __('Subscription akan dibatalkan di akhir periode.') }}</div>
                    <form method="POST" action="{{ route('billing.resume') }}">
                        @csrf
                        <button class="btn btn-sm">{{ __('Lanjutkan Subscription') }}</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    @php
        $maxInv = $features['max_invoices_lifetime'] ?? null;
        $maxClients = $features['max_clients'] ?? null;
    @endphp
    <div class="row row-deck row-cards mb-3">
        <x-tabler.stat :label="__('Invoice')" :value="$usage['invoices'] . '/' . ($maxInv ?? '∞')" icon="file-invoice" />
        <x-tabler.stat :label="__('Klien')" :value="$usage['clients'] . '/' . ($maxClients ?? '∞')" icon="users" />
        <x-tabler.stat :label="__('Recurring')" :value="$usage['recurring']" icon="repeat" />
    </div>

    <div class="card mb-3">
        <div class="card-header"><h3 class="card-title">{{ __('Ubah Plan') }}</h3></div>
        <div class="card-body">
            <div x-data="{ period: '{{ $subscription->billing_period === 'yearly' ? 'yearly' : 'monthly' }}', currency: '{{ $subscription->currency }}' }">
                <div class="d-flex gap-2 flex-wrap mb-3">
                    <div class="btn-group" role="group">
                        <button type="button" @click="period = 'monthly'" class="btn btn-sm" :class="period === 'monthly' ? 'btn-primary' : 'btn-outline-primary'">{{ __('Bulanan') }}</button>
                        <button type="button" @click="period = 'yearly'" class="btn btn-sm" :class="period === 'yearly' ? 'btn-primary' : 'btn-outline-primary'">{{ __('Tahunan -17%') }}</button>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" @click="currency = 'IDR'" class="btn btn-sm" :class="currency === 'IDR' ? 'btn-primary' : 'btn-outline-primary'">IDR</button>
                        <button type="button" @click="currency = 'USD'" class="btn btn-sm" :class="currency === 'USD' ? 'btn-primary' : 'btn-outline-primary'">USD</button>
                    </div>
                </div>

                <div class="row g-2">
                    @foreach($plans as $p)
                        <div class="col-md-4">
                            <div class="card {{ $p->id === $plan->id ? 'border-primary' : '' }} h-100">
                                <div class="card-body">
                                    <h4 class="mb-1">{{ $p->name }}</h4>
                                    <div class="text-muted small">{{ $p->tagline }}</div>
                                    <div class="h3 mt-2 mb-0">
                                        <template x-if="currency === 'IDR'">
                                            <span x-text="period === 'monthly' ? '{{ 'Rp' . number_format($p->price_idr_monthly, 0, ',', '.') }}' : '{{ 'Rp' . number_format($p->price_idr_yearly, 0, ',', '.') }}'"></span>
                                        </template>
                                        <template x-if="currency === 'USD'">
                                            <span x-text="period === 'monthly' ? '${{ number_format($p->price_usd_monthly, 0) }}' : '${{ number_format($p->price_usd_yearly, 0) }}'"></span>
                                        </template>
                                    </div>
                                    @if($p->id === $plan->id)
                                        <div class="mt-3 text-center text-muted fw-semibold">{{ __('Plan saat ini') }}</div>
                                    @else
                                        <form method="POST" action="{{ route('billing.subscribe', $p) }}" class="mt-3">
                                            @csrf
                                            <input type="hidden" name="period" :value="period">
                                            <input type="hidden" name="currency" :value="currency">
                                            <button class="btn btn-primary w-100">
                                                {{ $p->isFree() ? __('Downgrade ke Free') : ($p->price_idr_monthly > $plan->price_idr_monthly ? __('Upgrade') : __('Pilih Plan')) }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if(!$isFree && !$cancelling)
        <div class="text-center mb-3">
            <form method="POST" action="{{ route('billing.cancel') }}" onsubmit="return confirm('Yakin batalkan subscription?');">
                @csrf
                <button class="btn btn-link text-danger">{{ __('Batalkan Subscription') }}</button>
            </form>
        </div>
    @endif

    @if($billingHistory->isNotEmpty())
        <div class="card">
            <div class="card-header"><h3 class="card-title">{{ __('Riwayat Billing') }}</h3></div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('Tanggal') }}</th>
                            <th>{{ __('Referensi') }}</th>
                            <th class="text-end">{{ __('Jumlah') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($billingHistory as $bh)
                            <tr>
                                <td class="text-muted">{{ $bh->paid_at?->format('d M Y') ?? $bh->created_at->format('d M Y') }}</td>
                                <td class="fw-semibold">{{ $bh->reference_no }}</td>
                                <td class="text-end fw-semibold">{{ $bh->currency }} {{ number_format($bh->amount, 0) }}</td>
                                <td><x-tabler.status-badge :status="$bh->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-tabler.layout>
