<x-admin-layout title="Coupons">
    <div class="card-unicorn mb-4">
        <h3 class="font-display font-bold text-lg mb-3">+ New Coupon</h3>
        <form method="POST" action="{{ route('admin.coupons.store') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
            @csrf
            <input type="text" name="code" placeholder="LAUNCH50" required class="input-unicorn rounded-xl px-3 py-2 text-sm uppercase">
            <select name="discount_type" required class="input-unicorn rounded-xl px-3 py-2 text-sm">
                <option value="percent">Percent (%)</option><option value="fixed">Fixed</option>
            </select>
            <input type="number" step="0.01" name="discount_value" required placeholder="Value" class="input-unicorn rounded-xl px-3 py-2 text-sm">
            <select name="plan_id" class="input-unicorn rounded-xl px-3 py-2 text-sm">
                <option value="">All plans</option>
                @foreach($plans as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
            </select>
            <input type="number" name="max_uses" placeholder="Max uses (∞)" class="input-unicorn rounded-xl px-3 py-2 text-sm">
            <input type="datetime-local" name="expires_at" class="input-unicorn rounded-xl px-3 py-2 text-sm">
            <input type="text" name="description" placeholder="Description" class="input-unicorn rounded-xl px-3 py-2 text-sm md:col-span-3">
            <label class="flex items-center gap-2 text-sm md:col-span-2"><input type="checkbox" name="is_active" value="1" checked> Active</label>
            <button class="btn-unicorn">Create</button>
        </form>
    </div>

    <div class="card-unicorn">
        <table class="table-unicorn">
            <thead><tr><th>Code</th><th>Discount</th><th>Plan</th><th>Uses</th><th>Expires</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($coupons as $c)
                <tr>
                    <td class="font-mono font-bold">{{ $c->code }}</td>
                    <td>{{ $c->discount_type === 'percent' ? $c->discount_value . '%' : 'Rp' . number_format($c->discount_value, 0, ',', '.') }}</td>
                    <td>{{ $c->plan?->name ?? 'All' }}</td>
                    <td>{{ $c->uses_count }}/{{ $c->max_uses ?? '∞' }}</td>
                    <td class="text-purple-500 text-sm">{{ $c->expires_at?->format('d M Y') ?? 'Never' }}</td>
                    <td><span class="badge-{{ $c->is_active ? 'success' : 'warning' }}">{{ $c->is_active ? 'Active' : 'Disabled' }}</span></td>
                    <td>
                        <form method="POST" action="{{ route('admin.coupons.toggle', $c) }}" class="inline">@csrf<button class="text-purple-600 text-sm">{{ $c->is_active ? 'Disable' : 'Enable' }}</button></form>
                        <form method="POST" action="{{ route('admin.coupons.destroy', $c) }}" class="inline" onsubmit="return confirm('Hapus?');">@csrf @method('DELETE')<button class="text-rose-500 text-sm ml-2">×</button></form>
                    </td>
                </tr>
                @empty<tr><td colspan="7" class="text-center text-purple-400 py-8">Belum ada coupon.</td></tr>@endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $coupons->links() }}</div>
    </div>
</x-admin-layout>
