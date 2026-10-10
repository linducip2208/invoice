<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">⏱️ {{ __('Time Tracking') }}</h2>
    </x-slot>
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="card-unicorn"><p class="text-xs text-purple-400 uppercase font-bold">{{ __('Total Hours') }}</p><p class="text-2xl font-display font-bold">{{ number_format($totalHours, 2) }}h</p></div>
            <div class="card-unicorn"><p class="text-xs text-purple-400 uppercase font-bold">{{ __('Billable Amount') }}</p><p class="text-2xl font-display font-bold">{{ number_format($totalAmount, 2) }}</p></div>
        </div>

        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg mb-4">+ {{ __('Tambah Time Entry') }}</h3>
            <form method="POST" action="{{ route('time-entries.store') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                @csrf
                <input type="date" name="entry_date" value="{{ now()->toDateString() }}" required class="input-unicorn rounded-xl px-3 py-2 text-sm">
                <select name="client_id" class="input-unicorn rounded-xl px-3 py-2 text-sm">
                    <option value="">— Klien (opsional) —</option>
                    @foreach($clients as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </select>
                <input type="text" name="description" required placeholder="Apa yang dikerjakan" class="input-unicorn rounded-xl px-3 py-2 text-sm md:col-span-2">
                <input type="number" step="0.25" min="0.01" name="hours" required placeholder="Jam (mis: 2.5)" class="input-unicorn rounded-xl px-3 py-2 text-sm">
                <input type="number" step="0.01" min="0" name="hourly_rate" required placeholder="Rate / jam" class="input-unicorn rounded-xl px-3 py-2 text-sm">
                <label class="flex items-center gap-2 text-sm md:col-span-5"><input type="checkbox" name="billable" value="1" checked> {{ __('Billable (bisa di-invoice)') }}</label>
                <button class="btn-unicorn">{{ __('Catat') }}</button>
            </form>
        </div>

        <div class="card-unicorn">
            <table class="table-unicorn">
                <thead><tr><th>Tanggal</th><th>Klien</th><th>Deskripsi</th><th class="text-right">Jam</th><th class="text-right">Rate</th><th class="text-right">Total</th><th></th></tr></thead>
                <tbody>
                    @forelse($entries as $e)
                    <tr>
                        <td>{{ $e->entry_date?->format('d M Y') }}</td>
                        <td>{{ $e->client?->name ?? '—' }}</td>
                        <td>{{ $e->description }}</td>
                        <td class="text-right">{{ number_format($e->hours, 2) }}</td>
                        <td class="text-right">{{ number_format($e->hourly_rate, 2) }}</td>
                        <td class="text-right font-bold">{{ number_format($e->amount, 2) }}</td>
                        <td>
                            <form method="POST" action="{{ route('time-entries.destroy', $e) }}" onsubmit="return confirm('Hapus?');">@csrf @method('DELETE')<button class="text-rose-500 hover:text-rose-700 text-sm">×</button></form>
                        </td>
                    </tr>
                    @empty<tr><td colspan="7" class="text-center text-purple-400 py-8">Belum ada entry.</td></tr>@endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $entries->links() }}</div>
        </div>
    </div>
</x-app-layout>
