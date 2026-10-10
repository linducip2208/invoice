<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn flex items-center gap-2">🧾 {{ __('Expenses') }}</h2></x-slot>
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="card-unicorn"><p class="text-xs text-purple-400 uppercase font-bold">{{ __('Total Expenses') }}</p><p class="text-2xl font-display font-bold">{{ number_format($totalAmount, 2) }}</p></div>

        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg mb-4">+ {{ __('Catat Expense') }}</h3>
            <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                @csrf
                <input type="date" name="expense_date" value="{{ now()->toDateString() }}" required class="input-unicorn rounded-xl px-3 py-2 text-sm">
                <input type="text" name="category" placeholder="Kategori" class="input-unicorn rounded-xl px-3 py-2 text-sm">
                <input type="text" name="description" required placeholder="Deskripsi" class="input-unicorn rounded-xl px-3 py-2 text-sm md:col-span-2">
                <input type="number" step="0.01" min="0" name="amount" required placeholder="Jumlah" class="input-unicorn rounded-xl px-3 py-2 text-sm">
                <select name="currency" class="input-unicorn rounded-xl px-3 py-2 text-sm"><option>IDR</option><option>USD</option></select>
                <select name="client_id" class="input-unicorn rounded-xl px-3 py-2 text-sm md:col-span-2">
                    <option value="">— Klien (opsional, untuk billable) —</option>
                    @foreach($clients as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </select>
                <input type="file" name="receipt" accept="image/*" class="text-xs md:col-span-2">
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="billable" value="1"> Billable</label>
                <button class="btn-unicorn">Simpan</button>
            </form>
        </div>

        <div class="card-unicorn">
            <table class="table-unicorn">
                <thead><tr><th>Tanggal</th><th>Kategori</th><th>Deskripsi</th><th>Klien</th><th class="text-right">Jumlah</th><th></th></tr></thead>
                <tbody>
                    @forelse($expenses as $e)
                    <tr>
                        <td>{{ $e->expense_date?->format('d M Y') }}</td>
                        <td><span class="badge-unicorn">{{ $e->category ?? '—' }}</span></td>
                        <td>{{ $e->description }}</td>
                        <td>{{ $e->client?->name ?? '—' }}</td>
                        <td class="text-right font-bold">{{ $e->currency }} {{ number_format($e->amount, 2) }}</td>
                        <td><form method="POST" action="{{ route('expenses.destroy', $e) }}" onsubmit="return confirm('Hapus?');">@csrf @method('DELETE')<button class="text-rose-500 text-sm">×</button></form></td>
                    </tr>
                    @empty<tr><td colspan="6" class="text-center text-purple-400 py-8">Belum ada expense.</td></tr>@endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $expenses->links() }}</div>
        </div>
    </div>
</x-app-layout>
