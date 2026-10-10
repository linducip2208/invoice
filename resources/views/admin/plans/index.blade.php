<x-tabler.admin-layout :title="__('Plans')" :pretitle="__('Superadmin')" :subtitle="__(':count plans', ['count' => $plans->count()])">
    <x-slot name="actions">
        <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">
            <x-ti name="plus" /> {{ __('New Plan') }}
        </a>
    </x-slot>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Slug') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('IDR/mo') }}</th>
                        <th>{{ __('USD/mo') }}</th>
                        <th class="text-center">{{ __('Public') }}</th>
                        <th class="text-center">{{ __('Subs') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $p)
                        <tr>
                            <td><code>{{ $p->slug }}</code></td>
                            <td class="fw-semibold">{{ $p->name }}</td>
                            <td class="text-muted">Rp{{ number_format($p->price_idr_monthly, 0, ',', '.') }}</td>
                            <td class="text-muted">${{ number_format($p->price_usd_monthly, 2) }}</td>
                            <td class="text-center">
                                @if($p->is_public)
                                    <span class="badge bg-green-lt">{{ __('Yes') }}</span>
                                @else
                                    <span class="badge bg-secondary-lt">{{ __('No') }}</span>
                                @endif
                            </td>
                            <td class="text-center"><span class="badge bg-blue-lt">{{ $p->subscriptions()->count() }}</span></td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.plans.edit', $p) }}" class="btn btn-sm btn-outline-warning">{{ __('Edit') }}</a>
                                @if($p->slug !== 'free')
                                    <x-tabler.delete-button :action="route('admin.plans.destroy', $p)"
                                        :confirm="__('Delete this plan?')" />
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-tabler.empty icon="wallet" :title="__('No plans yet')"
                                    :body="__('Create your first plan to get started.')"
                                    :actionUrl="route('admin.plans.create')" :actionLabel="__('New Plan')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-tabler.admin-layout>
