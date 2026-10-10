<x-tabler.layout :title="__('Manual Subscription')" :pretitle="__('Superadmin')" :subtitle="__('Override a plan for any workspace — no transfer or proof submission needed.')">
    <div class="card mb-3">
        <div class="card-body">
            <ul class="mb-0 text-muted small">
                <li>{{ __('Use case 1: user transferred offline directly (outside the manual billing flow)') }}</li>
                <li>{{ __('Use case 2: complimentary plan for early adopters or partners') }}</li>
                <li>{{ __('Use case 3: extend an existing subscription period') }}</li>
            </ul>
        </div>
    </div>

    <div x-data="{ tab: 'existing' }" class="mb-3">
        <div class="btn-list mb-3">
            <button type="button" @click="tab = 'existing'" :class="tab === 'existing' ? 'btn-primary' : ''" class="btn btn-sm">
                <x-ti name="refresh" /> {{ __('Assign to Existing Workspace') }}
            </button>
            <button type="button" @click="tab = 'newuser'" :class="tab === 'newuser' ? 'btn-primary' : ''" class="btn btn-sm">
                <x-ti name="plus" /> {{ __('New User + Subscription') }}
            </button>
        </div>

        <div x-show="tab === 'existing'" class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Quick Assign') }}</h3>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">{{ __('Pick the target workspace, plan, and period, then click Activate.') }}</p>
                <form method="POST" action="{{ route('admin.subscriptions.manual.store') }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-4">
                            <x-tabler.field :label="__('Workspace')" name="workspace_id" required>
                                <select id="workspace_id" name="workspace_id" required class="form-select @error('workspace_id') is-invalid @enderror">
                                    <option value="">{{ __('— Select workspace —') }}</option>
                                    @foreach($workspaces as $w)
                                        <option value="{{ $w->id }}">
                                            {{ $w->name }}
                                            · {{ $w->subscription?->plan?->name ?? 'Free' }} ({{ $w->subscription?->status ?? '—' }})
                                        </option>
                                    @endforeach
                                </select>
                            </x-tabler.field>
                        </div>
                        <div class="col-md-3">
                            <x-tabler.field :label="__('Plan')" name="plan_id" required>
                                <select id="plan_id" name="plan_id" required class="form-select @error('plan_id') is-invalid @enderror">
                                    @foreach($plans as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </x-tabler.field>
                        </div>
                        <div class="col-md-2">
                            <x-tabler.field :label="__('Period')" name="billing_period" required>
                                <select id="billing_period" name="billing_period" required class="form-select @error('billing_period') is-invalid @enderror">
                                    <option value="monthly">{{ __('Monthly') }}</option>
                                    <option value="yearly">{{ __('Yearly') }}</option>
                                    <option value="free">{{ __('Free') }}</option>
                                </select>
                            </x-tabler.field>
                        </div>
                        <div class="col-md-1">
                            <x-tabler.field :label="__('Currency')" name="currency" required>
                                <select id="currency" name="currency" required class="form-select @error('currency') is-invalid @enderror">
                                    <option value="IDR">IDR</option>
                                    <option value="USD">USD</option>
                                </select>
                            </x-tabler.field>
                        </div>
                        <div class="col-md-2">
                            <x-tabler.field :label="__('Days valid')" name="period_days">
                                <input type="number" id="period_days" name="period_days" placeholder="{{ __('30 / 365') }}"
                                    class="form-control @error('period_days') is-invalid @enderror">
                            </x-tabler.field>
                        </div>
                        <div class="col-12">
                            <x-tabler.field :label="__('Note (internal)')" name="note">
                                <input type="text" id="note" name="note" placeholder="{{ __('e.g. user transferred in the morning, ref BC123XYZ') }}"
                                    class="form-control @error('note') is-invalid @enderror">
                            </x-tabler.field>
                        </div>
                    </div>
                    <label class="form-check">
                        <input type="checkbox" name="mark_paid" value="1" class="form-check-input">
                        <span class="form-check-label"><strong>{{ __('Mark as paid') }}</strong> — {{ __('generate a billing invoice record') }}</span>
                    </label>
                    <div class="mt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <x-ti name="refresh" /> {{ __('Activate Subscription') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="tab === 'newuser'" x-cloak class="card">
            <div class="card-body" x-data="{ mode: 'new_user' }">
                <h3 class="card-title mb-1">{{ __('New User + Workspace + Subscription') }}</h3>
                <p class="text-muted mb-3">{{ __('For users who transferred offline without registering first.') }}</p>
                <form method="POST" action="{{ route('admin.subscriptions.manual.create') }}">
                    @csrf
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-selectgroup-item">
                                <input type="radio" name="mode" value="new_user" x-model="mode" class="form-selectgroup-input">
                                <span class="form-selectgroup-label text-center">
                                    <span class="d-block fw-semibold">{{ __('New User') }}</span>
                                    <span class="d-block text-muted small">{{ __('No account yet') }}</span>
                                </span>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="form-selectgroup-item">
                                <input type="radio" name="mode" value="existing_user" x-model="mode" class="form-selectgroup-input">
                                <span class="form-selectgroup-label text-center">
                                    <span class="d-block fw-semibold">{{ __('Existing User') }}</span>
                                    <span class="d-block text-muted small">{{ __('Add a new workspace') }}</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div x-show="mode === 'new_user'" class="row g-2">
                        <div class="col-md-4">
                            <x-tabler.field :label="__('Full name')" name="new_user_name">
                                <input type="text" id="new_user_name" name="new_user_name" placeholder="{{ __('Full name') }}"
                                    class="form-control @error('new_user_name') is-invalid @enderror">
                            </x-tabler.field>
                        </div>
                        <div class="col-md-4">
                            <x-tabler.field :label="__('Email')" name="new_user_email">
                                <input type="email" id="new_user_email" name="new_user_email" placeholder="user@example.com"
                                    class="form-control @error('new_user_email') is-invalid @enderror">
                            </x-tabler.field>
                        </div>
                        <div class="col-md-4">
                            <x-tabler.field :label="__('Temporary password')" name="new_user_password" :help="__('The user changes it on first login.')">
                                <input type="text" id="new_user_password" name="new_user_password" value="ChangeMe123"
                                    class="form-control @error('new_user_password') is-invalid @enderror">
                            </x-tabler.field>
                        </div>
                    </div>

                    <div x-show="mode === 'existing_user'" x-cloak>
                        <x-tabler.field :label="__('Select user')" name="existing_user_id">
                            <select id="existing_user_id" name="existing_user_id" class="form-select @error('existing_user_id') is-invalid @enderror">
                                <option value="">{{ __('— Find user —') }}</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} · {{ $u->email }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>

                    <hr>

                    <x-tabler.field :label="__('Workspace / company name')" name="workspace_name" required>
                        <input type="text" id="workspace_name" name="workspace_name" required
                            placeholder="{{ __('Company name') }}" class="form-control @error('workspace_name') is-invalid @enderror">
                    </x-tabler.field>

                    <div class="row g-2">
                        <div class="col-md-3">
                            <x-tabler.field :label="__('Plan')" name="plan_id" required>
                                <select id="manual_plan_id" name="plan_id" required class="form-select @error('plan_id') is-invalid @enderror">
                                    @foreach($plans as $p)
                                        <option value="{{ $p->id }}" @selected($p->slug === 'pro')>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </x-tabler.field>
                        </div>
                        <div class="col-md-3">
                            <x-tabler.field :label="__('Period')" name="billing_period" required>
                                <select id="manual_billing_period" name="billing_period" required class="form-select @error('billing_period') is-invalid @enderror">
                                    <option value="monthly">{{ __('Monthly') }}</option>
                                    <option value="yearly">{{ __('Yearly') }}</option>
                                    <option value="free">{{ __('Free') }}</option>
                                </select>
                            </x-tabler.field>
                        </div>
                        <div class="col-md-3">
                            <x-tabler.field :label="__('Currency')" name="currency" required>
                                <select id="manual_currency" name="currency" required class="form-select @error('currency') is-invalid @enderror">
                                    <option value="IDR">IDR</option>
                                    <option value="USD">USD</option>
                                </select>
                            </x-tabler.field>
                        </div>
                        <div class="col-md-3">
                            <x-tabler.field :label="__('Days valid')" name="period_days">
                                <input type="number" id="manual_period_days" name="period_days" placeholder="{{ __('30 / 365') }}"
                                    class="form-control @error('period_days') is-invalid @enderror">
                            </x-tabler.field>
                        </div>
                    </div>

                    <x-tabler.field :label="__('Payment note (for audit)')" name="note">
                        <textarea id="manual_note" name="note" rows="2" class="form-control @error('note') is-invalid @enderror"
                            placeholder="{{ __('Transfer reference, contact channel, etc.') }}"></textarea>
                    </x-tabler.field>

                    <label class="form-check">
                        <input type="checkbox" name="mark_paid" value="1" checked class="form-check-input">
                        <span class="form-check-label"><strong>{{ __('Mark as paid') }}</strong> — {{ __('generate a billing invoice record') }}</span>
                    </label>

                    <div class="mt-3 pt-3 border-top d-flex justify-content-end gap-2">
                        <button type="reset" class="btn">{{ __('Reset') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <x-ti name="plus" /> {{ __('Create User + Activate Subscription') }}
                        </button>
                    </div>

                    <div class="alert alert-success mt-3 mb-0" role="alert">
                        <div class="d-flex">
                            <div><x-ti name="check" /></div>
                            <div>{{ __('After submit: notify the user by email. They log in with the email + temporary password above.') }}</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($pending->isNotEmpty())
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Pending From Users') }} ({{ $pending->count() }})</h3>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">{{ __('These users picked a plan and are waiting for verification.') }}
                    <a href="{{ route('admin.manual-payments.index') }}">{{ __('Process in Manual Payments') }}</a>
                </p>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('Workspace') }}</th>
                            <th>{{ __('Plan') }}</th>
                            <th>{{ __('Period') }}</th>
                            <th>{{ __('Proof') }}</th>
                            <th>{{ __('Note') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pending as $sub)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.workspaces.show', $sub->workspace) }}" class="fw-semibold">{{ $sub->workspace?->name }}</a>
                                </td>
                                <td class="text-muted">{{ $sub->plan?->name }}</td>
                                <td class="text-muted text-capitalize">{{ $sub->billing_period }} ({{ $sub->currency }})</td>
                                <td>
                                    @if($sub->manual_proof_path)
                                        <a href="{{ asset('storage/' . $sub->manual_proof_path) }}" target="_blank">{{ __('View') }}</a>
                                    @else
                                        <span class="text-muted small">{{ __('None') }}</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ Str::limit($sub->manual_payment_note, 50) ?: '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.manual-payments.index') }}" class="btn btn-sm">{{ __('Process') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('All Workspaces') }}</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Plan') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Method') }}</th>
                        <th>{{ __('Period Ends') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workspaces as $w)
                        @php $sub = $w->subscription; @endphp
                        <tr>
                            <td class="fw-semibold">{{ $w->name }}</td>
                            <td><span class="badge bg-blue-lt">{{ $sub?->plan?->name ?? __('Free') }}</span></td>
                            <td>
                                @if($sub?->status)
                                    <x-tabler.status-badge :status="$sub->status" />
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($sub?->payment_method === 'manual')
                                    <span class="badge bg-pink-lt">{{ __('Manual') }}</span>
                                @elseif($sub?->payment_method === 'gateway')
                                    <span class="badge bg-cyan-lt">{{ __('Gateway') }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $sub?->current_period_ends_at?->format('d M Y') ?? '∞' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.workspaces.show', $w) }}" class="btn btn-sm">{{ __('Manage') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-tabler.layout>
