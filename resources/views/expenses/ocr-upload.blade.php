<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Upload Struk via OCR') }} <span class="text-2xl">📸</span></h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        @if(session('error'))
            <div class="rounded-2xl bg-rose-50 border-2 border-rose-200 p-4 text-rose-800 mb-4">⚠️ {{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('expenses.ocr.process') }}" enctype="multipart/form-data" class="card-unicorn !p-8">
            @csrf
            <div class="text-center mb-6">
                <p class="text-5xl mb-3">📸</p>
                <h3 class="font-display text-xl font-bold text-purple-900">Foto / Upload Struk</h3>
                <p class="text-sm text-purple-500 mt-1">AI akan extract: merchant, tanggal, total, items, kategori</p>
            </div>

            <label class="block">
                <input type="file" name="receipt" accept="image/*" capture="environment" required
                       class="block w-full text-sm text-purple-700 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:font-bold file:bg-gradient-to-br file:from-pink-200 file:to-purple-200 file:text-purple-700 hover:file:from-pink-300 hover:file:to-purple-300 cursor-pointer">
            </label>
            <p class="text-xs text-purple-400 mt-2">Format: JPG / PNG / WEBP, max 5MB. Bisa langsung dari kamera HP.</p>

            <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-xs text-amber-800 mt-5">
                💡 <strong>Tip:</strong> Pastikan struk terbaca jelas, tidak miring, semua text terlihat. AI butuh provider model vision-capable (GPT-4o, Claude Sonnet 3.5+, Gemini Pro Vision).
            </div>

            <button type="submit" class="btn-unicorn w-full mt-6 py-4">✨ {{ __('Upload & Extract') }}</button>
        </form>

        <p class="text-xs text-center text-purple-400 mt-4">
            <a href="{{ route('expenses.index') }}" class="hover:underline">← Atau input expense manual</a>
        </p>
    </div>
</x-app-layout>
