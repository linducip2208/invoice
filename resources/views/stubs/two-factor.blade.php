<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">🔐 {{ __('Two-Factor Authentication (2FA)') }}</h2></x-slot>
    <div class="max-w-2xl mx-auto card-unicorn">
        <p class="text-sm text-purple-600 mb-4">{{ __('TOTP-based 2FA pakai Google Authenticator / Authy / Duo / 1Password.') }}</p>

        <div class="my-6 text-center">
            <div class="inline-block p-4 bg-white rounded-2xl border-2 border-purple-200">
                <div class="w-48 h-48 bg-gradient-to-br from-purple-100 to-pink-100 rounded-xl flex items-center justify-center text-purple-400">
                    <span class="text-sm">QR Code akan generate<br>setelah enable</span>
                </div>
            </div>
        </div>

        <div><label class="text-sm font-bold">{{ __('Enter 6-digit code dari authenticator app') }}</label>
            <input type="text" maxlength="6" placeholder="000000" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full text-center font-mono text-2xl tracking-widest">
        </div>

        <button type="button" disabled class="btn-unicorn opacity-50 mt-4 w-full">🔐 {{ __('Enable 2FA (scaffold)') }}</button>

        <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
            ⏳ {{ __('Backend TOTP secret generation + verification belum di-wire. Recovery codes akan di-generate setelah enable.') }}
        </div>
    </div>
</x-app-layout>
