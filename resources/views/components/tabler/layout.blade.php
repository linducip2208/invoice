<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($title ?? null) ? $title . ' — ' : '' }}{{ branding('app_name') }}</title>

    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#17133a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Invocely">

    <script>
        try {
            var t = localStorage.getItem('invocely-theme');
            if (!t) t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            document.documentElement.setAttribute('data-bs-theme', t);
        } catch (e) { document.documentElement.setAttribute('data-bs-theme', 'light'); }
    </script>

    @vite(['resources/css/tabler.css', 'resources/js/tabler.js'])
    <style>
        :root {
            @if(branding('primary_color')) --tblr-primary: {{ branding('primary_color') }}; @endif
        }
    </style>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(e => console.warn('SW reg failed', e));
            });
        }
    </script>
    @stack('head')
</head>
<body>
<a href="#main-content" class="skip-link">{{ __('Skip to main content') }}</a>
@php
    $ws = workspace();
    $isSuper = Auth::user()?->isSuperadmin();
    $unreadCount = \App\Models\AppNotification::unread()->count();
    $unreadList = \App\Models\AppNotification::unread()->latest()->take(6)->get();
    $mega = fn($route, $icon, $label, $desc, $pattern = null) => [
        'href' => Route::has($route) ? route($route) : '#',
        'active' => $pattern ? request()->routeIs($pattern) : request()->routeIs($route),
        'icon' => $icon, 'label' => $label, 'desc' => $desc,
    ];
    $groups = [
        ['key' => 'dashboard', 'label' => __('Dashboard'), 'icon' => 'layout-dashboard', 'patterns' => ['dashboard', 'notifications.*'], 'type' => 'simple', 'items' => [
            $mega('dashboard', 'layout-dashboard', __('Dashboard'), __('Ringkasan tagihan & kas'), 'dashboard'),
            $mega('notifications.index', 'bell', __('Notifications'), __('Aktivitas terbaru'), 'notifications.*'),
        ]],
        ['key' => 'penawaran', 'label' => __('Penawaran'), 'icon' => 'file-text', 'patterns' => ['quotes.*', 'products.*', 'bundles.*', 'clients.*'], 'type' => 'mega', 'cols' => [
            [__('Dokumen'), [
                $mega('quotes.index', 'file-text', __('Quotations'), __('Tawar, setujui, konversi'), 'quotes.*'),
                $mega('quotes.create', 'file-plus', __('Buat Quotation'), __('Penawaran baru'), 'quotes.create'),
            ]],
            [__('Katalog'), [
                $mega('products.index', 'package', __('Products & Services'), __('Katalog harga'), 'products.*'),
                $mega('bundles.index', 'package', __('Bundles'), __('Paket hemat'), 'bundles.*'),
                $mega('clients.index', 'users', __('Customers'), __('Data pelanggan'), 'clients.*'),
            ]],
        ], 'feature' => ['title' => __('Ubah penawaran jadi uang'), 'text' => __('Quote accepted → invoice 1-klik, DP & pelunasan otomatis.'), 'href' => route('quotes.index'), 'cta' => __('Buka quotation →')]],
        ['key' => 'penagihan', 'label' => __('Penagihan'), 'icon' => 'file-invoice', 'patterns' => ['invoices.*', 'recurring.*', 'credit-notes.*', 'approvals.*', 'projects.*', 'time-entries.*'], 'type' => 'mega', 'cols' => [
            [__('Tagih'), [
                $mega('invoices.index', 'file-invoice', __('Invoices'), __('Buat, kirim, pantau'), 'invoices.*'),
                $mega('invoices.create', 'plus', __('Buat Invoice'), __('Tagihan baru'), 'invoices.create'),
                $mega('recurring.index', 'repeat', __('Recurring Invoices'), __('Tagihan berulang'), 'recurring.*'),
                $mega('credit-notes.index', 'receipt-refund', __('Credit Notes'), __('Koreksi tagihan'), 'credit-notes.*'),
            ]],
            [__('Kerja'), [
                $mega('approvals.index', 'circle-check', __('Persetujuan'), __('Alur approval'), 'approvals.*'),
                $mega('projects.index', 'briefcase', __('Projects'), __('Proyek & progres'), 'projects.*'),
                $mega('time-entries.index', 'clock', __('Time Tracking'), __('Jam jadi invoice'), 'time-entries.*'),
            ]],
        ], 'feature' => ['title' => __('Terima beres dari jam kerja'), 'text' => __('Timer → entry → invoice 1-klik, tanpa rekap manual.'), 'href' => route('time-entries.index'), 'cta' => __('Buka time tracking →')]],
        ['key' => 'pembayaran', 'label' => __('Pembayaran'), 'icon' => 'credit-card', 'patterns' => ['payments.*', 'coupons.*', 'reports.aging', 'bank-reconcile.*', 'expenses.*'], 'type' => 'mega', 'cols' => [
            [__('Terima'), [
                $mega('payments.index', 'credit-card', __('Payments'), __('Lunas & parsial'), 'payments.*'),
                $mega('reports.aging', 'hourglass', __('Outstanding Invoices'), __('Piutang terbuka'), 'reports.aging'),
                $mega('bank-reconcile.index', 'building-bank', __('Reconciliation'), __('Cocokkan mutasi'), 'bank-reconcile.*'),
            ]],
            [__('Kelola'), [
                $mega('coupons.index', 'percentage', __('Kupon Diskon'), __('Promo invoice'), 'coupons.*'),
                $mega('expenses.index', 'receipt', __('Expenses'), __('Biaya + OCR'), 'expenses.*'),
            ]],
        ]],
        ['key' => 'laporan', 'label' => __('Laporan'), 'icon' => 'chart-bar', 'patterns' => ['reports.*', 'accounting-exports.*', 'exports.*'], 'type' => 'mega-sm', 'cols' => [
            [__('Analitik'), [
                $mega('reports.index', 'chart-bar', __('Reports'), __('Semua laporan'), 'reports.index'),
                $mega('reports.cashflow', 'chart-line', __('Cash Flow'), __('Arus kas + forecast'), 'reports.cashflow'),
            ]],
            [__('Keuangan'), [
                $mega('reports.pl', 'chart-pie', __('Profit & Loss'), __('Laba rugi'), 'reports.pl'),
                $mega('reports.tax', 'percentage', __('Tax Reports'), __('Pajak'), 'reports.tax'),
                $mega('accounting-exports.index', 'file-export', __('Accounting Exports'), __('Ekspor akuntansi'), 'accounting-exports.*'),
                $mega('exports.index', 'download', __('Exports'), __('CSV data'), 'exports.*'),
            ]],
        ]],
        ['key' => 'otomatisasi', 'label' => __('Otomatisasi'), 'icon' => 'robot', 'patterns' => ['ai-providers.*', 'expenses.ocr.*', 'email-forwards.*', 'report-subscriptions.*', 'api-tokens.*', 'tenant-webhooks.*', 'embed.demo'], 'type' => 'mega', 'cols' => [
            [__('Cerdas'), [
                $mega('ai-providers.index', 'robot', __('AI Providers'), __('Asisten & OCR'), 'ai-providers.*'),
                $mega('expenses.ocr.upload', 'scan', __('OCR Receipt'), __('Struk jadi biaya'), 'expenses.ocr.*'),
                $mega('email-forwards.index', 'mail', __('Email ke Expense'), __('Maju → tercatat'), 'email-forwards.*'),
                $mega('report-subscriptions.index', 'send', __('Scheduled Reports'), __('Laporan rutin'), 'report-subscriptions.*'),
            ]],
            [__('Integrasi'), [
                $mega('api-tokens.index', 'key', __('API Tokens'), __('Akses API'), 'api-tokens.*'),
                $mega('tenant-webhooks.index', 'link', __('Webhooks Out'), __('Event keluar'), 'tenant-webhooks.*'),
                $mega('embed.demo', 'code', __('Embed Widget'), __('Sematkan invoice'), 'embed.demo'),
            ]],
        ]],
        ['key' => 'pengaturan', 'label' => __('Pengaturan'), 'icon' => 'settings', 'patterns' => ['workspace.*', 'team.*', 'invoice-layouts.*', 'gateways.*', 'custom-domain.*', 'clients.import.*', 'audit-logs.*'], 'type' => 'simple-wide', 'items' => [
            $mega('workspace.settings', 'settings', __('Company Settings'), __('Identitas & default'), 'workspace.*'),
            $mega('team.index', 'users', __('Team & Roles'), __('Akses tim'), 'team.*'),
            $mega('invoice-layouts.index', 'template', __('Invoice Templates'), __('Desain invoice'), 'invoice-layouts.*'),
            $mega('gateways.index', 'plug', __('Payment Gateways'), __('Kanal bayar'), 'gateways.*'),
            $mega('custom-domain.show', 'globe', __('Custom Domain'), __('Domain sendiri'), 'custom-domain.*'),
            $mega('clients.import.show', 'upload', __('CSV Import'), __('Impor klien'), 'clients.import.*'),
            $mega('audit-logs.index', 'history', __('Audit Logs'), __('Jejak audit'), 'audit-logs.*'),
        ]],
        ['key' => 'langganan', 'label' => __('Langganan'), 'icon' => 'wallet', 'patterns' => ['billing.*', 'affiliate.*'], 'type' => 'simple', 'items' => [
            $mega('billing.show', 'wallet', __('Current Plan'), __('Paket & tagihan'), 'billing.*'),
            $mega('affiliate.index', 'star', __('Affiliate'), __('Program referral'), 'affiliate.*'),
        ]],
        ['key' => 'bantuan', 'label' => __('Bantuan'), 'icon' => 'lifebuoy', 'patterns' => ['docs', 'support.*'], 'type' => 'simple', 'items' => [
            $mega('docs', 'book', __('Documentation'), __('Panduan pakai'), 'docs'),
            $mega('support.index', 'lifebuoy', __('Support'), __('Hubungi bantuan'), 'support.*'),
        ]],
    ];
    $groupActive = fn($g) => collect($g['patterns'] ?? [])->contains(fn($p) => request()->routeIs($p));
