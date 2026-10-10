<x-tabler.layout :title="$ticket->subject" :pretitle="__('Support Tickets')">
    <x-slot name="actions">
        <a href="{{ route('support.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back') }}
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-body d-flex gap-3 flex-wrap">
            <span class="badge bg-blue-lt">{{ __('Status:') }} {{ ucfirst($ticket->status) }}</span>
            <span class="badge bg-yellow-lt">{{ __('Priority:') }} {{ ucfirst($ticket->priority) }}</span>
        </div>
    </div>

    @foreach($ticket->messages as $m)
        <div class="card mb-2 {{ $m->is_admin_reply ? 'bg-blue-lt' : '' }}">
            <div class="card-body">
                <div class="text-muted small mb-2 fw-semibold">
                    {{ $m->user?->name ?? 'User' }} · {{ $m->created_at->diffForHumans() }}
                    @if($m->is_admin_reply)<span class="badge bg-primary ms-1">ADMIN</span>@endif
                </div>
                <p class="mb-0" style="white-space: pre-wrap;">{{ $m->body }}</p>
            </div>
        </div>
    @endforeach

    @if($ticket->status !== 'closed')
        <div class="card">
            <div class="card-header"><h3 class="card-title">{{ __('Send Reply') }}</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('support.reply', $ticket) }}">
                    @csrf
                    <x-tabler.field :label="__('Reply')" name="body">
                        <textarea id="body" name="body" rows="4" required placeholder="Reply..." class="form-control"></textarea>
                    </x-tabler.field>
                    <div class="text-end">
                        <button class="btn btn-primary"><x-ti name="send" /> {{ __('Send Reply') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-tabler.layout>
