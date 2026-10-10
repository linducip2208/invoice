<x-tabler.layout :title="$workspace->name" :pretitle="__('Superadmin')">
    <x-slot name="actions">
        <a href="{{ route('admin.workspaces.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back to Workspaces') }}
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-body">
            <h3 class="card-title">{{ $workspace->name }}</h3>
            <div class="text-muted">{{ $workspace->email }} · {{ $workspace->city }}, {{ $workspace->country }}</div>
            <div class="mt-2 d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-blue-lt">{{ $workspace->subscription?->plan?->name ?? __('Free') }}</span>
                @if($workspace->subscription?->status)
                    <x-tabler.status-badge :status="$workspace->subscription->status" />
                @endif
                @if($workspace->subscription?->payment_method === 'manual')
                    <span class="badge bg-pink-lt">{{ __('Manual') }}</span>
                @endif
                <span class="text-muted small">{{ __('Period ends') }}: {{ $workspace->subscription?->current_period_ends_at?->format('d M Y') ?? '∞' }}</span>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Assign Plan Manually') }}</h3>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">{{ __('Override the subscription directly — no transfer or proof submission needed.') }}</p>
            <form method="POST" action="{{ route('admin.workspaces.assign-plan', $workspace) }}">
                @csrf
                <div class="row g-2">
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Plan')" name="plan_id" required>
                            <select id="plan_id" name="plan_id" required class="form-select @error('plan_id') is-invalid @enderror">
                                @foreach($plans as $p)
                                    <option value="{{ $p->id }}" @selected($workspace->subscription?->plan_id === $p->id)>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <x-tabler.field :label="__('Period')" name="billing_period" required>
                            <select id="billing_period" name="billing_period" required class="form-select @error('billing_period') is-invalid @enderror">
                                <option value="monthly">{{ __('Monthly') }}</option>
                                <option value="yearly">{{ __('Yearly') }}</option>
                                <option value="free">{{ __('Free (no period)') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <x-tabler.field :label="__('Currency')" name="currency" required>
                            <select id="currency" name="currency" required class="form-select @error('currency') is-invalid @enderror">
                                <option value="IDR">IDR</option>
                                <option value="USD">USD</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field :label="__('Days valid')" name="period_days">
                            <input type="number" id="period_days" name="period_days" placeholder="{{ __('30 / 365 / custom') }}" class="form-control @error('period_days') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                </div>
                <label class="form-check">
                    <input type="checkbox" name="mark_paid" value="1" class="form-check-input">
                    <span class="form-check-label">{{ __('Mark as paid (generate an invoice record)') }}</span>
                </label>
                <div class="mt-3 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <x-ti name="refresh" /> {{ __('Assign & Activate') }}
                    </button>
                </div>
            </form>
            <div class="alert alert-warning mt-3 mb-0" role="alert">
                <div class="d-flex">
                    <div><x-ti name="alert-circle" /></div>
                    <div>{{ __('Use this when the user paid offline (outside the manual billing flow), or to grant a complimentary plan.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Team Members') }} ({{ $workspace->users->count() }})</h3>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($workspace->users as $u)
                        <div class="list-group-item d-flex align-items-center justify-content-between">
                            <span>{{ $u->name }} <span class="text-muted">· {{ $u->email }}</span></span>
                            <span class="badge bg-blue-lt">{{ $u->pivot->role }}</span>
                        </div>
                    @empty
                        <div class="list-group-item text-muted">{{ __('No members yet.') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Billing History') }}</h3>
                </div>
                @if($workspace->subscription?->invoices?->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <tbody>
                                @foreach($workspace->subscription->invoices->sortByDesc('created_at') as $inv)
                                    <tr>
                                        <td class="text-muted">{{ $inv->paid_at?->format('d M Y') ?? '—' }}</td>
                                        <td><code>{{ $inv->reference_no }}</code></td>
                                        <td class="text-end fw-semibold">{{ $inv->currency }} {{ number_format($inv->amount, 0) }}</td>
                                        <td class="text-end"><x-tabler.status-badge :status="$inv->status" /></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="card-body text-muted">{{ __('No billing history yet.') }}</div>
                @endif
            </div>
        </div>
    </div>
</x-tabler.layout>
