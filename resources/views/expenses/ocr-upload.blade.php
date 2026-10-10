<x-tabler.layout :title="__('Upload Struk via OCR')" :pretitle="__('Finance')">
    <x-slot name="actions">
        <a href="{{ route('expenses.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Atau input expense manual') }}
        </a>
    </x-slot>

    <div class="card">
        <div class="card-body text-center">
            <div class="text-muted mb-2"><x-ti name="photo" class="icon icon-lg" /></div>
            <h3 class="card-title">{{ __('Foto / Upload Struk') }}</h3>
            <p class="text-muted">{{ __('AI akan extract: merchant, tanggal, total, items, kategori') }}</p>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('expenses.ocr.process') }}" enctype="multipart/form-data">
                @csrf
                <x-tabler.field name="receipt">
                    <input type="file" id="receipt" name="receipt" accept="image/*" capture="environment" required class="form-control @error('receipt') is-invalid @enderror">
                </x-tabler.field>
                <p class="text-muted small">{{ __('Format: JPG / PNG / WEBP, max 5MB. Bisa langsung dari kamera HP.') }}</p>
                <div class="alert alert-warning" role="alert">
                    <div class="d-flex">
                        <div><x-ti name="info-circle" /></div>
                        <div>{{ __('Pastikan struk terbaca jelas, tidak miring, semua text terlihat. AI butuh provider model vision-capable (GPT-4o, Claude Sonnet 3.5+, Gemini Pro Vision).') }}</div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100"><x-ti name="scan" /> {{ __('Upload & Extract') }}</button>
            </form>
        </div>
    </div>
</x-tabler.layout>
