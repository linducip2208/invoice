<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trim($__env->yieldContent('title')) ? $__env->yieldContent('title') . ' — ' : '' }}{{ branding('app_name') }}</title>
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
<body class="d-flex flex-column">
<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="text-center mb-4">
            <a href="/" class="navbar-brand navbar-brand-autodark" aria-label="{{ branding('app_name') }} home">
                <span class="logo-tile">I</span>
                <span class="font-display fs-3">{{ branding('app_name') }}</span>
            </a>
        </div>
        <x-tabler.alert />
        {{ $slot }}
    </div>
</div>
@stack('scripts')
</body>
</html>
