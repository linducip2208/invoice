<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($title ?? null) ? $title . ' — ' : '' }}{{ branding('app_name') }}</title>

    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#7c5cf0">
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
<div class="page">
    <!-- ============ Sidebar ============ -->
    <aside class="navbar navbar-vertical navbar-expand-lg invocely-nav d-print-none" data-bs-theme="dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
                aria-controls="sidebar-menu" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a href="{{ route('dashboard') }}" class="navbar-brand navbar-brand-autodark">
                <span class="logo-tile">I</span>
                <span class="font-display">{{ branding('app_name') }}</span>
            </a>
            <div class="collapse navbar-collapse" id="sidebar-menu">
                <ul class="navbar-nav pt-lg-3">
                    @php
                        $ws = workspace();
                        $isSuper = Auth::user()?->isSuperadmin();
                        $link = fn($route, $pattern, $icon, $label, $badge = null) => [
                            'href' => Route::has($route) ? route($route) : '#',
                            'active' => request()->routeIs($pattern),
                            'icon' => $icon, 'label' => $label, 'badge' => $badge,
                        ];
                        $sections = [
                            __('WORKSPACE') => [
                                $link('dashboard', 'dashboard', 'layout-dashboard', __('Dashboard')),
                                $link('notifications.index', 'notifications.*', 'bell', __('Notifications')),
                            ],
                            __('SALES') => [
                                $link('invoices.index', 'invoices.*', 'file-invoice', __('Invoices')),
                                $link('quotes.index', 'quotes.*', 'file-text', __('Quotations')),
                                $link('clients.index', 'clients.*', 'users', __('Customers')),
                                $link('products.index', 'products.*', 'package', __('Products & Services')),
                                $link('projects.index', 'projects.*', 'briefcase', __('Projects')),
                                $link('credit-notes.index', 'credit-notes.*', 'receipt-refund', __('Credit Notes')),
                            ],
                            __('CASH FLOW') => [
                                $link('payments.index', 'payments.*', 'credit-card', __('Payments')),
                                $link('reports.aging', 'reports.aging', 'hourglass', __('Outstanding Invoices')),
                                $link('recurring.index', 'recurring.*', 'repeat', __('Recurring Invoices')),
                                $link('expenses.index', 'expenses.*', 'receipt', __('Expenses')),
                                $link('bank-reconcile.index', 'bank-reconcile.*', 'building-bank', __('Reconciliation')),
                                $link('accounting-exports.index', 'accounting-exports.*', 'file-export', __('Accounting Exports')),
                            ],
                            __('INSIGHTS') => [
                                $link('reports.index', 'reports.index', 'chart-bar', __('Reports')),
                                $link('reports.cashflow', 'reports.cashflow', 'chart-line', __('Cash Flow')),
                                $link('reports.pl', 'reports.pl', 'chart-pie', __('Profit & Loss')),
                                $link('reports.tax', 'reports.tax', 'percentage', __('Tax Reports')),
                            ],
                            __('AUTOMATION') => [
                                $link('ai-providers.index', 'ai-providers.*', 'robot', __('AI Providers')),
                                $link('expenses.ocr.upload', 'expenses.ocr.*', 'scan', __('OCR Receipt')),
                                $link('report-subscriptions.index', 'report-subscriptions.*', 'send', __('Scheduled Reports')),
                            ],
                            __('CONFIGURATION') => [
                                $link('workspace.settings', 'workspace.*', 'settings', __('Company Settings')),
                                $link('team.index', 'team.*', 'users', __('Team & Roles')),
                                $link('invoice-layouts.index', 'invoice-layouts.*', 'template', __('Invoice Templates')),
                                $link('gateways.index', 'gateways.*', 'plug', __('Payment Gateways')),
                                $link('api-tokens.index', 'api-tokens.*', 'key', __('API Tokens')),
                                $link('tenant-webhooks.index', 'tenant-webhooks.*', 'link', __('Webhooks Out')),
                                $link('audit-logs.index', 'audit-logs.*', 'history', __('Audit Logs')),
                                $link('custom-domain.show', 'custom-domain.*', 'globe', __('Custom Domain')),
                                $link('clients.import.show', 'clients.import.*', 'upload', __('CSV Import')),
                                $link('exports.index', 'exports.*', 'download', __('Exports')),
                            ],
                            __('BILLING') => [
                                $link('billing.show', 'billing.*', 'wallet', __('Current Plan')),
                                $link('affiliate.index', 'affiliate.*', 'star', __('Affiliate')),
                            ],
                            __('HELP') => [
                                $link('docs', 'docs', 'book', __('Documentation')),
                                $link('support.index', 'support.*', 'lifebuoy', __('Support')),
                            ],
                        ];
                    @endphp
                    @foreach($sections as $heading => $items)
                        <li class="nav-item navbar-heading">{{ $heading }}</li>
                        @foreach($items as $item)
                            <li class="nav-item{{ $item['active'] ? ' active' : '' }}">
                                <a class="nav-link{{ $item['active'] ? ' active' : '' }}" href="{{ $item['href'] }}"
                                    @if($item['active']) aria-current="page" @endif>
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <x-ti :name="$item['icon']" />
                                    </span>
                                    <span class="nav-link-title">{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    @endforeach
                    @if($isSuper)
                        <li class="nav-item navbar-heading">{{ __('Platform') }}</li>
                        <li class="nav-item{{ request()->routeIs('admin.*') ? ' active' : '' }}">
                            <a class="nav-link{{ request()->routeIs('admin.*') ? ' active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <x-ti name="shield-lock" />
                                </span>
                                <span class="nav-link-title">{{ __('Platform Admin') }}</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </aside>

    <!-- ============ Main ============ -->
    <div class="page-wrapper">
        <header class="navbar navbar-expand-md d-print-none">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
                    aria-controls="sidebar-menu" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="navbar-nav flex-row order-md-last ms-auto">
                    <div class="nav-item dropdown me-2 d-flex">
                        <a href="#" class="btn btn-primary d-flex align-items-center gap-1" data-bs-toggle="dropdown"
                            aria-label="{{ __('Quick create') }}" aria-expanded="false">
                            <x-ti name="plus" /> <span class="d-none d-sm-inline">{{ __('New') }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('invoices.create') }}">{{ __('New Invoice') }}</a>
                            <a class="dropdown-item" href="{{ route('quotes.create') }}">{{ __('New Quotation') }}</a>
                            <a class="dropdown-item" href="{{ route('clients.create') }}">{{ __('New Customer') }}</a>
                        </div>
                    </div>
                    @php $unreadCount = \App\Models\AppNotification::unread()->count(); @endphp
                    <div class="nav-item me-2">
                        <a href="{{ route('notifications.index') }}" class="btn btn-icon position-relative" aria-label="{{ __('Notifications') }}">
                            <x-ti name="bell" />
                            @if($unreadCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-red-lt">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                            @endif
                        </a>
                    </div>
                    @if($ws)
                        <div class="nav-item dropdown me-2 d-flex">
                            <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown"
                                aria-label="{{ __('Switch workspace') }}" aria-expanded="false">
                                <span class="avatar avatar-sm">{{ strtoupper(mb_substr($ws->name ?? 'W', 0, 1)) }}</span>
                                <span class="d-none d-xl-block ps-2">
                                    <span class="d-block small text-muted">{{ __('Workspace') }}</span>
                                    <span class="d-block fw-semibold text-truncate" style="max-width: 10rem;">{{ $ws->name }}</span>
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <span class="dropdown-header">{{ __('Switch workspace') }}</span>
                                @foreach(Auth::user()->workspaces as $w)
                                    <form method="POST" action="{{ route('workspace.switch', $w) }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item{{ $w->id === $ws->id ? ' active' : '' }}">
                                            {{ $w->name }}
                                        </button>
                                    </form>
                                @endforeach
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('workspace.settings') }}">{{ __('Business Settings') }}</a>
                            </div>
                        </div>
                    @endif
                    <div class="nav-item me-2">
                        <button class="btn btn-icon" data-theme-toggle aria-label="{{ __('Toggle dark mode') }}">
                            <x-ti name="sparkles" />
                        </button>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 p-0" data-bs-toggle="dropdown" aria-label="{{ __('Account menu') }}" aria-expanded="false">
                            <span class="avatar avatar-sm">{{ strtoupper(mb_substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                            <span class="d-none d-xl-block ps-2">
                                <span class="d-block small text-muted">{{ Auth::user()->email }}</span>
                                <span class="d-block fw-semibold">{{ Auth::user()->name }}</span>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a>
                            <a class="dropdown-item" href="{{ route('workspace.settings') }}">{{ __('Business Settings') }}</a>
                            <a class="dropdown-item" href="{{ route('docs') }}" target="_blank" rel="noopener">{{ __('Documentation') }}</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">{{ __('Log out') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        @isset($breadcrumbs)
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-1">
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
                        @isset($pretitle)
                            <div class="page-pretitle">{{ $pretitle }}</div>
                        @endisset
                        <h1 class="page-title">{{ $title ?? branding('app_name') }}</h1>
                        @isset($subtitle)
                            <div class="text-muted mt-1">{{ $subtitle }}</div>
                        @endisset
                    </div>
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            {{ $actions ?? '' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <main class="page-body" id="main-content">
            <div class="container-xl">
                <x-tabler.alert />
                {{ $slot }}
            </div>
        </main>

        <footer class="footer footer-transparent d-print-none">
            <div class="container-xl">
                <div class="row text-center align-items-center flex-row-reverse">
                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item"><a href="{{ route('docs') }}">{{ __('Documentation') }}</a></li>
                            <li class="list-inline-item"><a href="{{ route('support.index') }}">{{ __('Support') }}</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-auto ms-lg-auto">
                        &copy; {{ date('Y') }} {{ branding('app_name') }}
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>
@stack('scripts')
</body>
</html>
