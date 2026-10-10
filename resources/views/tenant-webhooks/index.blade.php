<x-tabler.layout :title="__('Outbound Webhooks')" :pretitle="__('Workspace')">
    <div class="card mb-3">
        <div class="card-header"><h3 class="card-title">{{ __('Tambah Webhook') }}</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('tenant-webhooks.store') }}">
                @csrf
                <x-tabler.field :label="__('URL')" name="url">
                    <input type="url" id="url" name="url" placeholder="https://yourapp.com/webhook" required class="form-control">
                </x-tabler.field>
                <div class="mb-3">
                    <label class="form-label">{{ __('Events') }}</label>
                    <div class="row g-2">
                        @foreach(['invoice.created','invoice.sent','invoice.paid','invoice.viewed','payment.received','client.created'] as $ev)
                            <div class="col-md-4">
                                <label class="form-check">
                                    <input type="checkbox" name="events[]" value="{{ $ev }}" class="form-check-input">
                                    <span class="form-check-label font-monospace small">{{ $ev }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <button class="btn btn-primary"><x-ti name="plus" /> {{ __('Add Webhook') }}</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">{{ __('Webhooks') }}</h3></div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('URL') }}</th>
                        <th class="text-center">{{ __('Events') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Last Fire') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($webhooks as $w)
                        <tr>
                            <td class="font-monospace small text-truncate" style="max-width: 16rem;">{{ $w->url }}</td>
                            <td class="text-center"><span class="badge bg-blue-lt">{{ count($w->events ?? []) }}</span></td>
                            <td>
                                @if($w->is_active)
                                    <span class="badge bg-green-lt">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-yellow-lt">{{ __('Paused') }}</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $w->last_fired_at?->diffForHumans() ?? 'Never' }}</td>
                            <td class="text-end text-nowrap">
                                <form method="POST" action="{{ route('tenant-webhooks.toggle', $w) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm">{{ $w->is_active ? __('Pause') : __('Resume') }}</button>
                                </form>
                                <form method="POST" action="{{ route('tenant-webhooks.destroy', $w) }}" class="d-inline" onsubmit="return confirm('Hapus?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><x-ti name="trash" /></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-tabler.empty icon="link" :title="__('Belum ada webhook.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-tabler.layout>
