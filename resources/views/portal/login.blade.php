<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Portal — Login</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans min-h-screen flex items-center justify-center px-4" style="background: linear-gradient(135deg, #FFF8FE 0%, #FFE4F1 50%, #EDE9FE 100%);">
    <div class="max-w-md w-full">
        <div class="text-center mb-6">
            <p class="text-5xl mb-3">🪟</p>
            <h1 class="font-display text-2xl font-bold text-rainbow">Customer Portal</h1>
            <p class="text-sm text-purple-500 mt-1">Akses semua invoice & quote kamu</p>
        </div>

        <form method="POST" action="{{ route('portal.login.submit') }}" class="card-unicorn-glow !p-8 space-y-4">
            @csrf
            @if($errors->any())
                <div class="rounded-xl bg-rose-50 border border-rose-200 p-3 text-sm text-rose-800">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-purple-700 mb-1">Email</label>
                <input type="email" name="email" required value="{{ old('email') }}" autofocus
                       class="input-unicorn rounded-xl px-4 py-3 w-full">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-purple-700 mb-1">Password</label>
                <input type="password" name="password" required
                       class="input-unicorn rounded-xl px-4 py-3 w-full">
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" value="1" class="rounded border-purple-300 text-pink-500">
                <span class="text-sm text-purple-700">Ingat saya</span>
            </label>

            <button type="submit" class="btn-unicorn w-full py-3">✦ Masuk</button>
        </form>

        <p class="text-center text-xs text-purple-400 mt-6">Belum punya akun? Hubungi penyedia jasa kamu untuk akses portal.</p>
        <p class="text-center text-xs text-purple-400 mt-2">Powered by <a href="/" class="font-bold text-rainbow">Invocely</a></p>
    </div>
</body>
</html>
