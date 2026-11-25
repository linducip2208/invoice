<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($title ?? null) ? $title . ' — ' : '' }}{{ __('Platform Admin') }} · {{ branding('app_name') }}</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
    <aside class="navbar navbar-vertical navbar-expand-lg invocely-nav d-print-none" data-bs-theme="dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
                aria-controls="sidebar-menu" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="navbar-brand navbar-brand-autodark">
                <span class="logo-tile">I</span>
                <span class="font-display">{{ __('Platform') }}</span>
                <span class="badge bg-red-lt ms-1">{{ __('Admin') }}</span>
            </a>
            <div class="collapse navbar-collapse" id="sidebar-menu">
                <ul class="navbar-nav pt-lg-3" id="admin-nav">
                    @php
                        $isActive = fn($p) => request()->routeIs($p);
                        $navGroups = [
                            ['key' => 'overview', 'label' => __('OVERVIEW'), 'items' => [
                                ['href' => route('admin.dashboard'), 'pattern' => 'admin.dashboard', 'icon' => 'layout-dashboard', 'label' => __('Dashboard')],
                                ['href' => route('admin.analytics'), 'pattern' => 'admin.analytics', 'icon' => 'chart-line', 'label' => __('Analytics')],
                            ]],
                            ['key' => 'saas', 'label' => __('SAAS MANAGEMENT'), 'items' => [
                                ['href' => route('admin.users.index'), 'pattern' => 'admin.users.*', 'icon' => 'users', 'label' => __('Users')],
                                ['href' => route('admin.workspaces.index'), 'pattern' => 'admin.workspaces.*', 'icon' => 'building', 'label' => __('Workspaces')],
                                ['href' => route('admin.plans.index'), 'pattern' => 'admin.plans.*', 'icon' => 'wallet', 'label' => __('Plans')],
                                ['href' => route('admin.subscriptions.index'), 'pattern' => 'admin.subscriptions.*', 'icon' => 'repeat', 'label' => __('Subscriptions')],
                            ]],
                            ['key' => 'revenue', 'label' => __('REVENUE'), 'items' => [
                                ['href' => route('admin.manual-payments.index'), 'pattern' => 'admin.manual-payments.*', 'icon' => 'cash', 'label' => __('Manual Payments')],
                                ['href' => route('admin.coupons.index'), 'pattern' => 'admin.coupons.*', 'icon' => 'percentage', 'label' => __('Coupons')],
                                ['href' => route('admin.revenue'), 'pattern' => 'admin.revenue', 'icon' => 'report-money', 'label' => __('Revenue Reports')],
                            ]],
                            ['key' => 'config', 'label' => __('CONFIGURATION'), 'items' => [
                                ['href' => route('admin.branding.edit'), 'pattern' => 'admin.branding.*', 'icon' => 'palette', 'label' => __('Branding')],
                                ['href' => route('admin.settings'), 'pattern' => 'admin.settings', 'icon' => 'settings', 'label' => __('Settings')],
                                ['href' => route('admin.audit-logs'), 'pattern' => 'admin.audit-logs', 'icon' => 'scroll', 'label' => __('Audit Logs')],
                                ['href' => route('admin.blog.index'), 'pattern' => 'admin.blog.*', 'icon' => 'file-text', 'label' => __('Blog management')],
                                ['href' => route('dashboard'), 'pattern' => 'dashboard', 'icon' => 'arrow-left', 'label' => __('Back to workspace')],
                            ]],
                        ];
                    @endphp
                    @foreach($navGroups as $g)
                        <details class="admin-nav-group" open data-nav-group="{{ $g['key'] }}">
                            <summary aria-label="{{ $g['label'] }}"><span>{{ $g['label'] }}</span><x-ti name="chevron-down" class="chev" /></summary>
                            @foreach($g['items'] as $item)
                                <li class="nav-item{{ $isActive($item['pattern']) ? ' active' : '' }}" data-nav-label="{{ strtolower($item['label']) }}">
                                    <a class="nav-link{{ $isActive($item['pattern']) ? ' active' : '' }}" href="{{ $item['href'] }}"
                                        @if($isActive($item['pattern'])) aria-current="page" @endif>
                                        <span class="nav-link-icon d-md-none d-lg-inline-block"><x-ti :name="$item['icon']" /></span>
                                        <span class="nav-link-title">{{ $item['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </details>
                    @endforeach
                </ul>
                <p class="text-muted small px-3 mt-2 d-none" id="admin-nav-empty">{{ __('No menu matches your search.') }}</p>
            </div>
        </div>
    </aside>

    <div class="page-wrapper">
        <header class="navbar navbar-expand-md d-print-none border-bottom">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
                    aria-controls="sidebar-menu" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <nav aria-label="breadcrumb" class="d-none d-md-block">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Platform') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $title ?? __('Overview') }}</li>
                    </ol>
                </nav>
                <div class="navbar-nav flex-row order-md-last ms-auto gap-2 align-items-center">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="d-none d-lg-block" role="search" aria-label="{{ __('Global search') }}">
                        <div class="input-icon">
                            <span class="input-icon-addon"><x-ti name="search" /></span>
                            <input type="search" name="search" value="{{ request('search') }}" class="form-control form-control-sm" style="min-width:14rem;"
                                placeholder="{{ __('Search users, workspaces…') }}" aria-label="{{ __('Global search') }}" list="admin-search-hints">
                            <datalist id="admin-search-hints">
                                <option value="superadmin"></option>
                                <option value="pending"></option>
                                <option value="pro"></option>
                            </datalist>
                        </div>
                    </form>
                    @php
                        $notifPending = \App\Models\Subscription::where('status', 'pending')->whereNotNull('manual_proof_path')->count();
                        $notifSubs = \App\Models\Subscription::where('status', 'pending')->count();
                    @endphp
                    <div class="nav-item dropdown">
                        <button class="btn btn-icon" data-bs-toggle="dropdown" aria-label="{{ __('Notification center') }}" aria-expanded="false">
                            <x-ti name="bell" />
                            @if($notifSubs > 0)<span class="badge bg-red position-absolute top-0 start-100 translate-middle">{{ min($notifSubs, 9) }}</span>@endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" style="min-width:19rem;">
                            <h4 class="dropdown-header">{{ __('Notification center') }}</h4>
                            <a class="dropdown-item" href="{{ route('admin.manual-payments.index') }}">
                                <x-ti name="cash" /> {{ __('Pending manual payments') }}
                                <span class="badge bg-yellow-lt ms-1">{{ $notifPending }}</span>
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.subscriptions.index', ['status' => 'pending']) }}">
                                <x-ti name="repeat" /> {{ __('Pending subscriptions') }}
                                <span class="badge bg-azure-lt ms-1">{{ $notifSubs }}</span>
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.revenue') }}"><x-ti name="report-money" /> {{ __('Revenue reports') }}</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-muted" href="{{ route('admin.audit-logs') }}"><x-ti name="scroll" /> {{ __('Recent admin activity') }}</a>
                        </div>
                    </div>
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
                            <a class="dropdown-item" href="{{ route('dashboard') }}">{{ __('Workspace') }}</a>
                            <a class="dropdown-item" href="{{ route('admin.settings') }}">{{ __('Settings') }}</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">{{ __('Log out') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-xl d-lg-none mt-2">
                <form method="GET" action="{{ route('admin.users.index') }}" role="search" aria-label="{{ __('Global search') }}">
                    <div class="input-icon w-100">
                        <span class="input-icon-addon"><x-ti name="search" /></span>
                        <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Search users, workspaces…') }}" aria-label="{{ __('Global search') }}">
                    </div>
                </form>
            </div>
        </header>

        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <div class="page-pretitle">{{ __('Platform administration') }}</div>
                        <h1 class="page-title">{{ $title ?? __('Platform') }}</h1>
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
                <div class="row text-center align-items-center">
                    <div class="col-lg-auto ms-lg-auto">
                        &copy; {{ date('Y') }} {{ branding('app_name') }} · {{ __('Platform') }}
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>
@stack('scripts')
<script>
(function () {
    var input = document.querySelector('header input[name="search"]');
    var items = Array.prototype.slice.call(document.querySelectorAll('#admin-nav [data-nav-label]'));
    var empty = document.getElementById('admin-nav-empty');
    if (!input || !items.length) return;
    input.addEventListener('input', function () {
        var q = input.value.trim().toLowerCase();
        var visible = 0;
        items.forEach(function (li) {
            var hit = !q || (li.getAttribute('data-nav-label') || '').indexOf(q) !== -1;
            li.style.display = hit ? '' : 'none';
            if (hit) visible++;
        });
        if (empty) empty.classList.toggle('d-none', visible !== 0);
    });
})();
</script>
</body>
</html>
