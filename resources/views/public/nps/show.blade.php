<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bagaimana experience-mu?</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans min-h-screen flex items-center justify-center px-4" style="background: linear-gradient(135deg, #FFF8FE 0%, #FFE4F1 50%, #EDE9FE 100%);">
    <div class="max-w-2xl w-full">
        <div class="card-unicorn-glow !p-10 text-center">
            <p class="text-5xl mb-4">💫</p>
            <h1 class="font-display text-3xl font-bold text-purple-900 mb-3">Bagaimana experience-mu?</h1>
            <p class="text-purple-600 mb-8">Berapa kemungkinan kamu rekomendasiin kerjasama dengan tim kami ke teman atau kolega?</p>

            <form method="POST" action="{{ route('nps.submit', $token) }}" x-data="{ score: null }">
                @csrf

                <div class="grid grid-cols-11 gap-1.5 mb-3">
                    @for($i = 0; $i <= 10; $i++)
                        <button type="button"
                                @click="score = {{ $i }}"
                                :class="score === {{ $i }} ? 'shadow-unicorn-pink scale-110' : ''"
                                class="aspect-square rounded-xl font-bold text-base text-white transition transform hover:scale-110"
                                style="background: {{ $i <= 6 ? 'linear-gradient(135deg, #FB7185, #F97316)' : ($i <= 8 ? 'linear-gradient(135deg, #FCD34D, #FB923C)' : 'linear-gradient(135deg, #6EE7B7, #34D399)') }};">
                            {{ $i }}
                        </button>
                    @endfor
                </div>
                <input type="hidden" name="score" :value="score" required>

                <div class="flex justify-between text-xs text-purple-400 mb-6">
                    <span>← Kurang Puas</span>
                    <span>Sangat Puas →</span>
                </div>

                <div x-show="score !== null" x-transition class="text-left mb-6">
                    <label class="block text-sm font-semibold text-purple-700 mb-2">Cerita dikit dong (opsional)</label>
                    <textarea name="comment" rows="3" maxlength="2000"
                              class="input-unicorn rounded-xl px-4 py-3 w-full"
                              placeholder="Apa yang membuat kamu kasih nilai itu?"></textarea>
                </div>

                <button type="submit" :disabled="score === null"
                        :class="score !== null ? 'btn-unicorn' : 'opacity-40 cursor-not-allowed bg-purple-200 text-purple-500 px-6 py-3 rounded-xl'"
                        class="px-10 py-3 text-base font-bold">
                    ✦ Kirim Feedback
                </button>
            </form>
        </div>
        <p class="text-center text-xs text-purple-400 mt-6">Powered by <a href="/" class="font-bold text-rainbow">Invocely</a></p>
    </div>
    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
