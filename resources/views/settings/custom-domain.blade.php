<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Custom Domain') }} <span class="text-2xl">🌐</span></h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl bg-rose-50 border-2 border-rose-200 p-4 text-rose-800">⚠️ {{ session('error') }}</div>
        @endif

        <div class="card-unicorn !p-6 bg-gradient-to-br from-pink-50/40 to-purple-50/40">
            <p class="font-display font-bold text-purple-900 mb-2">💎 White-Label Premium</p>
            <p class="text-sm text-purple-600">
                Klien akses invoice via domain kamu sendiri (mis. <code class="bg-white px-2 py-0.5 rounded">invoice.brandkamu.com</code>) — bukan invocely.app. Lebih profesional, lebih trust, branding kuat.
            </p>
        </div>

        @if(! $domain)
            <form method="POST" action="{{ route('custom-domain.store') }}" class="card-unicorn !p-8 space-y-5">
                @csrf
                <div>
                    <x-input-label :value="__('Hostname *')" />
                    <x-text-input name="hostname" :value="old('hostname')" required class="mt-1 w-full font-mono" placeholder="invoice.brandkamu.com" />
                    <p class="text-xs text-purple-400 mt-1">Subdomain yang akan dipakai. Contoh: <code>billing.acme.com</code></p>
                    @error('hostname') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-input-label :value="__('Metode Verifikasi *')" />
                    <select name="verification_method" required class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                        <option value="cname">CNAME (recommended)</option>
                        <option value="txt">TXT Record</option>
                    </select>
                </div>
                <button type="submit" class="btn-unicorn px-8">+ {{ __('Tambah Domain') }}</button>
            </form>
        @else
            <div class="card-unicorn !p-6">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-purple-400">Hostname</p>
                        <p class="font-display font-bold text-xl text-purple-900 font-mono">{{ $domain->hostname }}</p>
                        <p class="text-sm text-purple-500 mt-2">
                            Status:
                            @if($domain->status === 'verified')
                                <span class="badge-mint">✓ Verified</span>
                                @if($domain->verified_at)
                                    <span class="text-xs text-purple-400">— {{ $domain->verified_at->diffForHumans() }}</span>
                                @endif
                            @elseif($domain->status === 'failed')
                                <span class="badge-red">✗ Failed</span>
                            @else
                                <span class="badge-gold">⏳ Pending</span>
                            @endif
                        </p>
                    </div>
                    <form method="POST" action="{{ route('custom-domain.destroy') }}" onsubmit="return confirm('Hapus custom domain?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-sm text-rose-500 font-semibold hover:underline">🗑️ Hapus</button>
                    </form>
                </div>
            </div>

            @if($domain->status !== 'verified')
                <div class="card-unicorn !p-6">
                    <h3 class="font-display font-bold text-lg text-purple-900 mb-4">📋 Setup DNS</h3>

                    @if($domain->verification_method === 'cname')
                        <p class="text-sm text-purple-600 mb-3">Tambah record berikut di DNS provider kamu (Cloudflare, Namecheap, dll):</p>
                        <div class="bg-purple-50 border-2 border-purple-200 rounded-xl p-4 font-mono text-sm space-y-1">
                            <div><span class="text-purple-400 inline-block w-20">Type:</span> <strong>CNAME</strong></div>
                            <div><span class="text-purple-400 inline-block w-20">Host:</span> <strong>{{ $domain->hostname }}</strong></div>
                            <div><span class="text-purple-400 inline-block w-20">Target:</span> <strong>cname.invocely.app</strong></div>
                            <div><span class="text-purple-400 inline-block w-20">TTL:</span> 3600 (or default)</div>
                        </div>
                    @else
                        <p class="text-sm text-purple-600 mb-3">Tambah TXT record berikut:</p>
                        <div class="bg-purple-50 border-2 border-purple-200 rounded-xl p-4 font-mono text-sm space-y-1">
                            <div><span class="text-purple-400 inline-block w-20">Type:</span> <strong>TXT</strong></div>
                            <div><span class="text-purple-400 inline-block w-20">Host:</span> <strong>_invocely-verify.{{ $domain->hostname }}</strong></div>
                            <div><span class="text-purple-400 inline-block w-20">Value:</span> <strong>{{ $domain->verification_token }}</strong></div>
                        </div>
                    @endif

                    <p class="text-xs text-amber-700 bg-amber-50 p-3 rounded-xl mt-4">⚠️ DNS propagation bisa 5 menit - 24 jam. Kalau gagal verify pertama kali, tunggu lalu coba lagi.</p>

                    <form method="POST" action="{{ route('custom-domain.verify') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="btn-unicorn">🔍 {{ __('Verify Sekarang') }}</button>
                    </form>

                    @if($domain->last_check_at)
                        <p class="text-xs text-purple-400 mt-3">Last check: {{ $domain->last_check_at->diffForHumans() }}</p>
                    @endif
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
