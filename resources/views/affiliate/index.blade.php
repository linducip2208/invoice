<x-tabler.layout :title="__('Affiliate Program')" :pretitle="__('Billing')">
    <div class="card mb-3">
        <div class="card-header"><h3 class="card-title">{{ __('Your Referral Link') }}</h3></div>
        <div class="card-body">
            <div class="input-group">
                <input type="text" readonly value="{{ $affiliate->referral_link }}" class="form-control font-monospace" onclick="this.select()">
                <button type="button" onclick="navigator.clipboard.writeText('{{ $affiliate->referral_link }}'); this.textContent='{{ __('Copied!') }}'" class="btn btn-primary">
                    <x-ti name="copy" /> {{ __('Copy') }}
                </button>
            </div>
            <p class="form-hint mt-2">{{ __('Komisi:') }} <strong>{{ $affiliate->commission_pct }}%</strong> {{ __('dari first paid month tiap referral.') }}</p>
        </div>
    </div>

    <div class="row row-deck row-cards mb-3">
        <x-tabler.stat :label="__('Clicks')" :value="number_format($affiliate->clicks)" icon="mouse" />
        <x-tabler.stat :label="__('Signups')" :value="number_format($affiliate->signups)" icon="user-plus" />
        <x-tabler.stat :label="__('Paid Conversions')" :value="number_format($affiliate->paid_conversions)" icon="check" />
        <x-tabler.stat :label="__('Balance (IDR)')" :value="'Rp' . number_format($affiliate->balance_idr, 0, ',', '.')" icon="wallet" />
    </div>

    <div class="card">
        <div class="card-body text-center text-muted">
            <x-ti name="info-circle" /> {{ __('Payout system coming soon. Saat ini track manual via superadmin.') }}
            <div class="small mt-1">${{ number_format($affiliate->balance_usd, 2) }}</div>
        </div>
    </div>
</x-tabler.layout>
