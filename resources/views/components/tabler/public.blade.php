<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trim($__env->yieldContent('title')) ? $__env->yieldContent('title') . ' — ' : '' }}{{ branding('app_name') }}</title>
    @hasSection('meta')@yield('meta')@endif
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
<div class="page">
    <header class="navbar navbar-expand-md d-print-none">
        <div class="container-xl">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#public-menu"
                aria-controls="public-menu" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a href="/" class="navbar-brand navbar-brand-autodark">
                <span class="logo-tile">I</span>
                <span class="font-display">{{ branding('app_name') }}</span>
            </a>
            <div class="collapse navbar-collapse" id="public-menu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/">{{ __('Home') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('pricing') }}">{{ __('Pricing') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('blog.index') }}">{{ __('Blog') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('docs') }}">{{ __('Docs') }}</a></li>
                    @auth
                        <li class="nav-item"><a class="btn btn-primary ms-md-2" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Log in') }}</a></li>
                        <li class="nav-item"><a class="btn btn-primary ms-md-2" href="{{ route('register') }}">{{ __('Sign Up') }}</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </header>
    <div class="page-wrapper">
        <div class="page-body">
            <div class="container-xl">
                {{ $slot }}
            </div>
        </div>
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
