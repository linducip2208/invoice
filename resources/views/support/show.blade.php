<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="heading-unicorn">💬 {{ $ticket->subject }}</h2>
            <a href="{{ route('support.index') }}" class="text-purple-500">← Back</a>
        </div>
    </x-slot>
    <div class="max-w-3xl mx-auto space-y-4">
        <div class="card-unicorn">
            <p>Status: <strong>{{ ucfirst($ticket->status) }}</strong> · Priority: <strong>{{ ucfirst($ticket->priority) }}</strong></p>
        </div>
        @foreach($ticket->messages as $m)
            <div class="card-unicorn {{ $m->is_admin_reply ? 'bg-pink-50' : '' }}">
                <p class="text-xs text-purple-400 mb-2 font-bold">{{ $m->user?->name ?? 'User' }} · {{ $m->created_at->diffForHumans() }} {{ $m->is_admin_reply ? '· ADMIN' : '' }}</p>
                <p class="text-purple-700 whitespace-pre-wrap">{{ $m->body }}</p>
            </div>
        @endforeach

        @if($ticket->status !== 'closed')
        <form method="POST" action="{{ route('support.reply', $ticket) }}" class="card-unicorn">
            @csrf
            <textarea name="body" rows="4" required class="input-unicorn rounded-xl px-3 py-2 w-full" placeholder="Reply..."></textarea>
            <div class="mt-3 flex justify-end"><button class="btn-unicorn">Send Reply</button></div>
        </form>
        @endif
    </div>
</x-app-layout>
