<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} — {{ branding('app_name') }} Admin ✦</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-branding-head />
</head>
<body class="font-sans antialiased text-purple-900">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen">

        {{-- Mobile sidebar overlay --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            x-transition.opacity
            @click="sidebarOpen = false"
            class="fixed inset-0 z-20 bg-purple-900/40 backdrop-blur-sm lg:hidden">
        </div>

        {{-- Sidebar --}}
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-30 w-64 transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:z-auto flex flex-col shrink-0"
            style="background: linear-gradient(180deg, rgba(139,92,246,0.15) 0%, rgba(236,72,153,0.10) 100%); border-right: 1px solid rgba(196,181,253,0.4);">

            <div class="rainbow-bar"></div>
            <div class="p-5 flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <span class="logo-glow flex items-center justify-center" style="padding: 4px;">
                        @if(branding('logo_path'))
                            <img src="{{ asset('storage/' . branding('logo_path')) }}" alt="logo" class="h-8 w-auto">
                        @else
                            <x-application-logo size="sm" />
                        @endif
                    </span>
                    <span class="font-display font-bold text-lg text-rainbow tracking-tight">{{ branding('app_name') }}</span>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden p-1 rounded-md text-purple-400 hover:text-pink-500 hover:bg-pink-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <p class="text-[10px] text-purple-500 px-5 -mt-1 font-bold uppercase tracking-widest">✦ Superadmin Panel</p>

            <nav class="flex-1 px-3 py-2 space-y-1 overflow-y-auto">
                @php
                    $items = [
                        ['route' => 'admin.dashboard',           'label' => 'Dashboard',     'icon' => '📊'],
                        ['route' => 'admin.plans.index',         'label' => 'Plans',         'icon' => '💎'],
                        ['route' => 'admin.subscriptions.index', 'label' => 'Subscriptions', 'icon' => '🔄'],
                        ['route' => 'admin.subscriptions.manual', 'label' => 'Manual Subscription ⚡', 'icon' => '⚡'],
                        ['route' => 'admin.users.index',         'label' => 'Users',         'icon' => '👥'],
                        ['route' => 'admin.workspaces.index',    'label' => 'Workspaces',    'icon' => '🏢'],
                        ['route' => 'admin.branding.edit',       'label' => 'Branding & Theme', 'icon' => '🎨'],
                        ['route' => 'admin.coupons.index',       'label' => 'Coupons', 'icon' => '🎟️'],
                        ['route' => 'admin.manual-payments.index', 'label' => 'Manual Payments', 'icon' => '💸'],
                    ];
                @endphp
                @foreach($items as $item)
                    @php $active = request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])); @endphp
                    <a href="{{ route($item['route']) }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ $active ? 'bg-white/80 text-purple-900 shadow-sm' : 'text-purple-700 hover:bg-white/50' }}">
                        <span>{{ $item['icon'] }}</span>{{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="p-4 border-t border-purple-200/50">
                <p class="text-xs text-purple-600 truncate">{{ Auth::user()?->email }}</p>
                <div class="flex gap-2 mt-2">
                    <a href="{{ route('dashboard') }}" class="flex-1 text-xs text-center px-2 py-1.5 rounded-lg bg-white/70 text-purple-700 font-semibold hover:bg-white">→ App</a>
                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button class="w-full text-xs text-center px-2 py-1.5 rounded-lg bg-rose-100 text-rose-700 font-semibold hover:bg-rose-200">Logout</button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col min-w-0">
            <header class="z-10 shrink-0" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(196,181,253,0.35);">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = true" class="lg:hidden p-1.5 rounded-lg text-purple-400 hover:text-pink-500 hover:bg-pink-50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h1 class="font-display font-bold text-lg sm:text-xl text-purple-900 truncate">{{ $title ?? '' }}</h1>
                    </div>
                    <div class="text-xs text-purple-500 hidden sm:block">{{ now()->format('d M Y, H:i') }}</div>
                </div>
            </header>

            @if(session('success') || session('error'))
                <div class="px-4 sm:px-6 pt-4">
                    @if(session('success'))<div class="card-unicorn bg-emerald-50 border-emerald-200"><span>🦄</span> <span class="text-sm font-semibold text-emerald-800">{{ session('success') }}</span></div>@endif
                    @if(session('error'))<div class="card-unicorn bg-rose-50 border-rose-200"><span>⚠️</span> <span class="text-sm font-semibold text-rose-700">{{ session('error') }}</span></div>@endif
                </div>
            @endif

            <main class="flex-1 overflow-y-auto p-4 sm:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Mobile bottom spacer (admin doesn't need bottom nav but needs safe area) --}}
    <div class="lg:hidden h-4" style="padding-bottom: env(safe-area-inset-bottom);"></div>
</body>
</html>
