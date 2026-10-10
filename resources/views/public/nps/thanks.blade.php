<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terima kasih!</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans min-h-screen flex items-center justify-center px-4" style="background: linear-gradient(135deg, #FFF8FE 0%, #FFE4F1 50%, #EDE9FE 100%);">
    <div class="max-w-md w-full">
        <div class="card-unicorn-glow !p-10 text-center sparkles-bg">
            <p class="text-6xl mb-4 animate-unicorn-float">🦄</p>
            <h1 class="font-display text-3xl font-bold text-rainbow mb-3">Terima kasih!</h1>
            <p class="text-purple-600">Feedback kamu sangat berharga buat tim kami. Kami baca semua, dan terus tingkatkan kualitas service.</p>
            @if($nps->score >= 9)
                <p class="text-pink-600 font-bold mt-6 text-lg">✨ Wah, kamu salah satu klien favorit kami! ✨</p>
            @endif
        </div>
        <p class="text-center text-xs text-purple-400 mt-6">Powered by <a href="/" class="font-bold text-rainbow">Invocely</a></p>
    </div>
</body>
</html>
