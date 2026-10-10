<x-tabler.admin-layout :title="__('Manual Payments')" :pretitle="__('Superadmin')">
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Pending Verification') }} ({{ $pending->count() }})</h3>
        </div>
        @if($pending->isEmpty())
            <div class="card-body">
                <x-tabler.empty icon="clock" :title="__('No pending payments')"
                    :body="__('No manual payments are waiting for verification.')" />
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('Workspace') }}</th>
                            <th>{{ __('Plan') }}</th>
                            <th>{{ __('Period') }}</th>
                            <th class="text-end">{{ __('Amount') }}</th>
                            <th>{{ __('Proof') }}</th>
                            <th>{{ __('Note') }}</th>
                            <th>{{ __('Submitted') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pending as $sub)
                            @php $amt = $sub->plan->priceFor($sub->currency, $sub->billing_period); @endphp
                            <tr>
                                <td class="fw-semibold">{{ $sub->workspace?->name }}</td>
                                <td class="text-muted">{{ $sub->plan?->name }}</td>
                                <td class="text-muted text-capitalize">{{ $sub->billing_period }}</td>
                                <td class="text-end fw-semibold">{{ $sub->currency }} {{ number_format($amt, 0) }}</td>
                                <td>
                                    <a href="{{ asset('storage/' . $sub->manual_proof_path) }}" target="_blank" class="btn btn-sm">
                                        <x-ti name="eye" /> {{ __('View') }}
                                    </a>
                                </td>
                                <td class="text-muted small">{{ Str::limit($sub->manual_payment_note, 60) }}</td>
                                <td class="text-muted small">{{ $sub->updated_at->diffForHumans() }}</td>
                                <td class="text-end text-nowrap">
                                    <form method="POST" action="{{ route('admin.manual-payments.approve', $sub) }}" class="d-inline"
                                        onsubmit="return confirm({{ Js::from(__('Approve and activate?')) }});">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <x-ti name="check" /> {{ __('Approve') }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.manual-payments.reject', $sub) }}" class="d-inline"
                                        onsubmit="return confirm({{ Js::from(__('Reject this payment?')) }});">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <x-ti name="x" /> {{ __('Reject') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Add Manual Payment Method') }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.manual-payments.methods.store') }}">
                @csrf
                <div class="row g-2">
                    <div class="col-md-2">
                        <x-tabler.field :label="__('Label')" name="label" required>
                            <input type="text" id="label" name="label" required placeholder="{{ __('e.g. Main BCA') }}"
                                class="form-control @error('label') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field :label="__('Type')" name="type" required>
                            <select id="type" name="type" required class="form-select @error('type') is-invalid @enderror">
                                <option value="bank">{{ __('Bank') }}</option>
                                <option value="ewallet">{{ __('E-Wallet') }}</option>
                                <option value="crypto">{{ __('Crypto') }}</option>
                                <option value="other">{{ __('Other') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field :label="__('Provider')" name="bank_or_provider">
                            <input type="text" id="bank_or_provider" name="bank_or_provider" placeholder="{{ __('BCA / GoPay / etc.') }}"
                                class="form-control @error('bank_or_provider') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field :label="__('Account number')" name="account_number">
                            <input type="text" id="account_number" name="account_number" placeholder="{{ __('Account no.') }}"
                                class="form-control @error('account_number') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field :label="__('Account name')" name="account_name">
                            <input type="text" id="account_name" name="account_name" placeholder="{{ __('Holder name') }}"
                                class="form-control @error('account_name') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field :label="__('Order')" name="sort_order">
                            <input type="number" id="sort_order" name="sort_order" value="0"
                                class="form-control @error('sort_order') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-9">
                        <x-tabler.field :label="__('Instructions')" name="instructions">
                            <textarea id="instructions" name="instructions" rows="2" placeholder="{{ __('Additional instructions (optional)') }}"
                                class="form-control @error('instructions') is-invalid @enderror"></textarea>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <label class="form-check form-switch mt-4">
                            <input type="checkbox" name="is_active" value="1" checked class="form-check-input">
                            <span class="form-check-label">{{ __('Active') }}</span>
                        </label>
                        <div class="mt-2 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <x-ti name="plus" /> {{ __('Add') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Active Payment Methods') }}</h3>
        </div>
        @if($methods->isEmpty())
            <div class="card-body">
                <x-tabler.empty icon="cash" :title="__('No payment methods')"
                    :body="__('Add one above — users will see it when choosing manual payment.')" />
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('Label') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Provider') }}</th>
                            <th>{{ __('Account') }}</th>
                            <th>{{ __('Holder') }}</th>
                            <th>{{ __('Active') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($methods as $m)
                            <tr>
                                <td class="fw-semibold">{{ $m->label }}</td>
                                <td><span class="badge bg-blue-lt">{{ ucfirst($m->type) }}</span></td>
                                <td class="text-muted">{{ $m->bank_or_provider }}</td>
                                <td><code>{{ $m->account_number }}</code></td>
                                <td class="text-muted">{{ $m->account_name }}</td>
                                <td>
                                    @if($m->is_active)
                                        <span class="badge bg-green-lt">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-red-lt">{{ __('Off') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <x-tabler.delete-button :action="route('admin.manual-payments.methods.destroy', $m)"
                                        :confirm="__('Delete this method?')" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-tabler.admin-layout>
