<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">🌐 {{ __('Custom Domain') }}</h2></x-slot>
    <div class="max-w-3xl mx-auto card-unicorn">
        <p class="text-sm text-purple-600 mb-4">{{ __('White-label invoice di domain kamu sendiri.') }}</p>
        <form class="space-y-4">
            <div>
                <label class="text-sm font-bold">{{ __('Subdomain (free)') }}</label>
                <div class="flex items-center mt-1">
                    <input type="text" value="{{ workspace()->slug }}" class="input-unicorn rounded-l-xl px-3 py-2 flex-1">
                    <span class="px-3 py-2 bg-purple-100 text-purple-700 rounded-r-xl border-2 border-l-0 border-purple-200 text-sm">.invocely.com</span>
                </div>
            </div>
            <div>
                <label class="text-sm font-bold">{{ __('Custom Domain (CNAME)') }}</label>
                <input type="text" placeholder="invoices.yourcompany.com" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full">
                <p class="text-xs text-purple-500 mt-2">{{ __('Setup CNAME record:') }} <code class="font-mono">invoices.yourcompany.com → invocely.com</code></p>
            </div>
            <button type="button" disabled class="btn-unicorn opacity-50">💾 {{ __('Save (DNS verification belum di-wire)') }}</button>
        </form>
        <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
            ⏳ {{ __('Auto-SSL via Let\'s Encrypt + DNS verification job — coming next release.') }}
        </div>
    </div>
</x-app-layout>
