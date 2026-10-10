<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Approval Inbox') }} <span class="text-2xl">📥</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Request yang menunggu kamu approve') }}</p>
            </div>
            <a href="{{ route('approval-rules.index') }}" class="btn-unicorn-secondary text-sm">⚙️ {{ __('Kelola Rules') }}</a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl bg-rose-50 border-2 border-rose-200 p-4 text-rose-800">⚠️ {{ session('error') }}</div>
        @endif

        {{-- Pending --}}
        <div class="card-unicorn !p-0 overflow-hidden">
            <div class="p-6 border-b border-purple-100">
                <h3 class="font-display font-bold text-lg text-purple-900 flex items-center gap-2">
                    <span class="text-amber-400">⏳</span> {{ __('Pending Approval') }} ({{ $pending->count() }})
                </h3>
            </div>
            @if($pending->isEmpty())
                <p class="p-8 text-center text-purple-400 text-sm">🎉 Tidak ada request menunggu approval.</p>
            @else
                <div class="divide-y divide-purple-100">
                    @foreach($pending as $req)
                        <div class="p-5">
                            <div class="flex items-start gap-3 flex-wrap">
                                <div class="flex-1 min-w-0">
                                    <p class="font-display font-bold text-purple-900">{{ $req->rule?->name ?? 'Rule dihapus' }}</p>
                                    <p class="text-sm text-purple-600 mt-1">
                                        Request {{ class_basename($req->subject_type) }} #{{ $req->subject_id }}
                                        oleh <strong>{{ $req->requester?->name ?? 'Unknown' }}</strong>
                                        · {{ $req->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <span class="badge-gold text-xs">⏳ Pending</span>
                            </div>

                            <div class="mt-4 flex items-end gap-2 flex-wrap">
                                <form method="POST" action="{{ route('approvals.approve', $req) }}" class="flex-1 flex items-end gap-2 min-w-0">
                                    @csrf
                                    <input name="comment" placeholder="Komen (opsional)" class="input-unicorn rounded-xl px-3 py-2 text-sm flex-1">
                                    <button type="submit" class="btn-unicorn text-sm shrink-0">✓ Approve</button>
                                </form>
                                <form method="POST" action="{{ route('approvals.reject', $req) }}" onsubmit="return confirm('Reject request ini?')" class="flex-1 flex items-end gap-2 min-w-0">
                                    @csrf
                                    <input name="comment" required placeholder="Alasan reject (wajib)" class="input-unicorn rounded-xl px-3 py-2 text-sm flex-1">
                                    <button type="submit" class="text-sm bg-rose-500 text-white px-4 py-2 rounded-xl font-bold hover:bg-rose-600 shrink-0">✗ Reject</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Past --}}
        @if($past->isNotEmpty())
            <div class="card-unicorn !p-0 overflow-hidden">
                <div class="p-6 border-b border-purple-100">
                    <h3 class="font-display font-bold text-lg text-purple-900">{{ __('Riwayat (20 terakhir)') }}</h3>
                </div>
                <div class="divide-y divide-purple-100">
                    @foreach($past as $req)
                        <div class="p-4 flex items-center justify-between flex-wrap gap-3 text-sm">
                            <div>
                                <p class="font-semibold text-purple-900">{{ $req->rule?->name ?? '—' }}</p>
                                <p class="text-xs text-purple-500">{{ class_basename($req->subject_type) }} #{{ $req->subject_id }} · {{ $req->requester?->name }} · {{ $req->final_decision_at?->diffForHumans() }}</p>
                            </div>
                            <span class="badge-{{ $req->status === 'approved' ? 'mint' : 'red' }} text-xs">
                                {{ $req->status === 'approved' ? '✓ Approved' : '✗ Rejected' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
