<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Konfirmasi Password — {{ config('app.name', 'Invocely') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=quicksand:400,500,600,700|comfortaa:400,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Quicksand', system-ui, sans-serif; }
        .font-display { font-family: 'Comfortaa', 'Quicksand', system-ui, sans-serif; }
        @keyframes floatSlow { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        .animate-float-slow { animation: floatSlow 6s ease-in-out infinite; }
    </style>
</head>
<body class="h-full antialiased" style="background:#FFF8FE;">

<div class="min-h-screen grid lg:grid-cols-2">

    {{-- LEFT: Hero Brand Panel --}}
    <div class="hidden lg:flex relative bg-gradient-to-br from-purple-800 via-fuchsia-900 to-slate-950 p-12 flex-col justify-between overflow-hidden">
        <div class="absolute inset-0 opacity-25"
             style="background-image: radial-gradient(circle at 20% 30%, rgba(167,139,250,.5) 0%, transparent 50%), radial-gradient(circle at 75% 60%, rgba(255,122,198,.4) 0%, transparent 50%), radial-gradient(circle at 40% 85%, rgba(125,211,252,.35) 0%, transparent 45%);"></div>
        <div class="absolute inset-0 opacity-[0.05]"
             style="background-image:linear-gradient(rgba(255,255,255,.3) 1px, transparent 1px),linear-gradient(90deg, rgba(255,255,255,.3) 1px, transparent 1px);background-size:60px 60px;"></div>
        <div class="absolute -bottom-16 -right-16 text-[18rem] opacity-[0.06] select-none">🦄</div>

        <div class="relative">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-white/20 to-white/5 backdrop-blur flex items-center justify-center shadow-lg shadow-black/20 group-hover:scale-105 transition-transform">
                    <span class="text-2xl">🦄</span>
                </div>
                <div>
                    <span class="font-display font-bold text-3xl text-white tracking-tight">{{ config('app.name', 'Invocely') }}</span>
                    <p class="text-purple-200/60 text-[11px] uppercase tracking-[0.2em] mt-0.5">Magical Invoicing</p>
                </div>
            </a>
        </div>

        <div class="relative text-white">
            <h2 class="font-display text-4xl lg:text-5xl font-bold leading-[1.1] mb-5">Area Aman 🔐</h2>
            <p class="text-purple-100/80 text-lg leading-relaxed max-w-md">
                Ini adalah area aman aplikasi. Konfirmasi password kamu untuk melanjutkan.
            </p>
        </div>

        <div class="relative text-purple-200/50 text-xs">
            &copy; {{ date('Y') }} {{ config('app.name', 'Invocely') }} &middot; Powered by Laravel
        </div>
    </div>

    {{-- RIGHT: Confirm Password Form --}}
    <div class="flex items-center justify-center p-6 sm:p-8 lg:p-16">
        <div class="w-full max-w-md">
            <div class="lg:hidden text-center mb-8">
                <span class="text-4xl inline-block animate-float-slow">🔐</span>
            </div>

            <h1 class="font-display text-4xl font-bold text-purple-900 mb-2">Konfirmasi Password</h1>
            <p class="text-purple-500 mb-8">Masukkan password kamu untuk melanjutkan ke area ini.</p>

            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-xl p-4 mb-6 text-sm">
                    <p class="text-rose-600">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="password" class="block text-sm font-semibold text-purple-800 mb-1.5">Password</label>
                    <input type="password" name="password" id="password" required autocomplete="current-password"
                           class="w-full border border-purple-200 rounded-xl px-4 py-3 text-sm text-purple-900 placeholder-purple-300 focus:ring-3 focus:ring-purple-500/20 focus:border-purple-400 transition-shadow hover:border-purple-300 bg-white/90"
                           placeholder="••••••••">
                </div>
                <button type="submit"
                        class="w-full bg-gradient-to-br from-pink-400 via-purple-400 to-sky-400 hover:from-pink-500 hover:via-purple-500 hover:to-sky-500 text-white font-bold text-sm rounded-xl px-6 py-3.5 shadow-lg shadow-purple-400/30 hover:shadow-xl hover:shadow-purple-400/40 hover:-translate-y-0.5 transition-all duration-200">
                    ✦ Konfirmasi
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
