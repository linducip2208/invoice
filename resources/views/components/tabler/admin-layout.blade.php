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
                <ul class="navbar-nav pt-lg-3">
                    @php
                        $link = fn($route, $pattern, $icon, $label) => [
                            'href' => Route::has($route) ? route($route) : '#',
                            'active' => request()->routeIs($pattern),
                            'icon' => $icon, 'label' => $label,
                        ];
                        $sections = [
                            __('PLATFORM') => [
                                $link('admin.dashboard', 'admin.dashboard', 'layout-dashboard', __('Platform overview')),
                                $link('admin.workspaces.index', 'admin.workspaces.*', 'building', __('Workspaces')),
                                $link('admin.users.index', 'admin.users.*', 'users', __('Users')),
                            ],
                            __('REVENUE') => [
                                $link('admin.plans.index', 'admin.plans.*', 'wallet', __('Plans')),
                                $link('admin.subscriptions.index', 'admin.subscriptions.*', 'repeat', __('Subscriptions')),
                                $link('admin.manual-payments.index', 'admin.manual-payments.*', 'cash', __('Manual payments')),
                                $link('admin.coupons.index', 'admin.coupons.*', 'percentage', __('Coupons')),
                            ],
                            __('OPERATIONS') => [
                                $link('admin.branding.edit', 'admin.branding.*', 'palette', __('Branding')),
                                $link('admin.blog.index', 'admin.blog.*', 'file-text', __('Blog management')),
                                $link('dashboard', 'dashboard', 'arrow-left', __('Back to workspace')),
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
                </ul>
            </div>
        </div>
    </aside>

    <div class="page-wrapper">
        <header class="navbar navbar-expand-md d-print-none">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
                    aria-controls="sidebar-menu" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="navbar-nav flex-row order-md-last ms-auto">
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
                        <div class="page-pretitle">{{ __('Platform administration') }}</div>
                        <h2 class="page-title">{{ $title ?? __('Platform') }}</h2>
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

        <div class="page-body">
            <div class="container-xl">
                <x-tabler.alert />
                {{ $slot }}
            </div>
        </div>

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
</body>
</html>
