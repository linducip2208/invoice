<x-admin-layout title="{{ $workspace->name }}">
    <div class="glass-card p-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h3 class="h-display heading-gradient text-2xl font-bold">{{ $workspace->name }}</h3>
                <p class="text-sm text-purple-500 mt-1">{{ $workspace->email }} · {{ $workspace->city }}, {{ $workspace->country }}</p>
                <div class="mt-3 flex items-center gap-2 flex-wrap text-sm">
                    <span class="pill pill-purple">{{ $workspace->subscription?->plan?->name ?? 'Free' }}</span>
                    <span class="pill pill-{{ $workspace->subscription?->status === 'active' ? 'emerald' : 'amber' }}">{{ ucfirst($workspace->subscription?->status ?? '—') }}</span>
                    @if($workspace->subscription?->payment_method === 'manual')<span class="pill pill-pink">💸 Manual</span>@endif
                    <span class="text-xs text-purple-400">Period ends: {{ $workspace->subscription?->current_period_ends_at?->format('d M Y') ?? '∞' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Manual Assign Plan -->
    <div class="mt-6 gradient-border-card">
        <div class="inner">
            <h3 class="h-display font-bold text-lg text-purple-900 mb-1">⚡ Assign Plan Manual</h3>
            <p class="text-sm text-purple-500 mb-4">Override subscription langsung — tanpa user perlu transfer / submit bukti.</p>

            <form method="POST" action="{{ route('admin.workspaces.assign-plan', $workspace) }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                @csrf
                <div class="md:col-span-2">
                    <label class="text-xs font-bold text-purple-700">Plan *</label>
                    <select name="plan_id" required class="input-premium w-full mt-1">
                        @foreach($plans as $p)
                            <option value="{{ $p->id }}" {{ $workspace->subscription?->plan_id === $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-purple-700">Period *</label>
                    <select name="billing_period" required class="input-premium w-full mt-1">
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                        <option value="free">Free (no period)</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-purple-700">Currency *</label>
                    <select name="currency" required class="input-premium w-full mt-1">
                        <option value="IDR">IDR</option>
                        <option value="USD">USD</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-purple-700">Days valid</label>
                    <input type="number" name="period_days" placeholder="30 / 365 / custom" class="input-premium w-full mt-1">
                </div>
                <label class="flex items-center gap-2 text-sm text-purple-700 md:col-span-3">
                    <input type="checkbox" name="mark_paid" value="1" class="rounded border-purple-300">
                    Tandai sebagai sudah dibayar (generate invoice record)
                </label>
                <div class="md:col-span-2 flex justify-end">
                    <button class="btn-glow">⚡ Assign & Activate</button>
                </div>
            </form>

            <div class="mt-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
                ⚠️ <strong>Use case:</strong> kalau user sudah transfer offline (di luar /billing/manual flow), kasih akses langsung di sini. Atau kasih komp gratis untuk user khusus.
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="glass-card p-5">
            <h4 class="h-display font-bold text-lg mb-3">👥 Team Members ({{ $workspace->users->count() }})</h4>
            <ul class="text-sm space-y-1">
                @forelse($workspace->users as $u)
                    <li class="flex items-center justify-between"><span>{{ $u->name }} <span class="text-purple-400">· {{ $u->email }}</span></span><span class="pill pill-purple text-[10px]">{{ $u->pivot->role }}</span></li>
                @empty
                    <li class="text-purple-400">Belum ada anggota.</li>
                @endforelse
            </ul>
        </div>
        <div class="glass-card p-5">
            <h4 class="h-display font-bold text-lg mb-3">💳 Billing History</h4>
            @if($workspace->subscription?->invoices?->isNotEmpty())
                <table class="w-full text-sm">
                    @foreach($workspace->subscription->invoices->sortByDesc('created_at') as $inv)
                        <tr class="border-b border-purple-100/50"><td class="py-2">{{ $inv->paid_at?->format('d M Y') ?? '—' }}</td><td class="font-mono text-xs">{{ $inv->reference_no }}</td><td class="text-right font-bold">{{ $inv->currency }} {{ number_format($inv->amount, 0) }}</td><td class="text-right"><span class="pill pill-{{ $inv->status === 'paid' ? 'emerald' : 'amber' }}">{{ $inv->status }}</span></td></tr>
                    @endforeach
                </table>
            @else
                <p class="text-purple-400 text-sm">Belum ada billing history.</p>
            @endif
        </div>
    </div>
</x-admin-layout>
