<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($title ?? null) ? $title . ' — ' : '' }}{{ __('Platform Admin') }} · {{ branding('app_name') }}</title>
    <meta name="robots" content="noindex, nofollow">
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
@php
    $isActive = fn($p) => request()->routeIs($p);
    $mega = fn($route, $icon, $label, $desc, $pattern = null, $params = []) => [
        'href' => Route::has($route) ? route($route, $params) : '#',
        'active' => $pattern ? request()->routeIs($pattern) : request()->routeIs($route),
        'icon' => $icon, 'label' => $label, 'desc' => $desc,
    ];
    $notifPending = \App\Models\Subscription::where('status', 'pending')->whereNotNull('manual_proof_path')->count();
    $notifSubs = \App\Models\Subscription::where('status', 'pending')->count();
    $groups = [
        ['key' => 'overview', 'label' => __('Dashboard'), 'icon' => 'layout-dashboard', 'patterns' => ['admin.dashboard', 'admin.analytics'], 'type' => 'simple', 'items' => [
            $mega('admin.dashboard', 'layout-dashboard', __('Platform overview'), __('KPI + aktivitas'), 'admin.dashboard'),
            $mega('admin.analytics', 'chart-line', __('Analytics'), __('Tren & distribusi'), 'admin.analytics'),
        ]],
        ['key' => 'saas', 'label' => __('SaaS'), 'icon' => 'building', 'patterns' => ['admin.users.*', 'admin.workspaces.*', 'admin.plans.*', 'admin.subscriptions.*'], 'type' => 'mega', 'cols' => [
            [__('Kelola'), [
                $mega('admin.users.index', 'users', __('Users'), __('Akun & peran'), 'admin.users.*'),
                $mega('admin.workspaces.index', 'building', __('Workspaces'), __('Bisnis tenant'), 'admin.workspaces.*'),
            ]],
            [__('Monetisasi'), [
                $mega('admin.plans.index', 'wallet', __('Plans'), __('Paket & harga'), 'admin.plans.*'),
                $mega('admin.subscriptions.index', 'repeat', __('Subscriptions'), __('Langganan aktif'), 'admin.subscriptions.*'),
                $mega('admin.subscriptions.manual', 'plus', __('Manual Assign'), __('Pasang plan manual'), 'admin.subscriptions.manual*'),
            ]],
        ], 'feature' => ['title' => __('Butuh intervensi?'), 'text' => __('Pending payment & subscription menunggu verifikasi.'), 'href' => route('admin.manual-payments.index'), 'cta' => __('Verifikasi sekarang →')]],
        ['key' => 'revenue', 'label' => __('Revenue'), 'icon' => 'cash', 'patterns' => ['admin.manual-payments.*', 'admin.coupons.*', 'admin.revenue'], 'type' => 'mega-sm', 'cols' => [
            [__('Uang'), [
                $mega('admin.manual-payments.index', 'cash', __('Manual Payments'), __('Verifikasi transfer'), 'admin.manual-payments.*'),
                $mega('admin.coupons.index', 'percentage', __('Coupons'), __('Kode diskon'), 'admin.coupons.*'),
            ]],
            [__('Laporan'), [
                $mega('admin.revenue', 'chart-bar', __('Revenue Reports'), __('MRR & tren'), 'admin.revenue'),
            ]],
        ]],
        ['key' => 'config', 'label' => __('Konfigurasi'), 'icon' => 'settings', 'patterns' => ['admin.branding.*', 'admin.settings', 'admin.audit-logs', 'admin.blog.*', 'dashboard'], 'type' => 'simple', 'items' => [
            $mega('admin.branding.edit', 'palette', __('Branding'), __('Logo & warna'), 'admin.branding.*'),
            $mega('admin.settings', 'settings', __('Settings'), __('Kurs & umum'), 'admin.settings'),
            $mega('admin.audit-logs', 'history', __('Audit Logs'), __('Jejak admin'), 'admin.audit-logs'),
            $mega('admin.blog.index', 'file-text', __('Blog management'), __('Konten publik'), 'admin.blog.*'),
            $mega('dashboard', 'arrow-left', __('Back to workspace'), __('Mode tenant'), 'dashboard'),
        ]],
    ];
    $groupActive = fn($g) => collect($g['patterns'] ?? [])->contains(fn($p) => request()->routeIs($p));
