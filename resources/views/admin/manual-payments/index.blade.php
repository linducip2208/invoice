<x-admin-layout title="Manual Payments">

    <!-- Pending verifikasi -->
    <div class="card-unicorn mb-6">
        <h3 class="font-display font-bold text-lg mb-3">⏳ Pending Verification ({{ $pending->count() }})</h3>
        @if($pending->isEmpty())
            <p class="text-purple-400 text-sm py-6 text-center">Belum ada pembayaran manual menunggu verifikasi.</p>
        @else
            <table class="table-premium">
                <thead><tr><th>Workspace</th><th>Plan</th><th>Period</th><th>Amount</th><th>Bukti</th><th>Note</th><th>Submitted</th><th></th></tr></thead>
                <tbody>
                    @foreach($pending as $sub)
                    @php $amt = $sub->plan->priceFor($sub->currency, $sub->billing_period); @endphp
                    <tr>
                        <td class="font-bold">{{ $sub->workspace?->name }}</td>
                        <td>{{ $sub->plan?->name }}</td>
                        <td class="capitalize">{{ $sub->billing_period }}</td>
                        <td class="font-bold">{{ $sub->currency }} {{ number_format($amt, 0) }}</td>
                        <td><a href="{{ asset('storage/' . $sub->manual_proof_path) }}" target="_blank" class="text-pink-600 font-semibold text-sm">📄 View</a></td>
                        <td class="text-xs text-purple-500 max-w-xs">{{ Str::limit($sub->manual_payment_note, 60) }}</td>
                        <td class="text-xs text-purple-500">{{ $sub->updated_at->diffForHumans() }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.manual-payments.approve', $sub) }}" class="inline" onsubmit="return confirm('Approve & aktifkan?');">@csrf<button class="text-emerald-600 font-bold text-sm">✓ Approve</button></form>
                            <form method="POST" action="{{ route('admin.manual-payments.reject', $sub) }}" class="inline" onsubmit="return confirm('Reject pembayaran?');">@csrf<button class="text-rose-500 font-bold text-sm ml-2">✗ Reject</button></form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Setup metode pembayaran -->
    <div class="card-unicorn mb-6">
        <h3 class="font-display font-bold text-lg mb-3">+ Tambah Metode Pembayaran Manual</h3>
        <form method="POST" action="{{ route('admin.manual-payments.methods.store') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
            @csrf
            <input type="text" name="label" placeholder="Label (mis: BCA Utama)" required class="input-premium">
            <select name="type" required class="input-premium">
                <option value="bank">🏦 Bank</option>
                <option value="ewallet">📱 E-Wallet</option>
                <option value="crypto">₿ Crypto</option>
                <option value="other">💳 Other</option>
            </select>
            <input type="text" name="bank_or_provider" placeholder="BCA / GoPay / dll" class="input-premium">
            <input type="text" name="account_number" placeholder="No rek / akun" class="input-premium">
            <input type="text" name="account_name" placeholder="Atas nama" class="input-premium">
            <input type="number" name="sort_order" placeholder="Urut" value="0" class="input-premium">
            <textarea name="instructions" rows="2" placeholder="Instruksi tambahan (opsional)" class="input-premium md:col-span-5"></textarea>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked> Active</label>
            <button class="btn-glow md:col-span-1">Tambah</button>
        </form>
    </div>

    <!-- List metode -->
    <div class="card-unicorn">
        <h3 class="font-display font-bold text-lg mb-3">Metode Pembayaran Aktif</h3>
        @if($methods->isEmpty())
            <p class="text-purple-400 text-sm py-6 text-center">Belum ada metode. Tambah di atas — user akan lihat ini saat pilih "Bayar Manual".</p>
        @else
            <table class="table-premium">
                <thead><tr><th>Label</th><th>Type</th><th>Provider</th><th>Account</th><th>A.N.</th><th>Active</th><th></th></tr></thead>
                <tbody>
                    @foreach($methods as $m)
                    <tr>
                        <td class="font-bold">{{ $m->label }}</td>
                        <td><span class="pill pill-purple">{{ ucfirst($m->type) }}</span></td>
                        <td>{{ $m->bank_or_provider }}</td>
                        <td><code class="font-mono text-sm">{{ $m->account_number }}</code></td>
                        <td>{{ $m->account_name }}</td>
                        <td>{!! $m->is_active ? '<span class="pill pill-emerald">Active</span>' : '<span class="pill pill-rose">Off</span>' !!}</td>
                        <td><form method="POST" action="{{ route('admin.manual-payments.methods.destroy', $m) }}" onsubmit="return confirm('Hapus?');">@csrf @method('DELETE')<button class="text-rose-500 text-sm">×</button></form></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-admin-layout>
