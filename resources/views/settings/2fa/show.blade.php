<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Two-Factor Authentication') }} <span class="text-2xl">🔐</span></h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl bg-rose-50 border-2 border-rose-200 p-4 text-rose-800">⚠️ {{ session('error') }}</div>
        @endif

        @if(session('recovery_codes'))
            <div class="card-unicorn-glow !p-6 bg-gradient-to-br from-amber-50 to-yellow-50">
                <h3 class="font-display font-bold text-lg text-amber-900 mb-3">🆘 Recovery Codes Baru</h3>
                <p class="text-sm text-amber-800 mb-3">Simpan kode-kode ini di tempat aman. Setiap kode hanya bisa dipakai sekali sebagai pengganti kode authenticator kalau HP hilang.</p>
                <div class="grid grid-cols-2 gap-2 font-mono text-sm">
                    @foreach(session('recovery_codes') as $code)
                        <div class="bg-white px-3 py-2 rounded-lg border border-amber-300 text-amber-900 font-bold">{{ $code }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="card-unicorn !p-8">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl shrink-0"
                     style="background: linear-gradient(135deg, {{ $isEnabled ? '#A7F3D0,#6EE7B7' : '#FFE4F1,#EDE9FE' }});">
                    {{ $isEnabled ? '✓' : '🔓' }}
                </div>
                <div class="flex-1">
                    <h3 class="font-display font-bold text-xl text-purple-900">
                        {{ $isEnabled ? '2FA Aktif' : '2FA Belum Aktif' }}
                    </h3>
                    <p class="text-sm text-purple-600 mt-1">
                        @if($isEnabled)
                            Akun terlindungi. Saat login, kamu akan diminta kode dari authenticator app.
                            @if($tfa?->enabled_at)
                                <br><span class="text-xs text-purple-400">Diaktifkan {{ $tfa->enabled_at->diffForHumans() }}</span>
                            @endif
                        @else
                            Tambah lapisan keamanan ekstra. Butuh app seperti Google Authenticator, Authy, atau 1Password.
                        @endif
                    </p>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-purple-100">
                @if(! $isEnabled)
                    <form method="POST" action="{{ route('2fa.enable') }}">
                        @csrf
                        <button type="submit" class="btn-unicorn">⚡ {{ __('Aktifkan 2FA Sekarang') }}</button>
                    </form>
                @else
                    <div class="space-y-4">
                        <form method="POST" action="{{ route('2fa.recovery-codes') }}" class="flex items-end gap-3">
                            @csrf
                            <div class="flex-1">
                                <x-input-label :value="__('Konfirmasi Password untuk Generate Recovery Codes Baru')" />
                                <x-text-input name="password" type="password" required class="mt-1 w-full" />
                            </div>
                            <button type="submit" class="btn-unicorn-secondary">🔑 Recovery Codes</button>
                        </form>

                        <hr class="border-purple-100">

                        <form method="POST" action="{{ route('2fa.disable') }}" onsubmit="return confirm('Yakin nonaktifkan 2FA? Akun akan kurang terlindungi.')" class="flex items-end gap-3">
                            @csrf
                            <div class="flex-1">
                                <x-input-label :value="__('Konfirmasi Password untuk Disable 2FA')" />
                                <x-text-input name="password" type="password" required class="mt-1 w-full" />
                            </div>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-rose-500 text-white font-semibold hover:bg-rose-600">✗ Disable 2FA</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
