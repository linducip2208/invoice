<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Quick Invoice') }} <span class="text-2xl">⚡</span></h2>
    </x-slot>

    <div class="max-w-lg mx-auto">
        <form method="POST" action="{{ route('mobile.quick-invoice.store') }}" class="card-unicorn !p-6 space-y-5">
            @csrf
            <p class="text-sm text-purple-500">3 field. 1 klik kirim. Bahkan dari HP.</p>

            <div>
                <x-input-label :value="__('Klien *')" />
                <select name="client_id" required class="input-unicorn rounded-xl px-4 py-3 mt-1 block w-full text-base">
                    <option value="">— Pilih klien —</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label :value="__('Apa yang ditagih *')" />
                <textarea name="description" rows="2" required maxlength="500"
                          class="input-unicorn rounded-xl px-4 py-3 mt-1 w-full text-base"
                          placeholder="Mis. Jasa design landing page"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label :value="__('Total *')" />
                    <x-text-input name="amount" type="number" step="0.01" min="0.01" required class="mt-1 w-full text-lg font-bold" placeholder="500000" />
                </div>
                <div>
                    <x-input-label :value="__('Jatuh tempo')" />
                    <select name="due_in_days" class="input-unicorn rounded-xl px-4 py-3 mt-1 block w-full text-base">
                        <option value="7">+7 hari</option>
                        <option value="14" selected>+14 hari</option>
                        <option value="30">+30 hari</option>
                        <option value="60">+60 hari</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-unicorn w-full py-4 text-lg">⚡ {{ __('Buat Invoice') }}</button>
            <p class="text-xs text-center text-purple-400">Detail lainnya bisa di-edit setelah ini di halaman invoice.</p>
        </form>
    </div>
</x-app-layout>
