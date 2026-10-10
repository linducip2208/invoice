<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="heading-unicorn">💬 {{ __('Support Tickets') }}</h2>
            <a href="{{ route('support.create') }}" class="btn-unicorn">+ {{ __('Ticket Baru') }}</a>
        </div>
    </x-slot>
    <div class="max-w-4xl mx-auto card-unicorn">
        <table class="table-unicorn">
            <thead><tr><th>Subject</th><th>Priority</th><th>Status</th><th>Updated</th></tr></thead>
            <tbody>
                @forelse($tickets as $t)
                <tr>
                    <td><a href="{{ route('support.show', $t) }}" class="font-bold text-purple-700 hover:text-pink-500">{{ $t->subject }}</a></td>
                    <td>{{ ucfirst($t->priority) }}</td>
                    <td><span class="badge-{{ $t->status === 'resolved' || $t->status === 'closed' ? 'success' : 'warning' }}">{{ ucfirst($t->status) }}</span></td>
                    <td class="text-purple-500 text-sm">{{ $t->updated_at->diffForHumans() }}</td>
                </tr>
                @empty<tr><td colspan="4" class="text-center text-purple-400 py-8">Belum ada ticket. Need help? <a href="{{ route('support.create') }}" class="text-pink-600 font-semibold">Buka tiket →</a></td></tr>@endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $tickets->links() }}</div>
    </div>
</x-app-layout>
