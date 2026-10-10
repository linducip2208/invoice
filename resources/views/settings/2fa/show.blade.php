<x-tabler.layout :title="__('Two-Factor Authentication')" :pretitle="__('Settings')">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @if(session('recovery_codes'))
                <div class="card mb-3 border-warning">
                    <div class="card-header"><h3 class="card-title">{{ __('Recovery Codes Baru') }}</h3></div>
                    <div class="card-body">
                        <p class="text-muted">{{ __('Simpan kode-kode ini di tempat aman. Setiap kode hanya bisa dipakai sekali sebagai pengganti kode authenticator kalau HP hilang.') }}</p>
                        <div class="row g-2 font-monospace">
                            @foreach(session('recovery_codes') as $code)
                                <div class="col-6"><div class="border rounded p-2 text-center fw-semibold">{{ $code }}</div></div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-3 mb-3">
                        <span class="avatar avatar-lg {{ $isEnabled ? 'bg-green-lt' : 'bg-blue-lt' }}">
                            <x-ti name="{{ $isEnabled ? 'lock' : 'lock-open' }}" />
                        </span>
                        <div>
                            <h3 class="mb-1">{{ $isEnabled ? '2FA Aktif' : '2FA Belum Aktif' }}</h3>
                            <p class="text-muted mb-0">
                                @if($isEnabled)
                                    {{ __('Akun terlindungi. Saat login, kamu akan diminta kode dari authenticator app.') }}
                                    @if($tfa?->enabled_at)
                                        <br><span class="small text-muted">{{ __('Diaktifkan') }} {{ $tfa->enabled_at->diffForHumans() }}</span>
                                    @endif
                                @else
                                    {{ __('Tambah lapisan keamanan ekstra. Butuh app seperti Google Authenticator, Authy, atau 1Password.') }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr>

                    @if(! $isEnabled)
                        <form method="POST" action="{{ route('2fa.enable') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary"><x-ti name="lock" /> {{ __('Aktifkan 2FA Sekarang') }}</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('2fa.recovery-codes') }}" class="mb-3">
                            @csrf
                            <div class="row g-2 align-items-end">
                                <div class="col-md-8">
                                    <x-tabler.field :label="__('Konfirmasi Password untuk Generate Recovery Codes Baru')" name="password">
                                        <input type="password" id="password" name="password" required class="form-control">
                                    </x-tabler.field>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn w-100"><x-ti name="key" /> {{ __('Recovery Codes') }}</button>
                                </div>
                            </div>
                        </form>

                        <hr>

                        <form method="POST" action="{{ route('2fa.disable') }}" onsubmit="return confirm('Yakin nonaktifkan 2FA? Akun akan kurang terlindungi.')">
                            @csrf
                            <div class="row g-2 align-items-end">
                                <div class="col-md-8">
                                    <x-tabler.field :label="__('Konfirmasi Password untuk Disable 2FA')" name="password_disable">
                                        <input type="password" id="password_disable" name="password" required class="form-control">
                                    </x-tabler.field>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-danger w-100"><x-ti name="lock-open" /> {{ __('Disable 2FA') }}</button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-tabler.layout>
