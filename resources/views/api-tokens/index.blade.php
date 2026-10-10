<x-tabler.layout :title="__('API Tokens')" :pretitle="__('Workspace')">
    @if($newToken)
        <div class="alert alert-success">
            <div class="d-flex">
                <div><x-ti name="key" /></div>
                <div>
                    <h4 class="alert-title">{{ __('Token baru — copy SEKARANG, tidak akan ditampilkan lagi:') }}</h4>
                    <code class="d-block bg-white p-2 rounded font-mono text-break">{{ $newToken }}</code>
                </div>
            </div>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-header"><h3 class="card-title">{{ __('Buat Token Baru') }}</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('api-tokens.store') }}">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-9">
                        <x-tabler.field :label="__('Nama token')" name="name">
                            <input type="text" id="name" name="name" placeholder="Nama token (mis: Mobile App, n8n)" required class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100"><x-ti name="plus" /> {{ __('Generate') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h3 class="card-title">{{ __('Existing Tokens') }}</h3></div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Prefix') }}</th>
                        <th>{{ __('Last Used') }}</th>
                        <th>{{ __('Created') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tokens as $t)
                        <tr>
                            <td class="fw-semibold">{{ $t->name }}</td>
                            <td class="font-monospace small">{{ $t->prefix }}_***</td>
                            <td class="text-muted">{{ $t->last_used_at?->diffForHumans() ?? 'Never' }}</td>
                            <td class="text-muted">{{ $t->created_at->diffForHumans() }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('api-tokens.destroy', $t) }}" class="d-inline" onsubmit="return confirm('Revoke?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ __('Revoke') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-tabler.empty icon="key" :title="__('Belum ada token.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-body text-muted">
            <p class="fw-semibold mb-1">{{ __('Cara pakai:') }}</p>
            <code class="d-block bg-light p-2 rounded font-mono small">curl -H "Authorization: Bearer YOUR_TOKEN" {{ url('/api/v1/invoices') }}</code>
            <p class="small mt-2 mb-0">REST API endpoint coming. Token format: <code>inv_xxxxxx_yyy...</code></p>
        </div>
    </div>
</x-tabler.layout>