@endphp
<div class="app-shell">
    <!-- ============ Topbar Tillora ============ -->
    <header class="app-topbar d-print-none">
        <div class="app-container topbar-main">
            <button class="topbar-icon-btn d-xl-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav"
                aria-controls="mobileNav" aria-label="{{ __('Buka navigasi') }}">
                <x-ti name="menu-2" />
            </button>
            <a class="topbar-brand" href="{{ route('dashboard') }}" aria-label="{{ branding('app_name') }} {{ __('home') }}">
                <span class="brand-mark">I</span>
                <span class="brand-text"><span class="brand-name">{{ branding('app_name') }}</span><span class="brand-sub">{{ __('Billing OS') }}</span></span>
            </a>
            <span class="topbar-divider d-none d-md-block" aria-hidden="true"></span>
            @if($ws)
                <div class="dropdown store-selector d-none d-md-block">
                    <button class="store-trigger" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('Switch workspace') }}">
                        <span class="store-icon">{{ strtoupper(mb_substr($ws->name ?? 'W', 0, 1)) }}</span>
                        <span class="store-label"><span>{{ $ws->name }}</span><small>{{ __('Workspace aktif') }}</small></span>
                        <x-ti name="chevron-down" class="store-caret" />
                    </button>
                    <div class="dropdown-menu store-menu">
                        <div class="dd-heading">{{ __('Pindah workspace') }}</div>
                        @foreach(Auth::user()->workspaces as $w)
                            <form method="POST" action="{{ route('workspace.switch', $w) }}">
                                @csrf
                                <button type="submit" class="dropdown-item store-option{{ $w->id === $ws->id ? ' active' : '' }}">
                                    <span class="store-icon">{{ strtoupper(mb_substr($w->name, 0, 1)) }}</span>
                                    <span><strong>{{ $w->name }}</strong><small>{{ $w->currency ?? '' }}</small></span>
                                </button>
                            </form>
                        @endforeach
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('workspace.settings') }}">{{ __('Business Settings') }}</a>
                    </div>
                </div>
            @endif
            <form method="GET" action="{{ route('invoices.index') }}" class="topbar-search d-none d-lg-flex" role="search" aria-label="{{ __('Global search') }}">
                <x-ti name="search" />
                <input type="search" name="search" value="{{ request('search') }}" id="ws-global-search" class="form-control"
                    placeholder="{{ __('Cari invoice, klien, menu…') }}" aria-label="{{ __('Global search') }}" autocomplete="off">
                <span class="search-keys"><kbd>/</kbd></span>
                <div class="search-results admin-search-results" id="ws-search-results"></div>
            </form>
            <div class="topbar-actions">
                <div class="dropdown d-none d-sm-block">
                    <button class="btn-quick" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('Quick create') }}">
                        <x-ti name="plus" /> <span class="d-none d-md-inline">{{ __('Buat') }}</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end create-menu">
                        <div class="dd-heading">{{ __('Buat baru') }}</div>
                        <div class="create-grid">
                            <a class="create-item" href="{{ route('invoices.create') }}"><span class="create-icon tone-primary"><x-ti name="file-invoice" /></span><strong>{{ __('Invoice') }}</strong><small>{{ __('Tagih klien') }}</small></a>
                            <a class="create-item" href="{{ route('quotes.create') }}"><span class="create-icon tone-cyan"><x-ti name="file-text" /></span><strong>{{ __('Quotation') }}</strong><small>{{ __('Penawaran') }}</small></a>
                            <a class="create-item" href="{{ route('clients.create') }}"><span class="create-icon tone-success"><x-ti name="users" /></span><strong>{{ __('Customer') }}</strong><small>{{ __('Klien baru') }}</small></a>
                            <a class="create-item" href="{{ route('payments.index') }}"><span class="create-icon tone-violet"><x-ti name="credit-card" /></span><strong>{{ __('Payment') }}</strong><small>{{ __('Catat bayar') }}</small></a>
                        </div>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="topbar-icon-btn position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('Notification center') }}">
                        <x-ti name="bell" />
                        @if($unreadCount > 0)<span class="badge-dot"></span>@endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notif-menu">
                        <div class="notif-head"><span class="notif-title">{{ __('Notifikasi') }}</span><a class="notif-action" href="{{ route('notifications.index') }}">{{ __('Lihat semua') }}</a></div>
                        <div class="notif-list">
                            @forelse($unreadList as $n)
                                <a class="notif-item is-unread" href="{{ $n->action_url ?? route('notifications.index') }}">
                                    <span class="notif-text">{{ $n->title }}<span class="notif-time">{{ $n->created_at?->diffForHumans() }}</span></span>
                                </a>
                            @empty
                                <div class="p-3 text-muted small text-center">{{ __('Tidak ada notifikasi baru.') }}</div>
                            @endforelse
                        </div>
                        <a class="notif-foot" href="{{ route('notifications.index') }}">{{ __('Buka pusat notifikasi') }}</a>
                    </div>
                </div>
                <button class="topbar-icon-btn d-none d-sm-inline-flex" data-theme-toggle aria-label="{{ __('Toggle dark mode') }}">
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
                            <div class="min-w-0"><strong>{{ Auth::user()->name }}</strong><small>{{ Auth::user()->email }}</small><span class="profile-role">{{ $isSuper ? 'Superadmin' : ($ws->users->firstWhere('id', Auth::id())->pivot->role ?? 'member') }}</span></div>
                        </div>
                        <div class="profile-links">
                            <a class="dropdown-item" href="{{ route('profile.edit') }}"><x-ti name="user" /> {{ __('Profile') }}</a>
                            <a class="dropdown-item" href="{{ route('workspace.settings') }}"><x-ti name="settings" /> {{ __('Business Settings') }}</a>
                            @if($isSuper)
                                <a class="dropdown-item" href="{{ route('admin.dashboard') }}"><x-ti name="shield-lock" /> {{ __('Platform Admin') }}</a>
                            @endif
                            <a class="dropdown-item" href="{{ route('docs') }}" target="_blank" rel="noopener"><x-ti name="book" /> {{ __('Documentation') }}</a>
                        </div>
                        <div class="profile-foot">
                            <a href="{{ route('support.index') }}">{{ __('Support') }}</a>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">@csrf<button type="submit" style="all:unset;cursor:pointer;width:100%;">{{ __('Log out') }}</button></form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <nav class="topbar-nav d-none d-xl-block" aria-label="{{ __('Navigasi utama') }}">
            <div class="app-container">
                <ul class="nav-menu" id="mainnav">
                    @foreach($groups as $g)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle{{ $groupActive($g) ? ' active' : '' }}" href="#" role="button"
                                data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false"
                                @if($groupActive($g)) aria-current="true" @endif>
                                <x-ti :name="$g['icon']" /><span>{{ $g['label'] }}</span>
                            </a>
                            @if(($g['type'] ?? '') === 'simple' || ($g['type'] ?? '') === 'simple-wide')
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

    <!-- ============ Main ============ -->
    <main class="app-main" id="main-content">
        <div class="app-container">
            @isset($breadcrumbs)
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3">
                        @foreach($breadcrumbs as $crumb)
                            @if(!empty($crumb['url']) && !$loop->last)
                                <li class="breadcrumb-item"><a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a></li>
                            @else
                                <li class="breadcrumb-item active" aria-current="page">{{ $crumb['label'] }}</li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
            @endisset
            <div class="page-header d-print-none">
                <div>
                    @isset($pretitle)<div class="page-pretitle">{{ $pretitle }}</div>@endisset
                    <h1 class="page-title">{{ $title ?? branding('app_name') }}</h1>
                    @isset($subtitle)<p class="page-subtitle">{{ $subtitle }}</p>@endisset
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
            <span>&copy; {{ date('Y') }} {{ branding('app_name') }}</span>
            <span><a href="{{ route('docs') }}">{{ __('Documentation') }}</a> · <a href="{{ route('support.index') }}">{{ __('Support') }}</a></span>
        </div>
    </footer>
