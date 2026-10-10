<x-admin-layout title="Manual Subscription">

    <!-- Hero -->
    <div class="hero-dark mb-6">
        <div class="flex items-center gap-3 mb-3">
            <span class="text-4xl">⚡</span>
            <div>
                <h2 class="h-display text-2xl font-bold">Assign Subscription Manual</h2>
                <p class="text-sm opacity-80">Override plan untuk workspace mana pun — tanpa user perlu transfer / submit bukti</p>
            </div>
        </div>
        <ul class="text-xs opacity-90 space-y-1">
            <li>✦ Use case 1: User transfer offline langsung ke rek bisnis (di luar /billing/manual flow)</li>
            <li>✦ Use case 2: Komp gratis untuk early adopter / influencer / kerja sama</li>
            <li>✦ Use case 3: Extend periode subscription user existing</li>
        </ul>
    </div>

    <!-- TAB SWITCHER -->
    <div x-data="{ tab: 'existing' }" class="mb-6">
        <div class="flex items-center gap-2 mb-4">
            <button @click="tab = 'existing'" :class="tab === 'existing' ? 'btn-glow' : 'btn-ghost'" class="text-sm">
                ⚡ Assign ke Workspace Existing
            </button>
            <button @click="tab = 'newuser'" :class="tab === 'newuser' ? 'btn-glow' : 'btn-ghost'" class="text-sm">
                ➕ Buat User Baru + Subscription
            </button>
        </div>

        <!-- TAB 1: assign to existing workspace -->
        <div x-show="tab === 'existing'" class="gradient-border-card">
            <div class="inner">
            <h3 class="h-display font-bold text-lg text-purple-900 mb-1">+ Quick Assign</h3>
            <p class="text-sm text-purple-500 mb-4">Pilih workspace target, plan, periode → klik Activate.</p>

            <form method="POST" action="{{ route('admin.subscriptions.manual.store') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3">
                @csrf
                <div class="md:col-span-4">
                    <label class="text-xs font-bold text-purple-700">Workspace *</label>
                    <select name="workspace_id" required class="input-premium w-full mt-1">
                        <option value="">— Pilih workspace —</option>
                        @foreach($workspaces as $w)
                            <option value="{{ $w->id }}">
                                {{ $w->name }}
                                · {{ $w->subscription?->plan?->name ?? 'Free' }} ({{ $w->subscription?->status ?? '—' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="text-xs font-bold text-purple-700">Plan *</label>
                    <select name="plan_id" required class="input-premium w-full mt-1">
                        @foreach($plans as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs font-bold text-purple-700">Period *</label>
                    <select name="billing_period" required class="input-premium w-full mt-1">
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                        <option value="free">Free</option>
                    </select>
                </div>
                <div class="md:col-span-1">
                    <label class="text-xs font-bold text-purple-700">Currency</label>
                    <select name="currency" required class="input-premium w-full mt-1">
                        <option value="IDR">IDR</option>
                        <option value="USD">USD</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs font-bold text-purple-700">Days valid</label>
                    <input type="number" name="period_days" placeholder="30 / 365" class="input-premium w-full mt-1">
                </div>
                <div class="md:col-span-12">
                    <label class="text-xs font-bold text-purple-700">Note (internal)</label>
                    <input type="text" name="note" placeholder="Misal: User transfer Rp99.000 ke BCA pagi ini, ref BC123XYZ" class="input-premium w-full mt-1">
                </div>
                <label class="md:col-span-6 flex items-center gap-2 text-sm text-purple-700">
                    <input type="checkbox" name="mark_paid" value="1" class="rounded border-purple-300">
                    <strong>Tandai sebagai sudah dibayar</strong> — generate billing invoice record
                </label>
                <div class="md:col-span-6 flex justify-end">
                    <button class="btn-glow">⚡ Activate Subscription</button>
                </div>
            </form>
            </div>
        </div>

        <!-- TAB 2: create new user + workspace + subscription -->
        <div x-show="tab === 'newuser'" x-cloak class="gradient-border-card">
            <div class="inner" x-data="{ mode: 'new_user' }">
                <h3 class="h-display font-bold text-lg text-purple-900 mb-1">➕ Buat User Baru + Workspace + Subscription</h3>
                <p class="text-sm text-purple-500 mb-4">Untuk user yang transfer manual offline tanpa register dulu. Saya bikinkan akun, workspace, dan langsung aktifkan subscription.</p>

                <form method="POST" action="{{ route('admin.subscriptions.manual.create') }}" class="space-y-4">
                    @csrf

                    <!-- USER PICKER -->
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="mode" value="new_user" x-model="mode" class="sr-only peer">
                            <div class="rounded-xl border-2 border-purple-100 peer-checked:border-pink-400 peer-checked:bg-pink-50 p-3 text-center transition">
                                <div class="text-2xl">🆕</div>
                                <p class="text-sm font-bold text-purple-900">User Baru</p>
                                <p class="text-xs text-purple-500">Belum punya akun</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="mode" value="existing_user" x-model="mode" class="sr-only peer">
                            <div class="rounded-xl border-2 border-purple-100 peer-checked:border-pink-400 peer-checked:bg-pink-50 p-3 text-center transition">
                                <div class="text-2xl">👤</div>
                                <p class="text-sm font-bold text-purple-900">User Existing</p>
                                <p class="text-xs text-purple-500">Sudah punya akun, tambah workspace baru</p>
                            </div>
                        </label>
                    </div>

                    <!-- NEW USER FIELDS -->
                    <div x-show="mode === 'new_user'" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="text-xs font-bold text-purple-700">Nama lengkap *</label>
                            <input type="text" name="new_user_name" placeholder="Budi Santoso" class="input-premium w-full mt-1">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-purple-700">Email *</label>
                            <input type="email" name="new_user_email" placeholder="budi@example.com" class="input-premium w-full mt-1">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-purple-700">Password sementara *</label>
                            <input type="text" name="new_user_password" value="ChangeMe123" class="input-premium w-full mt-1">
                            <p class="text-[10px] text-purple-400 mt-1">User ganti sendiri saat login pertama.</p>
                        </div>
                    </div>

                    <!-- EXISTING USER PICKER -->
                    <div x-show="mode === 'existing_user'" x-cloak>
                        <label class="text-xs font-bold text-purple-700">Pilih user *</label>
                        <select name="existing_user_id" class="input-premium w-full mt-1">
                            <option value="">— Cari user —</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} · {{ $u->email }}</option>
                            @endforeach
                        </select>
                    </div>

                    <hr class="border-purple-100">

                    <!-- WORKSPACE NAME -->
                    <div>
                        <label class="text-xs font-bold text-purple-700">Nama workspace / perusahaan *</label>
                        <input type="text" name="workspace_name" placeholder="PT Toko Maju Jaya" required class="input-premium w-full mt-1">
                    </div>

                    <!-- PLAN + PERIOD -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="text-xs font-bold text-purple-700">Plan *</label>
                            <select name="plan_id" required class="input-premium w-full mt-1">
                                @foreach($plans as $p)
                                    <option value="{{ $p->id }}" {{ $p->slug === 'pro' ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-purple-700">Period *</label>
                            <select name="billing_period" required class="input-premium w-full mt-1">
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                                <option value="free">Free</option>
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
                            <input type="number" name="period_days" placeholder="30 / 365" class="input-premium w-full mt-1">
                        </div>
                    </div>

                    <!-- NOTE + MARK PAID -->
                    <div>
                        <label class="text-xs font-bold text-purple-700">Note pembayaran (untuk audit)</label>
                        <textarea name="note" rows="2" class="input-premium w-full mt-1" placeholder="Misal: User transfer Rp99k via BCA 2026-05-07 ref BC123ABC, kontak via WA"></textarea>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-purple-700">
                        <input type="checkbox" name="mark_paid" value="1" checked class="rounded border-purple-300">
                        <strong>Tandai sebagai sudah dibayar</strong> — generate billing invoice record (recommended kalau memang sudah transfer)
                    </label>

                    <div class="flex justify-end gap-3 pt-2 border-t border-purple-100">
                        <button type="reset" class="btn-ghost">Reset</button>
                        <button type="submit" class="btn-glow">➕ Create User + Activate Subscription</button>
                    </div>

                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800">
                        ✅ {{ __('Setelah submit: user akan diberi tahu via email. Login pakai email + password sementara di atas. Workspace siap pakai.') }}
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pending verifikasi (dari /billing/manual flow) -->
    @if($pending->isNotEmpty())
    <div class="glass-card p-5 mb-6">
        <h3 class="h-display font-bold text-lg text-purple-900 mb-3">⏳ Pending dari User ({{ $pending->count() }})</h3>
        <p class="text-sm text-purple-500 mb-3">User ini sudah pilih plan & menunggu verifikasi. Untuk yang upload bukti via /billing/manual, lihat di <a href="{{ route('admin.manual-payments.index') }}" class="text-pink-600 font-bold">/admin/manual-payments</a>.</p>
        <table class="table-premium">
            <thead><tr><th>Workspace</th><th>Plan</th><th>Period</th><th>Bukti</th><th>Note</th><th></th></tr></thead>
            <tbody>
                @foreach($pending as $sub)
                <tr>
                    <td><a href="{{ route('admin.workspaces.show', $sub->workspace) }}" class="font-bold text-purple-700 hover:text-pink-500">{{ $sub->workspace?->name }}</a></td>
                    <td>{{ $sub->plan?->name }}</td>
                    <td class="capitalize">{{ $sub->billing_period }} ({{ $sub->currency }})</td>
                    <td>@if($sub->manual_proof_path)<a href="{{ asset('storage/' . $sub->manual_proof_path) }}" target="_blank" class="text-pink-600 text-sm">📄 View</a>@else<span class="text-purple-400 text-xs">Tidak ada</span>@endif</td>
                    <td class="text-xs text-purple-500 max-w-xs">{{ Str::limit($sub->manual_payment_note, 50) ?: '—' }}</td>
                    <td><a href="{{ route('admin.manual-payments.index') }}" class="text-pink-600 font-bold text-sm">Process →</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Workspace listing dengan plan saat ini -->
    <div class="glass-card p-5">
        <h3 class="h-display font-bold text-lg text-purple-900 mb-3">📋 Semua Workspace</h3>
        <table class="table-premium">
            <thead><tr><th>Name</th><th>Plan</th><th>Status</th><th>Method</th><th>Period Ends</th><th></th></tr></thead>
            <tbody>
                @foreach($workspaces as $w)
                @php $sub = $w->subscription; @endphp
                <tr>
                    <td class="font-bold">{{ $w->name }}</td>
                    <td><span class="pill pill-purple">{{ $sub?->plan?->name ?? 'Free' }}</span></td>
                    <td><span class="pill pill-{{ $sub?->status === 'active' ? 'emerald' : ($sub?->status === 'pending' ? 'amber' : 'rose') }}">{{ ucfirst($sub?->status ?? 'free') }}</span></td>
                    <td>
                        @if($sub?->payment_method === 'manual')<span class="pill pill-pink">💸 Manual</span>
                        @elseif($sub?->payment_method === 'gateway')<span class="pill pill-sky">💳 Gateway</span>
                        @else<span class="text-purple-400 text-xs">—</span>@endif
                    </td>
                    <td class="text-xs text-purple-500">{{ $sub?->current_period_ends_at?->format('d M Y') ?? '∞' }}</td>
                    <td><a href="{{ route('admin.workspaces.show', $w) }}" class="text-purple-600 hover:text-pink-500 text-sm font-semibold">Manage →</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
