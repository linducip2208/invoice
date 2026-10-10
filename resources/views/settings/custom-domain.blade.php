<x-tabler.layout :title="__('Custom Domain')" :pretitle="__('Workspace')">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="card-title"><x-ti name="star" /> {{ __('White-Label Premium') }}</h3>
                    <p class="text-muted mb-0">
                        {{ __('Klien akses invoice via domain kamu sendiri (mis.') }} <code>invoice.brandkamu.com</code>) — {{ __('bukan invocely.app. Lebih profesional, lebih trust, branding kuat.') }}
                    </p>
                </div>
            </div>

            @if(! $domain)
                <div class="card">
                    <div class="card-header"><h3 class="card-title">{{ __('Tambah Domain') }}</h3></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('custom-domain.store') }}">
                            @csrf
                            <x-tabler.field :label="__('Hostname *')" name="hostname" :required="true" :help="__('Subdomain yang akan dipakai. Contoh: billing.acme.com')">
                                <input type="text" id="hostname" name="hostname" value="{{ old('hostname') }}" required placeholder="invoice.brandkamu.com"
                                    class="form-control font-monospace @error('hostname') is-invalid @enderror">
                                @error('hostname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </x-tabler.field>
                            <x-tabler.field :label="__('Metode Verifikasi *')" name="verification_method" :required="true">
                                <select id="verification_method" name="verification_method" required class="form-select">
                                    <option value="cname">CNAME (recommended)</option>
                                    <option value="txt">TXT Record</option>
                                </select>
                            </x-tabler.field>
                            <button type="submit" class="btn btn-primary"><x-ti name="plus" /> {{ __('Tambah Domain') }}</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="card mb-3">
                    <div class="card-body d-flex justify-content-between flex-wrap gap-3">
                        <div>
                            <div class="subheader">Hostname</div>
                            <div class="h3 mb-1 font-monospace">{{ $domain->hostname }}</div>
                            <div class="text-muted">
                                {{ __('Status:') }}
                                @if($domain->status === 'verified')
                                    <span class="badge bg-green-lt">{{ __('Verified') }}</span>
                                    @if($domain->verified_at)
                                        <span class="small text-muted">— {{ $domain->verified_at->diffForHumans() }}</span>
                                    @endif
                                @elseif($domain->status === 'failed')
                                    <span class="badge bg-red-lt">{{ __('Failed') }}</span>
                                @else
                                    <span class="badge bg-yellow-lt">{{ __('Pending') }}</span>
                                @endif
                            </div>
                        </div>
                        <form method="POST" action="{{ route('custom-domain.destroy') }}" onsubmit="return confirm('Hapus custom domain?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm"><x-ti name="trash" /> {{ __('Hapus') }}</button>
                        </form>
                    </div>
                </div>

                @if($domain->status !== 'verified')
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">{{ __('Setup DNS') }}</h3></div>
                        <div class="card-body">
                            @if($domain->verification_method === 'cname')
                                <p class="text-muted">{{ __('Tambah record berikut di DNS provider kamu (Cloudflare, Namecheap, dll):') }}</p>
                                <div class="datagrid mb-3">
                                    <div class="datagrid-item"><div class="datagrid-title">Type</div><div class="datagrid-content font-monospace">CNAME</div></div>
                                    <div class="datagrid-item"><div class="datagrid-title">Host</div><div class="datagrid-content font-monospace">{{ $domain->hostname }}</div></div>
                                    <div class="datagrid-item"><div class="datagrid-title">Target</div><div class="datagrid-content font-monospace">cname.invocely.app</div></div>
                                    <div class="datagrid-item"><div class="datagrid-title">TTL</div><div class="datagrid-content">3600 (or default)</div></div>
                                </div>
                            @else
                                <p class="text-muted">{{ __('Tambah TXT record berikut:') }}</p>
                                <div class="datagrid mb-3">
                                    <div class="datagrid-item"><div class="datagrid-title">Type</div><div class="datagrid-content font-monospace">TXT</div></div>
                                    <div class="datagrid-item"><div class="datagrid-title">Host</div><div class="datagrid-content font-monospace">_invocely-verify.{{ $domain->hostname }}</div></div>
                                    <div class="datagrid-item"><div class="datagrid-title">Value</div><div class="datagrid-content font-monospace text-break">{{ $domain->verification_token }}</div></div>
                                </div>
                            @endif

                            <div class="alert alert-warning">
                                <x-ti name="alert-triangle" /> {{ __('DNS propagation bisa 5 menit - 24 jam. Kalau gagal verify pertama kali, tunggu lalu coba lagi.') }}
                            </div>

                            <form method="POST" action="{{ route('custom-domain.verify') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary"><x-ti name="search" /> {{ __('Verify Sekarang') }}</button>
                            </form>

                            @if($domain->last_check_at)
                                <p class="text-muted small mt-2 mb-0">{{ __('Last check:') }} {{ $domain->last_check_at->diffForHumans() }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-tabler.layout>