@endphp
<div class="app-shell">
    <header class="app-topbar d-print-none">
        <div class="app-container topbar-main">
            <button class="topbar-icon-btn d-xl-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileNav"
                aria-controls="adminMobileNav" aria-label="{{ __('Toggle navigation') }}">
                <x-ti name="menu-2" />
            </button>
            <a href="{{ route('admin.dashboard') }}" class="topbar-brand" aria-label="{{ __('Platform Admin') }}">
                <span class="brand-mark">I</span>
                <span class="brand-text"><span class="brand-name">{{ __('Platform') }} <span class="badge bg-red-lt ms-1">{{ __('Admin') }}</span></span><span class="brand-sub">{{ __('SaaS Control') }}</span></span>
            </a>
            <span class="topbar-divider d-none d-md-block" aria-hidden="true"></span>
            <form method="GET" action="{{ route('admin.users.index') }}" class="topbar-search d-none d-lg-flex" role="search" aria-label="{{ __('Global search') }}">
                <x-ti name="search" />
                <input type="search" name="search" value="{{ request('search') }}" class="form-control"
                    placeholder="{{ __('Search users, workspaces…') }}" aria-label="{{ __('Global search') }}" id="admin-global-search">
                <span class="search-keys"><kbd>/</kbd></span>
            </form>
            <div class="topbar-actions">
                <div class="dropdown">
                    <button class="topbar-icon-btn position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('Notification center') }}">
                        <x-ti name="bell" />
                        @if($notifSubs > 0)<span class="badge-dot"></span>@endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notif-menu">
                        <div class="notif-head"><span class="notif-title">{{ __('Notification center') }}</span><a class="notif-action" href="{{ route('admin.audit-logs') }}">{{ __('Lihat semua') }}</a></div>
                        <div class="notif-list">
                            <a class="notif-item{{ $notifPending ? ' is-unread' : '' }}" href="{{ route('admin.manual-payments.index') }}">
                                <span class="notif-text">{{ __('Pending manual payments') }} ({{ $notifPending }})<span class="notif-time">{{ __('Butuh verifikasi') }}</span></span>
                            </a>
                            <a class="notif-item{{ $notifSubs ? ' is-unread' : '' }}" href="{{ route('admin.subscriptions.index', ['status' => 'pending']) }}">
                                <span class="notif-text">{{ __('Pending subscriptions') }} ({{ $notifSubs }})<span class="notif-time">{{ __('Menunggu aktivasi') }}</span></span>
                            </a>
                            <a class="notif-item" href="{{ route('admin.revenue') }}">
                                <span class="notif-text">{{ __('Revenue reports') }}<span class="notif-time">{{ __('MRR & tren') }}</span></span>
                            </a>
                        </div>
                        <a class="notif-foot" href="{{ route('admin.audit-logs') }}">{{ __('Recent admin activity') }}</a>
                    </div>
                </div>
                <button class="topbar-icon-btn" data-theme-toggle aria-label="{{ __('Toggle dark mode') }}">
                    <x-ti name="sparkles" />
                </button>
                <div class="dropdown topbar-profile">
                    <button class="profile-trigger" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('Account menu') }}">
                        <span class="profile-avatar">{{ strtoupper(mb_substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                        <span class="profile-name d-none d-xl-flex">{{ Str::limit(Auth::user()->name ?? '', 14) }}<small>{{ Auth::user()->email }}</small></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end profile-menu">
                        <div class="profile-card">
                            <span class="profile-avatar lg">{{ strtoupper(mb_substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                            <div class="min-w-0"><strong>{{ Auth::user()->name }}</strong><small>{{ Auth::user()->email }}</small><span class="profile-role">{{ __('Superadmin') }}</span></div>
                        </div>
                        <div class="profile-links">
                            <a class="dropdown-item" href="{{ route('dashboard') }}"><x-ti name="arrow-left" /> {{ __('Workspace') }}</a>
                            <a class="dropdown-item" href="{{ route('admin.settings') }}"><x-ti name="settings" /> {{ __('Settings') }}</a>
                        </div>
                        <div class="profile-foot">
                            <a href="{{ route('dashboard') }}">{{ __('Workspace') }}</a>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">@csrf<button type="submit" style="all:unset;cursor:pointer;width:100%;">{{ __('Log out') }}</button></form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <nav class="topbar-nav d-none d-xl-block" aria-label="{{ __('Navigasi utama') }}">
            <div class="app-container">
                <ul class="nav-menu" id="adminnav">
                    @foreach($groups as $g)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle{{ $groupActive($g) ? ' active' : '' }}" href="#" role="button"
                                data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false"
                                @if($groupActive($g)) aria-current="true" @endif>
                                <x-ti :name="$g['icon']" /><span>{{ $g['label'] }}</span>
                            </a>
                            @if(($g['type'] ?? '') === 'simple')
                                <ul class="dropdown-menu nav-dropdown">
                                    <li class="dd-heading">{{ $g['label'] }}</li>
                                    @foreach($g['items'] as $item)
                                        <li><a class="dropdown-item nav-dd-item{{ $item['active'] ? ' active' : '' }}" href="{{ $item['href'] }}"
                                            @if($item['active']) aria-current="page" @endif><span class="dd-icon"><x-ti :name="$item['icon']" /></span><span><strong>{{ $item['label'] }}</strong><small>{{ $item['desc'] }}</small></span></a></li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="dropdown-menu mega-menu{{ isset($g['feature']) ? ' has-feature' : '' }}{{ ($g['type'] ?? '') === 'mega-sm' ? ' mega-sm' : '' }}">
                                    <div class="mega-grid" style="--mega-cols: {{ count($g['cols']) }};">
                                        @foreach($g['cols'] as [$colTitle, $colItems])
                                            <div class="mega-col">
                                                <div class="mega-title">{{ $colTitle }}</div>
                                                @foreach($colItems as $item)
                                                    <a class="mega-link{{ $item['active'] ? ' active' : '' }}" href="{{ $item['href'] }}"
                                                        @if($item['active']) aria-current="page" @endif>
                                                        <span class="mega-icon"><x-ti :name="$item['icon']" /></span>
                                                        <span class="mega-text"><strong>{{ $item['label'] }}</strong><span>{{ $item['desc'] }}</span></span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endforeach
                                        @if(isset($g['feature']))
                                            <a class="mega-feature" href="{{ $g['feature']['href'] }}">
                                                <span class="mega-feature-icon"><x-ti name="bolt" /></span>
                                                <span class="mega-feature-title">{{ $g['feature']['title'] }}</span>
                                                <span class="mega-feature-text">{{ $g['feature']['text'] }}</span>
                                                <span class="mega-feature-cta">{{ $g['feature']['cta'] }}</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </nav>
    </header>

    <main class="app-main page-body" id="main-content">
        <div class="app-container">
            <nav aria-label="breadcrumb" class="d-none d-md-block">
                <ol class="breadcrumb mb-3">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Platform') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $title ?? __('Overview') }}</li>
                </ol>
            </nav>
            <div class="page-header d-print-none">
                <div>
                    <div class="page-pretitle">{{ __('Platform administration') }}</div>
                    <h1 class="page-title">{{ $title ?? __('Platform') }}</h1>
                    @isset($subtitle)
                        <div class="text-muted mt-1">{{ $subtitle }}</div>
                    @endisset
                </div>
                <div class="page-actions">
                    {{ $actions ?? '' }}
                </div>
            </div>
            <x-tabler.alert />
            {{ $slot }}
        </div>
    </main>

    <footer class="app-footer d-print-none">
        <div class="app-container d-flex justify-content-between flex-wrap gap-2">
            <span>&copy; {{ date('Y') }} {{ branding('app_name') }} · {{ __('Platform') }}</span>
            <span class="footer-status"><span class="live-dot"></span>{{ __('All systems nominal') }}</span>
        </div>
    </footer>
</div>

<div class="offcanvas offcanvas-start mobile-nav" tabindex="-1" id="adminMobileNav" aria-label="{{ __('Navigasi') }}">
    <div class="offcanvas-header">
        <span class="brand-text"><span class="brand-name" style="color:var(--app-heading)">{{ __('Platform') }}</span></span>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('Close') }}"></button>
    </div>
    <div class="offcanvas-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-2" role="search">
            <div class="input-icon">
                <span class="input-icon-addon"><x-ti name="search" /></span>
                <input type="search" name="search" class="form-control" placeholder="{{ __('Search users, workspaces…') }}" aria-label="{{ __('Search') }}">
            </div>
        </form>
        @foreach($groups as $g)
            <details class="mb-1" @if($groupActive($g)) open @endif>
                <summary class="mobile-nav-link" style="cursor:pointer;list-style:none;">
                    <x-ti :name="$g['icon']" /> <span class="flex-grow-1">{{ $g['label'] }}</span>
                </summary>
                <div class="ps-2">
                    @if(isset($g['items']))
                        @foreach($g['items'] as $item)
                            <a class="mobile-nav-link{{ $item['active'] ? ' active' : '' }}" href="{{ $item['href'] }}"><x-ti :name="$item['icon']" /> {{ $item['label'] }}</a>
                        @endforeach
                    @else
                        @foreach($g['cols'] as [$colTitle, $colItems])
                            @foreach($colItems as $item)
                                <a class="mobile-nav-link{{ $item['active'] ? ' active' : '' }}" href="{{ $item['href'] }}"><x-ti :name="$item['icon']" /> {{ $item['label'] }}</a>
                            @endforeach
                        @endforeach
                    @endif
                </div>
            </details>
        @endforeach
    </div>
</div>

@stack('scripts')
<script>
(function () {
    var input = document.getElementById('admin-global-search');
    if (!input) return;
    document.addEventListener('keydown', function (e) {
        if ((e.key === '/' && !/INPUT|TEXTAREA|SELECT/.test(document.activeElement.tagName)) || (e.ctrlKey && e.key.toLowerCase() === 'k')) {
            e.preventDefault(); input.focus();
        }
    });
})();
</script>
</body>
</html>
