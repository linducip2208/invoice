<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✦ {{ $metaTitle ?? branding('app_name') }}</title>
    <meta name="description" content="{{ $metaDescription ?? '' }}">
    <meta property="og:title" content="✦ {{ $metaTitle ?? branding('app_name') }}">
    <meta property="og:description" content="{{ $metaDescription ?? '' }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ branding('app_name') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="✦ {{ $metaTitle ?? branding('app_name') }}">
    <meta name="twitter:description" content="{{ $metaDescription ?? '' }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-branding-head />
    @hasSection('jsonld')
        <script type="application/ld+json">@yield('jsonld')</script>
    @endif
</head>
<body class="font-sans antialiased text-purple-900">
    <nav class="sticky top-0 z-50" style="background: rgba(255, 248, 254, 0.85); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(196,181,253,0.3);">
        <div class="rainbow-bar"></div>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5">
                <span class="logo-glow flex items-center justify-center" style="padding: 4px;">
                    @if(branding('logo_path'))
                        <img src="{{ asset('storage/' . branding('logo_path')) }}" alt="{{ branding('app_name') }}" class="h-8 w-auto">
                    @else
                        <x-application-logo size="sm" />
                    @endif
                </span>
                <span class="font-display font-bold text-lg text-rainbow">{{ branding('app_name') }}</span>
            </a>
            <nav class="flex items-center gap-2 sm:gap-4 text-sm font-semibold">
                <a href="/" class="text-purple-600 hover:text-pink-500">{{ __('Home') }}</a>
                <a href="{{ route('docs') }}" class="text-purple-600 hover:text-pink-500 hidden sm:inline">{{ __('Docs') }}</a>
                <a href="{{ route('seo.templates') }}" class="text-purple-600 hover:text-pink-500 hidden sm:inline">{{ __('Templates') }}</a>
                <a href="{{ route('seo.best-tools', ['year' => now()->year]) }}" class="text-purple-600 hover:text-pink-500 hidden sm:inline">{{ __('Best Tools') }}</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-unicorn">✦ {{ __('Dashboard') }}</a>
                @else
                    <a href="{{ route('register') }}" class="btn-unicorn">✦ {{ __('Sign Up') }}</a>
                @endauth
            </nav>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="mt-16 py-10 px-4 sm:px-6" style="border-top: 1px solid rgba(196,181,253,0.3); background: rgba(255,255,255,0.4);">
        <div class="max-w-5xl mx-auto text-center">
            <div class="rainbow-bar w-32 mx-auto rounded-full mb-4"></div>
            <p class="text-sm text-purple-500">
                ✦ &copy; {{ date('Y') }} {{ branding('app_name') }} · {{ branding('tagline') }} 🦄
            </p>
            <p class="mt-2 text-xs text-purple-400 space-x-2">
                <a href="{{ route('docs') }}" class="hover:text-pink-500">{{ __('Docs') }}</a> ·
                <a href="{{ route('seo.templates') }}" class="hover:text-pink-500">{{ __('Templates') }}</a> ·
                <a href="{{ route('seo.best-tools', ['year' => date('Y')]) }}" class="hover:text-pink-500">{{ __('Best Tools') }} {{ date('Y') }}</a> ·
                <a href="{{ route('seo.alternatives', ['slug' => 'freshbooks']) }}" class="hover:text-pink-500">{{ __('Alternatives') }}</a>
            </p>
        </div>
    </footer>
</body>
</html>
