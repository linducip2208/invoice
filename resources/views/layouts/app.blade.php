<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@hasSection('title') @yield('title') — @endif {{ branding('app_name') }} ✦</title>

    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#A78BFA">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Invocely">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-branding-head />

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(e => console.warn('SW reg failed', e));
            });
        }
    </script>
</head>
<body class="font-sans antialiased text-purple-900">
    <div x-data="{ sidebarOpen: false, userMenuOpen: false }" class="flex h-screen">

        <!-- Mobile sidebar overlay -->
        <div
            x-show="sidebarOpen"
            x-cloak
            x-transition.opacity
            @click="sidebarOpen = false"
            class="fixed inset-0 z-20 bg-purple-900/40 backdrop-blur-sm lg:hidden">
        </div>

        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-30 w-64 transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:z-auto flex flex-col"
            style="background: linear-gradient(180deg, rgba(255,255,255,0.92) 0%, rgba(255,244,251,0.92) 50%, rgba(237,233,254,0.92) 100%); backdrop-filter: blur(16px); border-right: 1px solid rgba(196, 181, 253, 0.4);">

            <div class="rainbow-bar"></div>

            <!-- Sidebar header -->
            <div class="flex items-center justify-between h-16 px-4 shrink-0" style="border-bottom: 1px solid rgba(196, 181, 253, 0.3);">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                    <span class="logo-glow flex items-center justify-center animate-unicorn-float" style="padding: 4px;">
                        <x-application-logo size="sm" class="drop-shadow" />
                    </span>
                    <span class="font-display font-bold text-lg text-rainbow tracking-tight">{{ branding('app_name') }}</span>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden p-1 rounded-md text-purple-400 hover:text-pink-500 hover:bg-pink-50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Sidebar navigation -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                <x-nav-sidebar :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
                    {{ __('Dashboard') }}
                </x-nav-sidebar>

                <div class="pt-5 pb-1 px-3">
                    <p class="text-[10px] font-bold text-purple-400 uppercase tracking-[0.2em] flex items-center gap-1.5">
                        <span class="text-pink-400">✦</span> {{ __('Manage') }}
                    </p>
                </div>

                <x-nav-sidebar :href="route('clients.index')" :active="request()->routeIs('clients.*')" icon="users">
                    {{ __('Clients') }}
                </x-nav-sidebar>

                <x-nav-sidebar :href="route('products.index')" :active="request()->routeIs('products.*')" icon="box">
                    {{ __('Products') }}
                </x-nav-sidebar>

                <x-nav-sidebar :href="route('invoices.index')" :active="request()->routeIs('invoices.*')" icon="document">
                    {{ __('Invoices') }}
                </x-nav-sidebar>

                <x-nav-sidebar :href="route('payments.index')" :active="request()->routeIs('payments.*')" icon="credit-card">
                    {{ __('Payments') }}
                </x-nav-sidebar>

                <x-nav-sidebar :href="route('recurring.index')" :active="request()->routeIs('recurring.*')" icon="refresh">
                    {{ __('Recurring') }}
                </x-nav-sidebar>

                <div class="pt-5 pb-1 px-3">
                    <p class="text-[10px] font-bold text-purple-400 uppercase tracking-[0.2em] flex items-center gap-1.5">
                        <span class="text-sky-400">✦</span> {{ __('Insights') }}
                    </p>
                </div>

                <x-nav-sidebar :href="route('reports.index')" :active="request()->routeIs('reports.*')" icon="chart">
                    {{ __('Reports') }}
                </x-nav-sidebar>

                <x-nav-sidebar :href="route('workspace.settings')" :active="request()->routeIs('workspace.*')" icon="cog">
                    {{ __('Workspace') }}
                </x-nav-sidebar>

                <x-nav-sidebar :href="route('gateways.index')" :active="request()->routeIs('gateways.*')" icon="credit-card">
                    {{ __('Payment Gateway') }}
                </x-nav-sidebar>

                <x-nav-sidebar :href="route('billing.show')" :active="request()->routeIs('billing.*')" icon="credit-card">
                    💳 {{ __('Billing & Plan') }}
                </x-nav-sidebar>

                <div class="pt-5 pb-1 px-3">
                    <p class="text-[10px] font-bold text-purple-400 uppercase tracking-[0.2em] flex items-center gap-1.5">
                        <span class="text-emerald-400">✦</span> {{ __('Productivity') }}
                    </p>
                </div>
                @php $hasBuilder = workspace()?->planFeature('drag_drop_builder'); @endphp
                <x-nav-sidebar :href="$hasBuilder ? route('invoice-layouts.index') : route('billing.show')" :active="request()->routeIs('invoice-layouts.*')" icon="document">
                    🎨 {{ __('Invoice Builder') }}
                    @if(! $hasBuilder)<span class="ml-auto text-[9px] text-amber-500 font-bold">PRO+</span>@else<span class="ml-auto text-[9px] text-emerald-500 font-bold">●</span>@endif
                </x-nav-sidebar>
                <x-nav-sidebar :href="$hasBuilder ? route('invoice-layouts.index') : route('billing.show')" icon="document">
                    📜 {{ __('Custom Print Layouts') }}
                </x-nav-sidebar>
                <x-nav-sidebar :href="route('time-entries.index')" :active="request()->routeIs('time-entries.*')" icon="document">⏱️ {{ __('Time Tracking') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('expenses.index')"     :active="request()->routeIs('expenses.*')" icon="document">🧾 {{ __('Expenses') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('quotes.index')"       :active="request()->routeIs('quotes.*')" icon="document">📋 {{ __('Quotes') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('projects.index')"     :active="request()->routeIs('projects.*')" icon="box">📁 {{ __('Projects') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('credit-notes.index')" :active="request()->routeIs('credit-notes.*')" icon="document">↩️ {{ __('Credit Notes') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('reports.aging')"      :active="request()->routeIs('reports.aging')" icon="chart">⏳ {{ __('Aging Report') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('reports.cashflow')"   :active="request()->routeIs('reports.cashflow')" icon="chart">📈 {{ __('Cashflow Forecast') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('reports.pl')"         :active="request()->routeIs('reports.pl')" icon="chart">💰 {{ __('P&L per Klien') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('reports.tax')"        :active="request()->routeIs('reports.tax')" icon="chart">🏛️ {{ __('Tax Summary') }}</x-nav-sidebar>

                <div class="pt-5 pb-1 px-3">
                    <p class="text-[10px] font-bold text-purple-400 uppercase tracking-[0.2em] flex items-center gap-1.5">
                        <span class="text-amber-400">✦</span> {{ __('Automation') }}
                    </p>
                </div>
                <x-nav-sidebar :href="route('ai-providers.index')"   :active="request()->routeIs('ai-providers.*')" icon="sparkle">🤖 {{ __('AI Providers') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('feature.coming', 'email-templates')" icon="document">✉️ {{ __('Email Templates') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('feature.coming', 'bulk')"            icon="document">📦 {{ __('Bulk Actions') }}</x-nav-sidebar>

                <div class="pt-5 pb-1 px-3">
                    <p class="text-[10px] font-bold text-purple-400 uppercase tracking-[0.2em] flex items-center gap-1.5">
                        <span class="text-rose-400">✦</span> {{ __('Team & Access') }}
                    </p>
                </div>
                <x-nav-sidebar :href="route('team.index')"        :active="request()->routeIs('team.*')" icon="users">👥 {{ __('Team Members') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('approvals.index')"     :active="request()->routeIs('approvals.*') || request()->routeIs('approval-rules.*')" icon="document">🛡️ {{ __('Approvals') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('feature.coming', 'client-portal')" icon="users">🪟 {{ __('Client Portal') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('2fa.show')"            :active="request()->routeIs('2fa.*')" icon="document">🔐 {{ __('2FA Security') }}</x-nav-sidebar>

                <div class="pt-5 pb-1 px-3">
                    <p class="text-[10px] font-bold text-purple-400 uppercase tracking-[0.2em] flex items-center gap-1.5">
                        <span class="text-emerald-400">✦</span> {{ __('Finance Tools') }}
                    </p>
                </div>
                <x-nav-sidebar :href="route('bank-reconcile.index')"   :active="request()->routeIs('bank-reconcile.*')" icon="credit-card">🏦 {{ __('Bank Reconcile') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('accounting-exports.index')" :active="request()->routeIs('accounting-exports.*')" icon="document">📊 {{ __('Accounting Export') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('coupons.index')"          :active="request()->routeIs('coupons.*')" icon="document">🎟️ {{ __('Coupons') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('expenses.ocr.upload')"    icon="document">📸 {{ __('OCR Receipt') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('reports.tax')"            :active="request()->routeIs('reports.tax')" icon="document">🏛️ {{ __('Tax (e-Faktur)') }}</x-nav-sidebar>

                <div class="pt-5 pb-1 px-3">
                    <p class="text-[10px] font-bold text-purple-400 uppercase tracking-[0.2em] flex items-center gap-1.5">
                        <span class="text-indigo-400">✦</span> {{ __('Advanced') }}
                    </p>
                </div>
                <x-nav-sidebar :href="route('audit-logs.index')"       :active="request()->routeIs('audit-logs.*')" icon="document">📜 {{ __('Audit Log') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('notifications.index')"    :active="request()->routeIs('notifications.*')" icon="document">🔔 {{ __('Notifications') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('feature.coming', 'activity')"         icon="document">📰 {{ __('Activity Feed') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('dashboard.customize')"    :active="request()->routeIs('dashboard.customize')" icon="cog">🎨 {{ __('Customize Dashboard') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('report-subscriptions.index')" :active="request()->routeIs('report-subscriptions.*')" icon="document">📨 {{ __('Email Reports') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('feature.coming', 'attachments')"  icon="document">📎 {{ __('Attachments') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('clients.import.show')"    icon="document">📥 {{ __('CSV Import') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('exports.index')"          :active="request()->routeIs('exports.*')" icon="document">📊 {{ __('Export CSV') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('api-tokens.index')"       :active="request()->routeIs('api-tokens.*')" icon="document">🔌 {{ __('REST API') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('tenant-webhooks.index')"  :active="request()->routeIs('tenant-webhooks.*')" icon="document">🪝 {{ __('Webhooks Out') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('custom-domain.show')"     :active="request()->routeIs('custom-domain.*')" icon="document">🌐 {{ __('Custom Domain') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('feature.coming', 'fx-rates')"         icon="document">💱 {{ __('Exchange Rates') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('affiliate.index')"        :active="request()->routeIs('affiliate.*')" icon="document">🤝 {{ __('Affiliate') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('support.index')"          :active="request()->routeIs('support.*')" icon="document">💬 {{ __('Support') }}</x-nav-sidebar>
                <x-nav-sidebar :href="route('admin.blog.index')"    :active="request()->routeIs('admin.blog.*')" icon="document">📝 {{ __('Blog Admin') }}</x-nav-sidebar>

                @if(Auth::user()?->isSuperadmin())
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-semibold text-pink-600 hover:bg-pink-50 transition mt-3 border border-pink-200">
                        <span>👑</span> {{ __('Superadmin') }}
                    </a>
                @endif
            </nav>

            <!-- Sidebar footer -->
            <div class="p-4 shrink-0" style="border-top: 1px solid rgba(196, 181, 253, 0.3);">
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gradient-to-br from-pink-50 via-purple-50 to-sky-50 border border-purple-100">
                    <div class="relative">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center shadow-unicorn-pink" style="background: linear-gradient(135deg, #FF7AC6, #A78BFA, #7DD3FC);">
                            <span class="text-xs font-bold text-white">{{ strtoupper(substr(workspace()?->name ?? 'W', 0, 2)) }}</span>
                        </div>
                        <span class="absolute -top-1 -right-1 text-[10px] animate-unicorn-twinkle">✦</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-purple-900 truncate">{{ workspace()?->name ?? 'Workspace' }}</p>
                        <p class="text-[11px] text-purple-500 font-medium">{{ workspace()?->currency ?? 'IDR' }} · ✨ Magical</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content area -->
        <div class="flex-1 flex flex-col min-w-0 lg:ml-0">

            <!-- Top navbar -->
            <header class="z-10 shrink-0" style="background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(196, 181, 253, 0.35);">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = true" class="lg:hidden p-1.5 rounded-lg text-purple-400 hover:text-pink-500 hover:bg-pink-50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        @php $allWorkspaces = Auth::user()?->workspaces ?? collect(); @endphp
                        <div class="hidden sm:flex items-center gap-2 relative" x-data="{ wsOpen: false }" @click.away="wsOpen = false">
                            <span class="text-xs font-medium text-purple-400 uppercase tracking-wider">{{ __('Workspace') }}</span>
                            <button type="button" @click="wsOpen = !wsOpen" class="badge-unicorn cursor-pointer flex items-center gap-1">
                                <span>✦</span>
                                {{ workspace()?->name ?? '—' }}
                                @if($allWorkspaces->count() > 1)<span class="text-[10px]">▼</span>@endif
                            </button>
                            @if($allWorkspaces->count() > 1)
                            <div x-show="wsOpen" x-cloak class="absolute top-full mt-2 left-0 w-56 rounded-2xl py-1 z-50" style="background: rgba(255,255,255,0.97); backdrop-filter: blur(14px); border: 1px solid rgba(196,181,253,0.4); box-shadow: 0 20px 50px -12px rgba(167,139,250,0.4);">
                                @foreach($allWorkspaces as $w)
                                    <form method="POST" action="{{ route('workspace.switch', $w) }}">@csrf
                                        <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm font-medium text-purple-700 hover:bg-pink-50 transition {{ workspace()?->id === $w->id ? 'bg-purple-50' : '' }}">
                                            <span>✦</span> {{ $w->name }}
                                            @if(workspace()?->id === $w->id)<span class="ml-auto text-pink-500">✓</span>@endif
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3">
                        <a href="{{ route('invoices.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-full text-white transition-all duration-200 hover:-translate-y-0.5"
                           style="background: linear-gradient(135deg, #FF7AC6, #A78BFA); box-shadow: 0 6px 16px -4px rgba(167, 139, 250, 0.5);">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            {{ __('New Invoice') }}
                        </a>

                        <a href="{{ route('workspace.settings') }}" class="p-2 rounded-full text-purple-400 hover:text-pink-500 hover:bg-pink-50 transition" title="{{ __('Settings') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </a>

                        <div class="relative" @click.away="userMenuOpen = false">
                            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 p-1 pr-2.5 rounded-full hover:bg-purple-50 transition border border-transparent hover:border-purple-100">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-unicorn-pink" style="background: linear-gradient(135deg, #FF7AC6, #A78BFA);">
                                    <span class="text-sm font-bold text-white">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                </div>
                                <span class="hidden sm:block text-sm font-semibold text-purple-700">{{ Auth::user()->name }}</span>
                                <svg class="hidden sm:block w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div
                                x-show="userMenuOpen"
                                x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-56 rounded-2xl py-1 z-50 overflow-hidden"
                                style="background: rgba(255,255,255,0.97); backdrop-filter: blur(14px); border: 1px solid rgba(196, 181, 253, 0.4); box-shadow: 0 20px 50px -12px rgba(167, 139, 250, 0.4);">
                                <div class="px-4 py-3 bg-gradient-to-r from-pink-50 via-purple-50 to-sky-50">
                                    <p class="text-sm font-semibold text-purple-900 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-purple-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-medium text-purple-700 hover:bg-pink-50 transition">
                                    <svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    {{ __('Profile') }}
                                </a>
                                <a href="{{ route('workspace.settings') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-medium text-purple-700 hover:bg-pink-50 transition">
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ __('Workspace') }}
                                </a>
                                <a href="{{ route('docs') }}" target="_blank" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-medium text-purple-700 hover:bg-pink-50 transition">
                                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    {{ __('Docs') }} ↗
                                </a>
                                <div class="my-1 mx-3 h-px bg-gradient-to-r from-transparent via-purple-200 to-transparent"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2.5 text-left px-4 py-2.5 text-sm font-medium text-rose-500 hover:bg-rose-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Heading -->
            @isset($header)
                <div class="z-0" style="background: rgba(255, 255, 255, 0.5); backdrop-filter: blur(8px); border-bottom: 1px solid rgba(196, 181, 253, 0.25);">
                    <div class="px-4 sm:px-6 py-4">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <!-- Flash messages -->
            @if (session('success') || session('error') || session('status'))
                <div class="px-4 sm:px-6 pt-4">
                    @if (session('success'))
                        <div class="card-unicorn flex items-center gap-3" style="background: linear-gradient(90deg, rgba(220,252,231,0.7), rgba(187,247,208,0.5)); border-color: rgba(134,239,172,0.5);">
                            <span class="text-2xl">🦄</span>
                            <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="card-unicorn flex items-center gap-3" style="background: linear-gradient(90deg, rgba(254,226,226,0.7), rgba(252,165,165,0.4)); border-color: rgba(248,113,113,0.5);">
                            <span class="text-2xl">⚠️</span>
                            <p class="text-sm font-semibold text-rose-700">{{ session('error') }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot }}
                @endif
            </main>
        </div>
    </div>
    <x-mobile-bottom-nav />
</body>
</html>
