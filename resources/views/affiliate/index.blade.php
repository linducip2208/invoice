<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">🤝 {{ __('Affiliate Program') }}</h2></x-slot>
    <div class="max-w-4xl mx-auto space-y-4">
        <div class="card-unicorn">
            <p class="text-xs text-purple-400 uppercase font-bold">Your Referral Link</p>
            <div class="mt-2 flex gap-2 items-center">
                <input type="text" readonly value="{{ $affiliate->referral_link }}" class="input-unicorn rounded-xl px-3 py-2 flex-1 text-sm font-mono" onclick="this.select()">
                <button onclick="navigator.clipboard.writeText('{{ $affiliate->referral_link }}'); this.textContent='✓ Copied!'" class="btn-unicorn">Copy</button>
            </div>
            <p class="text-xs text-purple-500 mt-2">Komisi: <strong>{{ $affiliate->commission_pct }}%</strong> dari first paid month tiap referral.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="card-unicorn"><p class="text-xs text-purple-400 uppercase font-bold">Clicks</p><p class="text-2xl font-display font-bold">{{ number_format($affiliate->clicks) }}</p></div>
            <div class="card-unicorn"><p class="text-xs text-purple-400 uppercase font-bold">Signups</p><p class="text-2xl font-display font-bold">{{ number_format($affiliate->signups) }}</p></div>
            <div class="card-unicorn"><p class="text-xs text-purple-400 uppercase font-bold">Paid Conversions</p><p class="text-2xl font-display font-bold">{{ number_format($affiliate->paid_conversions) }}</p></div>
            <div class="card-unicorn"><p class="text-xs text-purple-400 uppercase font-bold">Balance</p><p class="text-lg font-display font-bold">Rp{{ number_format($affiliate->balance_idr, 0, ',', '.') }}</p><p class="text-xs">${{ number_format($affiliate->balance_usd, 2) }}</p></div>
        </div>

        <div class="card-unicorn text-center text-sm text-purple-500">
            💰 Payout system coming soon. Saat ini track manual via superadmin.
        </div>
    </div>
</x-app-layout>
