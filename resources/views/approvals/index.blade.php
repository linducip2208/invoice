<x-tabler.layout :title="__('Approval Inbox')" :pretitle="__('Approvals')" :subtitle="__('Request yang menunggu kamu approve')">
    <x-slot name="actions">
        <a href="{{ route('approval-rules.index') }}" class="btn">
            <x-ti name="settings" /> {{ __('Kelola Rules') }}
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Pending Approval') }} ({{ $pending->count() }})</h3>
        </div>
        @if($pending->isEmpty())
            <div class="card-body">
                <x-tabler.empty icon="inbox" :title="__('Tidak ada request menunggu approval.')" />
            </div>
        @else
            <div class="list-group list-group-flush">
                @foreach($pending as $req)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold">{{ $req->rule?->name ?? 'Rule dihapus' }}</div>
                                <div class="text-muted small mt-1">
                                    {{ __('Request') }} {{ class_basename($req->subject_type) }} #{{ $req->subject_id }}
                                    {{ __('oleh') }} <strong>{{ $req->requester?->name ?? 'Unknown' }}</strong>
                                    · {{ $req->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <span class="badge bg-yellow-lt">{{ __('Pending') }}</span>
                        </div>
                        <div class="row g-2 mt-2">
                            <div class="col-md-6">
                                <form method="POST" action="{{ route('approvals.approve', $req) }}" class="d-flex gap-2">
                                    @csrf
                                    <input name="comment" placeholder="Komen (opsional)" class="form-control">
                                    <button type="submit" class="btn btn-primary text-nowrap"><x-ti name="check" /> {{ __('Approve') }}</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form method="POST" action="{{ route('approvals.reject', $req) }}" onsubmit="return confirm('Reject request ini?')" class="d-flex gap-2">
                                    @csrf
                                    <input name="comment" required placeholder="Alasan reject (wajib)" class="form-control">
                                    <button type="submit" class="btn btn-danger text-nowrap"><x-ti name="x" /> {{ __('Reject') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if($past->isNotEmpty())
        <div class="card">
            <div class="card-header"><h3 class="card-title">{{ __('Riwayat (20 terakhir)') }}</h3></div>
            <div class="list-group list-group-flush">
                @foreach($past as $req)
                    <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <div class="fw-semibold">{{ $req->rule?->name ?? '—' }}</div>
                            <div class="text-muted small">{{ class_basename($req->subject_type) }} #{{ $req->subject_id }} · {{ $req->requester?->name }} · {{ $req->final_decision_at?->diffForHumans() }}</div>
                        </div>
                        <x-tabler.status-badge :status="$req->status" />
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-tabler.layout>
