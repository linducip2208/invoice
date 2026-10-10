<x-tabler.layout :title="__('AI Providers')" :pretitle="__('Automation')" :subtitle="__('Bring Your Own Key — pakai LLM apapun')">
    <x-slot name="actions">
        <a href="{{ route('ai-providers.create') }}" class="btn btn-primary">
            <x-ti name="plus" /> {{ __('Tambah Provider') }}
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-header"><h3 class="card-title">{{ __('Pemakaian Bulan Ini') }}</h3></div>
        <div class="card-body">
            <div class="datagrid">
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Total Cost (estimasi)') }}</div>
                    <div class="datagrid-content h3 mb-0">${{ number_format($totalCost, 4) }}</div>
                </div>
                @foreach($usageThisMonth as $u)
                    <div class="datagrid-item">
                        <div class="datagrid-title">{{ str_replace('_',' ', ucfirst($u->feature)) }}</div>
                        <div class="datagrid-content">{{ number_format($u->calls) }} call · {{ number_format($u->tokens) }} token</div>
                        <div class="text-muted small">${{ number_format($u->cost, 4) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if($providers->isEmpty())
        <div class="card">
            <div class="card-body">
                <x-tabler.empty icon="robot" :title="__('Belum ada AI provider')"
                    :body="__('Tambah provider untuk pakai fitur AI (smart description, OCR receipt, predict, dll).')"
                    :actionUrl="route('ai-providers.create')" :actionLabel="__('Tambah Provider Pertama')" />
            </div>
        </div>
    @else
        <div class="row row-deck row-cards">
            @foreach($providers as $p)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h3 class="card-title mb-0 text-truncate">{{ $p->name }}</h3>
                                @if($p->is_active)
                                    <span class="badge bg-green-lt">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-secondary-lt">{{ __('Disabled') }}</span>
                                @endif
                            </div>
                            <div class="text-muted small font-monospace">{{ str_replace('_',' ', $p->api_format) }} · {{ parse_url($p->base_url, PHP_URL_HOST) }}</div>
                            @if($p->default_model)
                                <div class="small text-muted mt-1">{{ __('Default:') }} <code>{{ $p->default_model }}</code></div>
                            @endif
                            <div class="d-flex gap-2 mt-3 pt-3 border-top">
                                <a href="{{ route('ai-providers.edit', $p) }}" class="btn btn-sm flex-fill">{{ __('Edit') }}</a>
                                <form method="POST" action="{{ route('ai-providers.test', $p) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary"><x-ti name="plug" /> {{ __('Test') }}</button>
                                </form>
                                <form method="POST" action="{{ route('ai-providers.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Hapus provider {{ $p->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Hapus') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-tabler.layout>
