<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="✦ Invocely — Magical invoicing for freelancers, agencies, and dreamy small businesses. Send sparkly invoices, track payments, and run on auto-pilot.">
    <meta name="keywords" content="invoicing, invoice software, freelance invoicing, recurring invoices, invoice templates">
    <meta property="og:title" content="✦ Invocely — Magical Invoicing">
    <meta property="og:description" content="Sparkly invoices, recurring billing, and beautiful templates for the modern business.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="✦ Invocely — Magical Invoicing">
    <link rel="canonical" href="{{ url('/') }}">

    <title>✦ Invocely — Magical Invoicing for Dreamy Businesses</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=quicksand:400,500,600,700|comfortaa:400,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script type="application/ld+json">
    @verbatim
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "Invocely",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "offers": { "@type": "Offer", "price": "0", "priceCurrency": "USD" },
        "aggregateRating": { "@type": "AggregateRating", "ratingValue": "4.9", "reviewCount": "1247" }
    }
    @endverbatim
    </script>
</head>
<body class="font-sans antialiased text-purple-900" x-data="{ mobileMenuOpen: false }">

    <!-- Top nav -->
    <nav class="fixed top-0 left-0 right-0 z-50" style="background: rgba(255, 248, 254, 0.85); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(196,181,253,0.3);">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5">
                <span class="logo-glow flex items-center justify-center" style="padding: 4px;">
                    <x-application-logo size="sm" />
                </span>
                <span class="font-display font-bold text-lg text-rainbow">Invocely</span>
            </a>
            {{-- Desktop nav --}}
            <div class="hidden sm:flex items-center gap-2 sm:gap-4">
                <a href="{{ route('blog.index') }}" class="text-sm font-semibold text-purple-600 hover:text-pink-500">{{ __('Blog') }}</a>
                <a href="{{ route('docs') }}" class="text-sm font-semibold text-purple-600 hover:text-pink-500">{{ __('Docs') }}</a>
                <a href="{{ route('seo.best-tools', ['year' => now()->year]) }}" class="text-sm font-semibold text-purple-600 hover:text-pink-500">{{ __('Best Tools') }}</a>
                <a href="{{ route('seo.templates') }}" class="text-sm font-semibold text-purple-600 hover:text-pink-500">{{ __('Templates') }}</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-unicorn">✦ {{ __('Dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-purple-600 hover:text-pink-500">{{ __('Login') }}</a>
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-unicorn">✦ {{ __('Sign Up') }}</a>
                    @endif
                @endauth
            </div>
            {{-- Mobile hamburger --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="sm:hidden p-2 rounded-lg text-purple-500 hover:text-pink-500 hover:bg-pink-50 transition">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        {{-- Mobile menu dropdown --}}
        <div x-show="mobileMenuOpen" x-cloak x-transition.opacity class="sm:hidden border-t border-purple-100/50 px-4 py-3 space-y-2" style="background: rgba(255, 248, 254, 0.95); backdrop-filter: blur(14px);">
            <a href="{{ route('blog.index') }}" class="block text-sm font-semibold text-purple-600 hover:text-pink-500 py-1.5">{{ __('Blog') }}</a>
            <a href="{{ route('docs') }}" class="block text-sm font-semibold text-purple-600 hover:text-pink-500 py-1.5">{{ __('Docs') }}</a>
            <a href="{{ route('seo.best-tools', ['year' => now()->year]) }}" class="block text-sm font-semibold text-purple-600 hover:text-pink-500 py-1.5">{{ __('Best Tools') }}</a>
            <a href="{{ route('seo.templates') }}" class="block text-sm font-semibold text-purple-600 hover:text-pink-500 py-1.5">{{ __('Templates') }}</a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn-unicorn w-full text-center">✦ {{ __('Dashboard') }}</a>
            @else
                <a href="{{ route('login') }}" class="block text-sm font-semibold text-purple-600 hover:text-pink-500 py-1.5">{{ __('Login') }}</a>
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-unicorn w-full text-center">✦ {{ __('Sign Up') }}</a>
                @endif
            @endauth
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero-aurora relative pt-28 sm:pt-32 pb-20 sm:pb-24 px-4 sm:px-6 overflow-hidden">
        <!-- Sparkles -->
        <div class="absolute inset-0 pointer-events-none">
            <span class="absolute top-[18%] left-[12%] text-3xl animate-unicorn-float">✦</span>
            <span class="absolute top-[28%] right-[15%] text-2xl animate-unicorn-twinkle text-yellow-400">✧</span>
            <span class="absolute bottom-[25%] left-[8%] text-2xl animate-unicorn-float text-purple-400" style="animation-delay:1s;">✦</span>
            <span class="absolute top-[60%] right-[10%] text-3xl animate-unicorn-twinkle text-sky-400">✧</span>
            <span class="absolute top-[40%] left-[45%] text-xl animate-unicorn-float text-pink-400" style="animation-delay:0.5s;">✦</span>
        </div>

        <div class="relative max-w-5xl mx-auto text-center">
            <span class="badge-unicorn inline-flex animate-unicorn-bounce">🦄 {{ __('Now magical for') }} {{ now()->year }}</span>
            <h1 class="mt-6 font-display text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight text-purple-900">
                {{ __('Invoicing that feels like') }}
                <span class="text-rainbow">✦ {{ __('magic') }} ✦</span>
            </h1>
            <p class="mt-6 text-lg text-purple-600 max-w-2xl mx-auto leading-relaxed">
                {{ __('Send sparkly invoices, track payments, and run recurring billing on auto-pilot. Built for freelancers, agencies, and dreamy small businesses.') }}
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn-unicorn px-8 py-4 text-base">
                    ✦ {{ __('Start Free') }}
                </a>
                <a href="{{ route('seo.templates') }}" class="btn-unicorn-secondary px-8 py-4 text-base">
                    📜 {{ __('Browse Templates') }}
                </a>
            </div>
            <div class="mt-8 flex items-center justify-center gap-1 text-sm text-purple-500">
                <span class="text-yellow-400">★★★★★</span>
                <span class="font-semibold ml-2">4.9/5</span>
                <span class="ml-2">{{ __('from 1,247 happy unicorns') }}</span>
            </div>
        </div>

        <!-- Hero illustration (CSS-only invoice mock) -->
        <div class="relative max-w-4xl mx-auto mt-16">
            <div class="card-unicorn-glow text-left animate-unicorn-float">
                <div class="rainbow-bar -mx-6 -mt-6 mb-4 rounded-t-2xl"></div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-bold text-purple-400 uppercase tracking-wider">✦ Invoice</p>
                        <p class="font-display font-bold text-purple-900">INV-2026-0042</p>
                    </div>
                    <span class="badge-mint">PAID</span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-purple-400">Bill To</p>
                        <p class="font-bold text-purple-900">Sparkle Studios</p>
                        <p class="text-purple-500">hello@sparkle.land</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-purple-400">Total</p>
                        <p class="font-display text-2xl font-bold text-rainbow">$2,450.00</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-20 px-4 sm:px-6">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <span class="badge-unicorn">✦ {{ __('Features') }}</span>
                <h2 class="mt-4 font-display text-3xl sm:text-4xl font-bold text-purple-900">{{ __('Everything you need, nothing you don\'t') }}</h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach([
                    ['🦄', __('5 Magical Templates'),    __('Classic, Modern, Minimal, Bold, and Elegant — all customizable with your brand colors.')],
                    ['🔄', __('Recurring Invoices'),     __('Set it and forget it. Bill clients on auto-pilot with daily, weekly, monthly, or yearly schedules.')],
                    ['💖', __('Payment Tracking'),       __('Record partial or full payments with cash, bank transfer, credit card, or any method.')],
                    ['📨', __('Email Magic'),            __('Send invoices straight from the app with a beautiful sparkly template and PDF attached.')],
                    ['🔗', __('Public Sharing'),         __('Each invoice gets a magical public URL. Share with clients — no login required.')],
                    ['📊', __('Smart Reports'),          __('See revenue, outstanding, and overdue at a glance. Top clients and payment trends included.')],
                ] as [$icon, $title, $desc])
                    <div class="card-unicorn group">
                        <div class="text-4xl mb-3 group-hover:animate-unicorn-bounce inline-block">{{ $icon }}</div>
                        <h3 class="font-display font-bold text-lg text-purple-900">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-purple-600 leading-relaxed">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Templates Preview -->
    <section class="py-20 px-4 sm:px-6" style="background: linear-gradient(180deg, transparent 0%, rgba(255,228,241,0.4) 50%, transparent 100%);">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <span class="badge-unicorn">📜 {{ __('Templates') }}</span>
                <h2 class="mt-4 font-display text-3xl sm:text-4xl font-bold text-purple-900">{{ __('Five styles, infinite sparkle') }}</h2>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                @php $tplGradients = ['Classic'=>'from-purple-100 to-blue-100','Modern'=>'from-pink-100 to-purple-100','Minimal'=>'from-gray-100 to-purple-50','Bold'=>'from-pink-200 to-yellow-100','Elegant'=>'from-purple-200 to-pink-100']; @endphp
                @foreach($tplGradients as $name => $grad)
                    <a href="{{ route('seo.templates') }}" class="card-unicorn group text-center">
                        <div class="w-full h-24 rounded-xl bg-gradient-to-br {{ $grad }} mb-3 flex items-center justify-center text-xs uppercase font-bold text-purple-700 group-hover:shadow-unicorn-pink transition">{{ $name }}</div>
                        <p class="font-semibold text-purple-900">{{ $name }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 px-4 sm:px-6">
        <div class="max-w-4xl mx-auto card-unicorn-glow text-center sparkles-bg p-12">
            <span class="text-6xl block animate-unicorn-float">🦄</span>
            <h2 class="mt-5 font-display text-3xl sm:text-4xl font-bold text-purple-900">{{ __('Ready to sparkle?') }}</h2>
            <p class="mt-4 text-purple-600 max-w-xl mx-auto leading-relaxed">
                {{ __('Join the magical realm of invoicing. Free to start, no credit card needed.') }}
            </p>
            <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn-unicorn mt-8 px-10 py-4 text-base">
                ✦ {{ __('Begin Your Magic') }} ✦
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 px-4 sm:px-6 border-t" style="border-color: rgba(196,181,253,0.3); background: rgba(255,255,255,0.4);">
        <div class="max-w-6xl mx-auto">
            <div class="grid sm:grid-cols-4 gap-8">
                <div>
                    <a href="/" class="flex items-center gap-2 mb-3">
                        <span class="logo-glow flex items-center justify-center" style="padding: 4px;"><x-application-logo size="sm" /></span>
                        <span class="font-display font-bold text-lg text-rainbow">Invocely</span>
                    </a>
                    <p class="text-sm text-purple-500">{{ __('Magical invoicing for dreamy businesses ✦') }}</p>
                </div>
                <div>
                    <h4 class="font-display font-bold text-sm text-purple-900 mb-3">{{ __('Product') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('blog.index') }}" class="text-purple-600 hover:text-pink-500">{{ __('Blog') }}</a></li>
                        <li><a href="{{ route('docs') }}" class="text-purple-600 hover:text-pink-500">{{ __('Documentation') }}</a></li>
                        <li><a href="{{ route('seo.templates') }}" class="text-purple-600 hover:text-pink-500">{{ __('Templates') }}</a></li>
                        <li><a href="{{ route('seo.best-tools', ['year' => now()->year]) }}" class="text-purple-600 hover:text-pink-500">{{ __('Best Tools') }} {{ now()->year }}</a></li>
                        <li><a href="{{ route('login') }}" class="text-purple-600 hover:text-pink-500">{{ __('Login') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-display font-bold text-sm text-purple-900 mb-3">{{ __('Compare') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('seo.compare', ['a' => 'invocely', 'b' => 'freshbooks']) }}" class="text-purple-600 hover:text-pink-500">vs FreshBooks</a></li>
                        <li><a href="{{ route('seo.compare', ['a' => 'invocely', 'b' => 'quickbooks']) }}" class="text-purple-600 hover:text-pink-500">vs QuickBooks</a></li>
                        <li><a href="{{ route('seo.compare', ['a' => 'invocely', 'b' => 'xero']) }}" class="text-purple-600 hover:text-pink-500">vs Xero</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-display font-bold text-sm text-purple-900 mb-3">{{ __('Alternatives') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('seo.alternatives', ['slug' => 'freshbooks']) }}" class="text-purple-600 hover:text-pink-500">FreshBooks {{ __('alternatives') }}</a></li>
                        <li><a href="{{ route('seo.alternatives', ['slug' => 'quickbooks']) }}" class="text-purple-600 hover:text-pink-500">QuickBooks {{ __('alternatives') }}</a></li>
                        <li><a href="{{ route('seo.alternatives', ['slug' => 'wave']) }}" class="text-purple-600 hover:text-pink-500">Wave {{ __('alternatives') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="rainbow-bar mt-10 mb-6 rounded-full"></div>
            <p class="text-center text-xs text-purple-400">✦ © {{ now()->year }} Invocely — Sprinkled with magic and built with 🦄</p>
        </div>
    </footer>

    <!-- Source Code Purchase Popup -->
    <div
        x-data="{ showPopup: false }"
        x-init="setTimeout(() => { showPopup = true }, 2500)"
        x-show="showPopup"
        x-cloak
        x-transition:enter="transition ease-out duration-400"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
        style="background: rgba(76, 29, 149, 0.55); backdrop-filter: blur(10px);"
        @click.self="showPopup = false"
    >
        <div
            x-show="showPopup"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-90 translate-y-8"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative w-full max-w-2xl transform"
        >
            <!-- Glow border card -->
            <div class="card-unicorn-grow" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(24px); border-radius: 2rem; padding: 3rem 2.5rem; box-shadow: 0 0 0 1px rgba(255,255,255,0.7) inset, 0 40px 100px -20px rgba(167, 139, 250, 0.6), 0 0 60px -10px rgba(255, 122, 198, 0.4);">

                <!-- Rainbow bar at top -->
                <div class="rainbow-bar absolute top-0 left-0 right-0 rounded-t-[2rem]"></div>

                <!-- Close button -->
                <button
                    @click="showPopup = false"
                    class="absolute top-5 right-5 w-10 h-10 rounded-full flex items-center justify-center transition-all duration-200 hover:rotate-90"
                    style="background: rgba(237,233,254,0.6); border: 1px solid rgba(196,181,253,0.4);"
                    title="Close"
                >
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Content -->
                <div class="text-center">
                    <span class="text-7xl block animate-unicorn-bounce">🦄</span>
                    <h2 class="mt-4 font-display text-3xl sm:text-4xl font-bold text-purple-900 leading-tight">
                        Mau Aplikasi Ini?
                    </h2>
                    <p class="mt-3 text-lg sm:text-xl font-display font-bold text-rainbow">
                        Source Code Dijual ✦
                    </p>
                </div>

                <div class="mt-6 p-6 rounded-2xl" style="background: linear-gradient(135deg, rgba(255,122,198,0.08), rgba(167,139,250,0.1), rgba(125,211,252,0.08)); border: 2px solid rgba(167,139,250,0.25);">
                    <p class="text-purple-800 font-semibold text-center mb-4">
                        Aplikasi invoicing Laravel lengkap — siap deploy &amp; rebrand
                    </p>
                    <ul class="grid sm:grid-cols-2 gap-2 text-sm text-purple-700">
                        <li class="flex items-start gap-2">
                            <span class="text-pink-400 mt-0.5 shrink-0">✦</span>
                            <span>Source code Laravel 13 + PHP 8.3</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-purple-400 mt-0.5 shrink-0">✦</span>
                            <span>5 template invoice siap pakai</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-sky-400 mt-0.5 shrink-0">✦</span>
                            <span>Klien, Invoice, Pembayaran, Laporan</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-mint-400 mt-0.5 shrink-0">✦</span>
                            <span>Invoice berulang otomatis</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-gold-400 mt-0.5 shrink-0">✦</span>
                            <span>Kirim email + PDF bawaan</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-pink-400 mt-0.5 shrink-0">✦</span>
                            <span>Halaman SEO otomatis</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-purple-400 mt-0.5 shrink-0">✦</span>
                            <span>Tema Unicorn Tailwind + Alpine.js</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-sky-400 mt-0.5 shrink-0">✦</span>
                            <span>Unlimited project &amp; klien — tanpa batasan</span>
                        </li>
                    </ul>
                </div>

                <!-- Harga & CTA -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-purple-400 font-medium uppercase tracking-wider">Harga Nego — DM untuk info</p>
                    <div class="mt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                        <a
                            href="https://wa.me/62081296052010"
                            target="_blank"
                            rel="noopener"
                            class="btn-unicorn px-10 py-4 text-lg !gap-3 w-full sm:w-auto"
                        >
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Chat WhatsApp
                        </a>
                        <p class="text-purple-700 font-bold text-xl sm:ml-2">081296052010</p>
                    </div>
                    <p class="mt-4 text-xs text-purple-400">
                        Lisensi unlimited. Rebrand, jual ulang, atau pakai untuk klien — punya kamu sepenuhnya.
                    </p>
                </div>

                <!-- Footer -->
                <p class="mt-6 text-center text-xs text-purple-300">
                    ✦ Tersedia juga: kustomisasi, white-label, dan jasa integrasi ✦
                </p>
            </div>
        </div>
    </div>
    </body>
</html>
