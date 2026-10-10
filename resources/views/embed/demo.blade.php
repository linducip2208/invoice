<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Embed Widget Demo') }} <span class="text-2xl">🔌</span></h2>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="card-unicorn !p-6">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-3">📦 {{ __('Embed via JS Snippet') }}</h3>
            <p class="text-sm text-purple-600 mb-3">Tempel script ini di website kamu untuk show invoice as iframe widget.</p>
            <pre class="bg-gray-900 text-emerald-400 p-4 rounded-xl overflow-x-auto text-xs"><code>&lt;script src="{{ url('/widget/invocely.js') }}"
        data-token="YOUR_INVOICE_PUBLIC_TOKEN"
        data-height="600px"
        data-width="100%"
&gt;&lt;/script&gt;</code></pre>
        </div>

        <div class="card-unicorn !p-6">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-3">🪟 {{ __('Embed via iFrame Direct') }}</h3>
            <pre class="bg-gray-900 text-emerald-400 p-4 rounded-xl overflow-x-auto text-xs"><code>&lt;iframe src="{{ url('/embed/invoice/') }}/YOUR_INVOICE_PUBLIC_TOKEN"
        width="100%"
        height="600"
        frameborder="0"
        style="border-radius: 16px;"&gt;&lt;/iframe&gt;</code></pre>
        </div>

        <div class="card-unicorn !p-6">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-3">💡 {{ __('Use Cases') }}</h3>
            <ul class="text-sm text-purple-700 space-y-2">
                <li>✦ Embed invoice di halaman thank-you e-commerce kamu</li>
                <li>✦ Tampilin di portal klien custom kamu</li>
                <li>✦ Integrasi dengan CMS (WordPress, Squarespace, Webflow) via custom HTML</li>
                <li>✦ Preview invoice di email yang support iframe (terbatas)</li>
            </ul>
        </div>

        <div class="card-unicorn !p-6 bg-amber-50/60 border-amber-200">
            <p class="text-xs text-amber-800">
                🔐 <strong>Security:</strong> public_token unik per invoice. Sebar token cuma ke pihak yang berhak. Untuk control lebih ketat, pakai workspace setting <code>widget_allowed_domains</code> untuk whitelist origin (advanced — phase 2).
            </p>
        </div>
    </div>
</x-app-layout>
