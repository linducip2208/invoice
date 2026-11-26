<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($title ?? null) ? $title . ' — ' : '' }}{{ branding('app_name') }}</title>
    <meta name="description" content="{{ $metaDescription ?? '' }}">
    <meta property="og:title" content="{{ ($title ?? null) ? $title . ' — ' : '' }}{{ branding('app_name') }}">
    <meta property="og:description" content="{{ $metaDescription ?? '' }}">
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ branding('app_name') }}">
    @if(!empty($publishedTime))
        <meta property="article:published_time" content="{{ $publishedTime }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#7c5cf0">
    <script>
        try {
            var t = localStorage.getItem('invocely-theme');
            if (!t) t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            document.documentElement.setAttribute('data-bs-theme', t);
        } catch (e) { document.documentElement.setAttribute('data-bs-theme', 'light'); }
    </script>
    @vite(['resources/css/tabler.css', 'resources/js/tabler.js'])
    @stack('head')
</head>
<body>
<a href="#main-content" class="skip-link">{{ __('Skip to main content') }}</a>
<div class="page">
    <header class="app-topbar d-print-none">
        <div class="app-container topbar-main">
            <button class="topbar-icon-btn d-xl-none" type="button" data-bs-toggle="collapse" data-bs-target="#public-menu"
                aria-controls="public-menu" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <x-ti name="menu-2" />
            </button>
            <a href="/" class="topbar-brand" aria-label="{{ branding('app_name') }} home">
                <span class="brand-mark">I</span>
                <span class="brand-text"><span class="brand-name">{{ branding('app_name') }}</span><span class="brand-sub">{{ __('Billing OS') }}</span></span>
            </a>
            <nav class="topbar-nav d-none d-xl-block" aria-label="{{ __('Navigasi utama') }}" style="height:50px;">
                <div class="app-container">
                    <ul class="nav-menu">
                        <li class="nav-item"><a class="nav-link{{ request()->routeIs('home') ? ' active' : '' }}" href="/"><x-ti name="home" /><span>{{ __('Home') }}</span></a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle{{ request()->routeIs('pricing', 'seo.*') ? ' active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false"><x-ti name="package" /><span>{{ __('Produk') }}</span></a>
                            <div class="dropdown-menu mega-menu has-feature">
                                <div class="mega-grid" style="--mega-cols: 2;">
                                    <div class="mega-col">
                                        <div class="mega-title">{{ __('Jelajah') }}</div>
                                        <a class="mega-link" href="{{ url('/#showcase') }}"><span class="mega-icon"><x-ti name="star" /></span><span class="mega-text"><strong>{{ __('Fitur') }}</strong><span>{{ __('12 fitur inti') }}</span></span></a>
                                        <a class="mega-link" href="{{ route('seo.templates') }}"><span class="mega-icon"><x-ti name="template" /></span><span class="mega-text"><strong>{{ __('Template') }}</strong><span>{{ __('6 desain invoice') }}</span></span></a>
                                        <a class="mega-link" href="{{ route('pricing') }}"><span class="mega-icon"><x-ti name="wallet" /></span><span class="mega-text"><strong>{{ __('Harga') }}</strong><span>{{ __('Free / Pro / Business') }}</span></span></a>
                                    </div>
                                    <div class="mega-col">
                                        <div class="mega-title">{{ __('Banding') }}</div>
                                        <a class="mega-link" href="{{ route('seo.compare', ['a' => 'free', 'b' => 'pro']) }}"><span class="mega-icon"><x-ti name="chart-bar" /></span><span class="mega-text"><strong>Free vs Pro</strong><span>{{ __('Pilih tier') }}</span></span></a>
                                        <a class="mega-link" href="{{ route('seo.compare', ['a' => 'pro', 'b' => 'business']) }}"><span class="mega-icon"><x-ti name="chart-line" /></span><span class="mega-text"><strong>Pro vs Business</strong><span>{{ __('Naik kelas') }}</span></span></a>
                                        <a class="mega-link" href="{{ route('seo.alternatives', ['slug' => 'freshbooks']) }}"><span class="mega-icon"><x-ti name="repeat" /></span><span class="mega-text"><strong>{{ __('Alternatif') }}</strong><span>{{ __('vs kompetitor') }}</span></span></a>
                                    </div>
                                    @if(Route::has('register'))
                                    <a class="mega-feature" href="{{ route('register') }}">
                                        <span class="mega-feature-icon"><x-ti name="bolt" /></span>
                                        <span class="mega-feature-title">{{ __('Mulai Gratis') }}</span>
                                        <span class="mega-feature-text">{{ __('Invoice pertama dalam hitungan menit. Tanpa kartu kredit.') }}</span>
                                        <span class="mega-feature-cta">{{ __('Daftar →') }}</span>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle{{ request()->routeIs('blog.*', 'docs') ? ' active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false"><x-ti name="book" /><span>{{ __('Sumber Daya') }}</span></a>
                            <ul class="dropdown-menu nav-dropdown">
                                <li class="dd-heading">{{ __('Belajar') }}</li>
                                <li><a class="dropdown-item nav-dd-item" href="{{ route('docs') }}"><span class="dd-icon"><x-ti name="book" /></span><span><strong>{{ __('Docs') }}</strong><small>{{ __('Panduan pakai') }}</small></span></a></li>
                                <li><a class="dropdown-item nav-dd-item" href="{{ route('blog.index') }}"><span class="dd-icon"><x-ti name="file-text" /></span><span><strong>{{ __('Blog') }}</strong><small>{{ __('Tips & update') }}</small></span></a></li>
                                @auth
                                <li><a class="dropdown-item nav-dd-item" href="{{ route('support.index') }}"><span class="dd-icon"><x-ti name="lifebuoy" /></span><span><strong>{{ __('Support') }}</strong><small>{{ __('Hubungi kami') }}</small></span></a></li>
                                @endauth
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="topbar-actions">
                @auth
                    <a class="btn-quick" href="{{ route('dashboard') }}"><x-ti name="layout-dashboard" /> {{ __('Dashboard') }}</a>
                @else
                    <a class="topbar-icon-btn d-none d-sm-inline-flex" href="{{ route('login') }}" aria-label="{{ __('Log in') }}" title="{{ __('Log in') }}"><x-ti name="user" /></a>
                    @if(Route::has('register'))
                        <a class="btn-quick" href="{{ route('register') }}"><x-ti name="plus" /> {{ __('Sign Up') }}</a>
                    @endif
                @endauth
            </div>
        </div>
        <div class="collapse d-xl-none" id="public-menu">
            <div class="app-container py-2">
                <a class="mobile-nav-link" href="/"><x-ti name="home" /> {{ __('Home') }}</a>
                <a class="mobile-nav-link" href="{{ url('/#showcase') }}"><x-ti name="star" /> {{ __('Fitur') }}</a>
                <a class="mobile-nav-link" href="{{ route('seo.templates') }}"><x-ti name="template" /> {{ __('Template') }}</a>
                <a class="mobile-nav-link" href="{{ route('pricing') }}"><x-ti name="wallet" /> {{ __('Pricing') }}</a>
                <a class="mobile-nav-link" href="{{ route('blog.index') }}"><x-ti name="file-text" /> {{ __('Blog') }}</a>
                <a class="mobile-nav-link" href="{{ route('docs') }}"><x-ti name="book" /> {{ __('Docs') }}</a>
                @auth
                    <a class="mobile-nav-link" href="{{ route('dashboard') }}"><x-ti name="layout-dashboard" /> {{ __('Dashboard') }}</a>
                @else
                    <a class="mobile-nav-link" href="{{ route('login') }}"><x-ti name="user" /> {{ __('Log in') }}</a>
                    @if(Route::has('register'))
                        <a class="mobile-nav-link" href="{{ route('register') }}"><x-ti name="plus" /> {{ __('Sign Up') }}</a>
                    @endif
                @endauth
            </div>
        </div>
    </header>
    <div class="page-wrapper">
        <main class="page-body" id="main-content">
            <div class="container-xl">
                {{ $slot }}
            </div>
        </main>
        <footer class="footer footer-transparent">
            <div class="container-xl">
                <div class="row text-center align-items-center flex-row-reverse">
                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item"><a href="{{ route('docs') }}">{{ __('Docs') }}</a></li>
                            <li class="list-inline-item"><a href="{{ route('pricing') }}">{{ __('Pricing') }}</a></li>
                            <li class="list-inline-item"><a href="{{ route('blog.index') }}">{{ __('Blog') }}</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-auto ms-lg-auto">&copy; {{ date('Y') }} {{ branding('app_name') }}</div>
                </div>
            </div>
        </footer>
    </div>
</div>
@stack('scripts')
</body>
</html>
