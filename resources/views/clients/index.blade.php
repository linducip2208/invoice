<x-tabler.layout :title="__('Clients')" :pretitle="__('Sales')">
    <x-slot name="actions">
        <a href="{{ route('clients.create') }}" class="btn btn-primary">
            <x-ti name="plus" /> {{ __('Add Client') }}
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('clients.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Search')" name="search">
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                placeholder="{{ __('Name, company, email, phone…') }}" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <x-tabler.field :label="__('Status')" name="status">
                            <select id="status" name="status" class="form-select">
                                <option value="">{{ __('All') }}</option>
                                <option value="active" @selected(request('status') === 'active')>{{ __('Active') }}</option>
                                <option value="inactive" @selected(request('status') === 'inactive')>{{ __('Inactive') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <div class="btn-list justify-content-end">
                            @if(request()->anyFilled(['search', 'status']))
                                <a href="{{ route('clients.index') }}" class="btn">{{ __('Reset') }}</a>
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
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Company') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th class="text-center">{{ __('Invoices') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $client)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar avatar-sm">{{ strtoupper(mb_substr($client->name, 0, 1)) }}</span>
                                    <div>
                                        <a href="{{ route('clients.show', $client) }}" class="fw-semibold">{{ $client->name }}</a>
                                        @if(!$client->is_active)
                                            <span class="badge bg-red ms-1">{{ __('Inactive') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $client->company_name ?? '—' }}</td>
                            <td class="text-muted">{{ $client->email ?? '—' }}</td>
                            <td class="text-muted">{{ $client->phone ?? '—' }}</td>
                            <td class="text-center"><span class="badge bg-blue-lt">{{ $client->invoices_count }}</span></td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('clients.show', $client) }}" class="btn btn-sm">{{ __('View') }}</a>
                                <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-warning">{{ __('Edit') }}</a>
                                <x-tabler.delete-button :action="route('clients.destroy', $client)"
                                    :confirm="__('Delete this client?')" :title="__('Delete')" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-tabler.empty icon="users" :title="__('No clients yet!')"
                                    :body="__('Add your first client to get started.')"
                                    :actionUrl="route('clients.create')" :actionLabel="__('Add Client')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($clients->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $clients->links() }}</div>
        @endif
    </div>
</x-tabler.layout>
