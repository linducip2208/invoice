<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password — {{ config('app.name', 'Invocely') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=quicksand:400,500,600,700|comfortaa:400,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Quicksand', system-ui, sans-serif; }
        .font-display { font-family: 'Comfortaa', 'Quicksand', system-ui, sans-serif; }
        @keyframes floatSlow { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        @keyframes fadeSlideUp { 0%{transform:translateY(24px);opacity:0} 100%{transform:translateY(0);opacity:1} }
        @keyframes sparkle { 0%,100%{opacity:0.4;transform:scale(0.8)} 50%{opacity:1;transform:scale(1.2)} }
        .animate-float-slow { animation: floatSlow 6s ease-in-out infinite; }
        .animate-fade-slide { animation: fadeSlideUp .7s cubic-bezier(.16,1,.3,1) both; }
        .animate-sparkle { animation: sparkle 2s ease-in-out infinite; }
        .delay-100 { animation-delay: .1s; }
        .delay-200 { animation-delay: .2s; }
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
            <h2 class="font-display text-4xl lg:text-5xl font-bold leading-[1.1] mb-5 animate-fade-slide">Lupa Password? 🔮</h2>
            <p class="text-purple-100/80 text-lg leading-relaxed max-w-md animate-fade-slide delay-100">
                Tenang, kita kirimkan link reset ke email kamu. Cek inbox atau folder spam.
            </p>
        </div>

        <div class="relative text-purple-200/50 text-xs">
            &copy; {{ date('Y') }} {{ config('app.name', 'Invocely') }} &middot; Powered by Laravel
        </div>
    </div>

    {{-- RIGHT: Forgot Password Form --}}
    <div class="flex items-center justify-center p-6 sm:p-8 lg:p-16">
        <div class="w-full max-w-md">
            <div class="lg:hidden text-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg shadow-purple-500/30">
                        <span class="text-lg">🦄</span>
                    </div>
                    <span class="font-display font-bold text-xl text-purple-900">{{ config('app.name', 'Invocely') }}</span>
                </a>
            </div>

            <h1 class="font-display text-4xl font-bold text-purple-900 mb-2">Lupa Password</h1>
            <p class="text-purple-500 mb-8">Masukkan email kamu, kami kirim link reset.</p>

            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 mb-6 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-xl p-4 mb-6 text-sm">
                    <p class="text-rose-600">{{ $errors->first('email') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-semibold text-purple-800 mb-1.5">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="w-full border border-purple-200 rounded-xl px-4 py-3 text-sm text-purple-900 placeholder-purple-300 focus:ring-3 focus:ring-purple-500/20 focus:border-purple-400 transition-shadow hover:border-purple-300 bg-white/90"
                           placeholder="kamu@magic.land">
                </div>
                <button type="submit"
                        class="w-full bg-gradient-to-br from-pink-400 via-purple-400 to-sky-400 hover:from-pink-500 hover:via-purple-500 hover:to-sky-500 text-white font-bold text-sm rounded-xl px-6 py-3.5 shadow-lg shadow-purple-400/30 hover:shadow-xl hover:shadow-purple-400/40 hover:-translate-y-0.5 transition-all duration-200">
                    ✦ Kirim Link Reset
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-purple-400 hover:text-pink-500 transition-colors inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
