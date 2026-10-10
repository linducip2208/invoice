<x-tabler.layout :title="__('Subscriptions')" :pretitle="__('Superadmin')">
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.subscriptions.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Status')" name="status">
                            <select id="status" name="status" class="form-select">
                                <option value="">{{ __('All statuses') }}</option>
                                @foreach(['trial','active','past_due','cancelled','expired'] as $s)
                                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ __(ucfirst($s)) }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Plan')" name="plan">
                            <select id="plan" name="plan" class="form-select">
                                <option value="">{{ __('All plans') }}</option>
                                @foreach($plans as $p)
                                    <option value="{{ $p->slug }}" @selected(request('plan') === $p->slug)>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-4">
                        <div class="btn-list justify-content-end">
                            @if(request()->anyFilled(['status', 'plan']))
                                <a href="{{ route('admin.subscriptions.index') }}" class="btn">{{ __('Reset') }}</a>
                            @endif
                            <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Workspace') }}</th>
                        <th>{{ __('Plan') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Period') }}</th>
                        <th>{{ __('Currency') }}</th>
                        <th>{{ __('Next billing') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $sub)
                        <tr>
                            <td>
                                <a href="{{ route('admin.workspaces.show', $sub->workspace) }}" class="fw-semibold">{{ $sub->workspace?->name }}</a>
                            </td>
                            <td class="text-muted">{{ $sub->plan?->name }}</td>
                            <td><x-tabler.status-badge :status="$sub->status" /></td>
                            <td class="text-muted text-capitalize">{{ $sub->billing_period }}</td>
                            <td class="text-muted">{{ $sub->currency }}</td>
                            <td class="text-muted">{{ $sub->current_period_ends_at?->format('d M Y') ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.subscriptions.show', $sub) }}" class="btn btn-sm">{{ __('Manage') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-tabler.empty icon="repeat" :title="__('No subscriptions found')"
                                    :body="__('Try different filter criteria.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subscriptions->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $subscriptions->links() }}</div>
        @endif
    </div>
</x-tabler.layout>
