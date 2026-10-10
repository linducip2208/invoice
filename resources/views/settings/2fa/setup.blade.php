<x-tabler.layout :title="__('Setup 2FA')" :pretitle="__('Settings')">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title"><span class="badge bg-primary me-2">1</span>{{ __('Install authenticator app') }}</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">Google Authenticator, Authy, 1Password, {{ __('atau Microsoft Authenticator.') }}</p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title"><span class="badge bg-primary me-2">2</span>{{ __('Scan QR ini') }}</h3>
                    <div class="card-subtitle">{{ __('Atau input kode manual.') }}</div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row gap-3 align-items-start border rounded p-3">
                        @php
                            $writer = new \BaconQrCode\Writer(new \BaconQrCode\Renderer\ImageRenderer(
                                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(180),
                                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
                            ));
                            $qrSvg = base64_encode($writer->writeString($qrUrl));
                        @endphp
                        <img src="data:image/svg+xml;base64,{{ $qrSvg }}" alt="QR Code 2FA" class="rounded border">
                        <div class="flex-fill">
                            <div class="subheader">{{ __('Manual Code') }}</div>
                            <code class="d-block bg-light p-2 rounded font-monospace text-break border">{{ $secret }}</code>
                            <p class="text-muted small mt-2 mb-0">{{ __('Account:') }} {{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title"><span class="badge bg-primary me-2">3</span>{{ __('Save recovery codes') }}</h3>
                    <div class="card-subtitle">{{ __('Simpan baik-baik — pengganti kode authenticator kalau HP hilang.') }}</div>
                </div>
                <div class="card-body">
                    <div class="row g-2 font-monospace">
                        @foreach($recoveryCodes as $code)
                            <div class="col-6"><div class="alert alert-warning text-center fw-semibold mb-0 py-2">{{ $code }}</div></div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><span class="badge bg-primary me-2">4</span>{{ __('Konfirmasi dengan kode 6-digit') }}</h3>
                    <div class="card-subtitle">{{ __('Masukkan kode dari authenticator app untuk verifikasi.') }}</div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('2fa.confirm') }}" class="d-flex gap-2 align-items-end flex-wrap">
                        @csrf
                        <x-tabler.field :label="__('Kode 6-digit')" name="code">
                            <input type="text" id="code" name="code" required pattern="[0-9]{6}" maxlength="6" autocomplete="off"
                                placeholder="••••••" autofocus class="form-control form-control-lg text-center font-monospace" style="max-width: 12rem;">
                        </x-tabler.field>
                        <button type="submit" class="btn btn-primary btn-lg"><x-ti name="check" /> {{ __('Aktifkan') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-tabler.layout>
