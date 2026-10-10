<x-tabler.layout :title="__('Support Tickets')" :pretitle="__('Help')">
    <x-slot name="actions">
        <a href="{{ route('support.create') }}" class="btn btn-primary">
            <x-ti name="plus" /> {{ __('Ticket Baru') }}
        </a>
    </x-slot>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Subject') }}</th>
                        <th>{{ __('Priority') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Updated') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $t)
                        <tr>
                            <td><a href="{{ route('support.show', $t) }}" class="fw-semibold">{{ $t->subject }}</a></td>
                            <td class="text-muted">{{ ucfirst($t->priority) }}</td>
                            <td><x-tabler.status-badge :status="$t->status" /></td>
                            <td class="text-muted">{{ $t->updated_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-tabler.empty icon="lifebuoy" :title="__('Belum ada ticket.')"
                                    :body="__('Need help? Buka tiket baru.')"
                                    :actionUrl="route('support.create')" :actionLabel="__('Ticket Baru')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $tickets->links() }}</div>
        @endif
    </div>
</x-tabler.layout>
