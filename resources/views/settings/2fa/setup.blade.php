<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Setup 2FA') }} <span class="text-2xl">🔐</span></h2>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        @if(session('error'))
            <div class="rounded-2xl bg-rose-50 border-2 border-rose-200 p-4 text-rose-800">⚠️ {{ session('error') }}</div>
        @endif

        <ol class="card-unicorn !p-8 space-y-6">
            <li class="flex gap-4">
                <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center font-display font-bold text-white shadow-unicorn-pink"
                     style="background: linear-gradient(135deg, #FF7AC6, #A78BFA);">1</div>
                <div class="flex-1">
                    <p class="font-display font-bold text-purple-900">Install authenticator app</p>
                    <p class="text-sm text-purple-600 mt-1">Google Authenticator, Authy, 1Password, atau Microsoft Authenticator.</p>
                </div>
            </li>

            <li class="flex gap-4">
                <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center font-display font-bold text-white shadow-unicorn-pink"
                     style="background: linear-gradient(135deg, #FF7AC6, #A78BFA);">2</div>
                <div class="flex-1">
                    <p class="font-display font-bold text-purple-900">Scan QR ini</p>
                    <p class="text-sm text-purple-600 mt-1 mb-3">Atau input kode manual.</p>
                    <div class="flex flex-col sm:flex-row items-start gap-4 bg-white p-5 rounded-2xl border-2 border-purple-100">
                        @php
                            $writer = new \BaconQrCode\Writer(new \BaconQrCode\Renderer\ImageRenderer(
                                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(180),
                                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
                            ));
                            $qrSvg = base64_encode($writer->writeString($qrUrl));
                        @endphp
                        <img src="data:image/svg+xml;base64,{{ $qrSvg }}" alt="QR Code 2FA" class="rounded-lg">
                        <div class="flex-1">
                            <p class="text-xs text-purple-400 uppercase tracking-wider mb-1">Manual Code</p>
                            <code class="block bg-purple-50 px-4 py-3 rounded-xl font-mono text-sm text-purple-900 break-all border border-purple-200">{{ $secret }}</code>
                            <p class="text-xs text-purple-400 mt-2">Account: {{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>
            </li>

            <li class="flex gap-4">
                <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center font-display font-bold text-white shadow-unicorn-pink"
                     style="background: linear-gradient(135deg, #FF7AC6, #A78BFA);">3</div>
                <div class="flex-1">
                    <p class="font-display font-bold text-purple-900">Save recovery codes</p>
                    <p class="text-sm text-purple-600 mt-1 mb-3">Simpan baik-baik — pengganti kode authenticator kalau HP hilang.</p>
                    <div class="grid grid-cols-2 gap-2 font-mono text-sm bg-amber-50 border-2 border-amber-200 rounded-xl p-4">
                        @foreach($recoveryCodes as $code)
                            <div class="bg-white px-3 py-2 rounded-lg text-amber-900 font-bold text-center">{{ $code }}</div>
                        @endforeach
                    </div>
                </div>
            </li>

            <li class="flex gap-4">
                <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center font-display font-bold text-white shadow-unicorn-pink"
                     style="background: linear-gradient(135deg, #FF7AC6, #A78BFA);">4</div>
                <div class="flex-1">
                    <p class="font-display font-bold text-purple-900">Konfirmasi dengan kode 6-digit</p>
                    <p class="text-sm text-purple-600 mt-1 mb-3">Masukkan kode dari authenticator app untuk verifikasi.</p>
                    <form method="POST" action="{{ route('2fa.confirm') }}" class="flex items-center gap-3">
                        @csrf
                        <input type="text" name="code" required pattern="[0-9]{6}" maxlength="6" autocomplete="off"
                               class="input-unicorn rounded-xl px-4 py-3 text-center font-mono text-2xl tracking-[0.4em] w-48"
                               placeholder="••••••" autofocus>
                        <button type="submit" class="btn-unicorn">✓ {{ __('Aktifkan') }}</button>
                    </form>
                </div>
            </li>
        </ol>
    </div>
</x-app-layout>
