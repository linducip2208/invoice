<x-tabler.layout :title="__('Portal Users')">
    <x-slot name="actions">
        <a href="{{ route('clients.show', $client) }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Kembali ke Klien') }}
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Portal Users') }} — {{ $client->name }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('clients.portal-users.invite', $client) }}">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-8">
                        <x-tabler.field :label="__('Email akses portal')" name="email" type="email"
                            :value="old('email')" attrs="required placeholder=staff@klien.com" />
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <x-ti name="plus" /> {{ __('Buat Akses') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Daftar Akses ({{ $portalUsers->count() }})</h3>
        </div>
        <div class="card-body">
            <p class="text-muted small">URL portal: <code>{{ route('portal.login') }}</code></p>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Last Login') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($portalUsers as $u)
                        <tr>
                            <td class="fw-semibold">{{ $u->email }}</td>
                            <td class="text-muted">
                                @if($u->last_login_at)
                                    {{ $u->last_login_at->diffForHumans() }}
                                @else
                                    <span class="badge bg-yellow">{{ __('Belum pernah login') }}</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <x-tabler.delete-button :action="route('portal-users.destroy', $u)"
                                    :confirm="__('Hapus akses :email?', ['email' => $u->email])"
                                    :label="__('Hapus')" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <x-tabler.empty icon="users" :title="__('Belum ada akses portal untuk klien ini.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-info mb-0">
        <x-ti name="info-circle" />
        <strong>Cara kerja:</strong> Klien akan login di <code>/portal/login</code> dengan email &amp; password sementara yang di-generate saat invite. Mereka bisa lihat semua invoice &amp; quote miliknya. Tidak bisa edit, tidak bisa lihat klien lain.
    </div>
</x-tabler.layout>
