<x-tabler.layout :title="__('Projects')">
    <x-slot name="actions">
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <x-ti name="plus" /> {{ __('Project Baru') }}
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('projects.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Status')" name="status">
                            <select name="status" id="status" class="form-select">
                                <option value="">{{ __('Semua') }}</option>
                                <option value="active" @selected(request('status') === 'active')>Active</option>
                                <option value="on_hold" @selected(request('status') === 'on_hold')>On Hold</option>
                                <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                                <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-5">
                        <x-tabler.field :label="__('Klien')" name="client_id">
                            <select name="client_id" id="client_id" class="form-select">
                                <option value="">{{ __('Semua klien') }}</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}" @selected(request('client_id') == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <div class="btn-list justify-content-end">
                            @if(request()->anyFilled(['status', 'client_id']))
                                <a href="{{ route('projects.index') }}" class="btn">{{ __('Reset') }}</a>
                            @endif
                            <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($projects->isEmpty())
        <div class="card">
            <div class="card-body">
                <x-tabler.empty icon="briefcase" :title="__('Belum ada project')"
                    :body="__('Buat project pertama untuk mulai grouping invoice & time entry.')"
                    :actionUrl="route('projects.create')" :actionLabel="__('Project Pertama')" />
            </div>
        </div>
    @else
        <div class="row row-cards">
            @foreach($projects as $p)
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-status-top" style="background: {{ $p->color ?? '#A78BFA' }};"></div>
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-2">
                                <h3 class="card-title mb-0 text-truncate">
                                    <a href="{{ route('projects.show', $p) }}">{{ $p->name }}</a>
                                </h3>
                                <span class="badge bg-{{ match($p->status){'active'=>'green','on_hold'=>'yellow','completed'=>'blue','cancelled'=>'red',default=>'secondary'} }} flex-shrink-0">{{ ucfirst(str_replace('_',' ',$p->status)) }}</span>
                            </div>
                            <div class="text-muted small mt-1">
                                <x-ti name="user" /> {{ $p->client?->name }}
                            </div>
                            @if($p->description)
                                <p class="text-muted small mt-2 mb-0 text-truncate">{{ $p->description }}</p>
                            @endif
                            <div class="row g-2 mt-3 pt-3 border-top">
                                <div class="col-6">
                                    <div class="text-muted small text-uppercase">Budget</div>
                                    <div class="fw-semibold">{{ $p->budget_amount ? number_format($p->budget_amount, 0) : '—' }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small text-uppercase">Jam</div>
                                    <div class="fw-semibold">{{ $p->budget_hours ? number_format($p->budget_hours, 0) : '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if($projects->hasPages())
            <div class="card mt-3">
                <div class="card-footer d-flex justify-content-center">{{ $projects->links() }}</div>
            </div>
        @endif
    @endif
</x-tabler.layout>