</div>

<!-- Mobile drawer -->
<div class="offcanvas offcanvas-start mobile-nav" tabindex="-1" id="mobileNav" aria-label="{{ __('Navigasi') }}">
    <div class="offcanvas-header">
        <span class="brand-text"><span class="brand-name" style="color:var(--app-heading)">{{ branding('app_name') }}</span></span>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('Close') }}"></button>
    </div>
    <div class="offcanvas-body">
        <div class="mobile-user">
            <span class="avatar">{{ strtoupper(mb_substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
            <div><strong>{{ Auth::user()->name }}</strong><small>{{ $ws->name ?? '' }}</small></div>
        </div>
        <form method="GET" action="{{ route('invoices.index') }}" class="mb-2" role="search">
            <div class="input-icon">
                <span class="input-icon-addon"><x-ti name="search" /></span>
                <input type="search" name="search" class="form-control" placeholder="{{ __('Cari…') }}" aria-label="{{ __('Search') }}">
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
        @if($isSuper)
            <a class="mobile-nav-link" href="{{ route('admin.dashboard') }}"><x-ti name="shield-lock" /> {{ __('Platform Admin') }}</a>
        @endif
    </div>
</div>

@stack('scripts')
<script>
(function () {
    var input = document.getElementById('ws-global-search');
    var box = document.getElementById('ws-search-results');
    if (input) {
        document.addEventListener('keydown', function (e) {
            if ((e.key === '/' && !/INPUT|TEXTAREA|SELECT/.test(document.activeElement.tagName)) || (e.ctrlKey && e.key.toLowerCase() === 'k')) {
                e.preventDefault(); input.focus();
            }
        });
    }
    // Filter menu topbar saat mengetik (navigasi cepat).
    var links = Array.prototype.slice.call(document.querySelectorAll('#mainnav a.mega-link, #mainnav a.nav-dd-item'));
    if (input && box) {
        input.addEventListener('input', function () {
            var q = input.value.trim().toLowerCase();
            if (!q) { box.classList.remove('is-open'); box.innerHTML = ''; return; }
            var hits = links.filter(function (a) { return a.textContent.toLowerCase().indexOf(q) !== -1; }).slice(0, 7);
            box.innerHTML = hits.length
                ? '<div class="dropdown-header">{{ __('Menu') }}</div>' + hits.map(function (a) {
                    return '<a class="dropdown-item" href="' + a.href + '">' + a.querySelector('strong, span strong').textContent + '</a>';
                }).join('')
                : '<div class="dropdown-item disabled">{{ __('Tekan Enter untuk cari invoice') }}</div>';
            box.classList.toggle('is-open', true);
        });
        document.addEventListener('click', function (e) {
            if (!box.contains(e.target) && e.target !== input) box.classList.remove('is-open');
        });
    }
})();
</script>
</body>
</html>
