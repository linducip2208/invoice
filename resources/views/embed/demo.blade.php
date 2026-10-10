<x-tabler.layout :title="__('Embed Widget Demo')" :pretitle="__('Integrations')">
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><x-ti name="code" /> {{ __('Embed via JS Snippet') }}</h3></div>
                <div class="card-body">
                    <p class="text-muted">{{ __('Paste this script on your website to show an invoice as an iframe widget.') }}</p>
                    <pre class="bg-dark text-green p-3 rounded overflow-auto"><code>&lt;script src="{{ url('/widget/invocely.js') }}"
        data-token="YOUR_INVOICE_PUBLIC_TOKEN"
        data-height="600px"
        data-width="100%"
&gt;&lt;/script&gt;</code></pre>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><x-ti name="frame" /> {{ __('Embed via iFrame Direct') }}</h3></div>
                <div class="card-body">
                    <pre class="bg-dark text-green p-3 rounded overflow-auto"><code>&lt;iframe src="{{ url('/embed/invoice/') }}/YOUR_INVOICE_PUBLIC_TOKEN"
        width="100%"
        height="600"
        frameborder="0"
        style="border-radius: 16px;"&gt;&lt;/iframe&gt;</code></pre>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><x-ti name="bulb" /> {{ __('Use Cases') }}</h3></div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex gap-2 mb-2"><span class="text-primary"><x-ti name="check" /></span><span>{{ __('Embed an invoice on your e-commerce thank-you page') }}</span></li>
                        <li class="d-flex gap-2 mb-2"><span class="text-primary"><x-ti name="check" /></span><span>{{ __('Show it in your custom client portal') }}</span></li>
                        <li class="d-flex gap-2 mb-2"><span class="text-primary"><x-ti name="check" /></span><span>{{ __('Integrate with a CMS (WordPress, Squarespace, Webflow) via custom HTML') }}</span></li>
                        <li class="d-flex gap-2"><span class="text-primary"><x-ti name="check" /></span><span>{{ __('Preview an invoice in email clients with iframe support (limited)') }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="alert alert-warning mb-0">
                <div class="d-flex">
                    <div><x-ti name="lock" /></div>
                    <div><strong>{{ __('Security:') }}</strong> {{ __('The public token is unique per invoice. Share tokens only with authorized parties.') }}</div>
                </div>
            </div>
        </div>
    </div>
</x-tabler.layout>
