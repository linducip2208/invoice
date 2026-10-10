<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Projects') }} <span class="text-2xl">📁</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Group invoice + time + expense per project') }}</p>
            </div>
            <a href="{{ route('projects.create') }}" class="btn-unicorn">+ {{ __('Project Baru') }}</a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800">✓ {{ session('success') }}</div>
        @endif

        <form method="GET" class="card-unicorn !p-4 flex flex-wrap items-end gap-3">
            <div>
                <x-input-label :value="__('Status')" />
                <select name="status" class="input-unicorn rounded-xl px-4 py-2 mt-1">
                    <option value="">{{ __('Semua') }}</option>
                    <option value="active"    {{ request('status')==='active' ? 'selected' : '' }}>Active</option>
                    <option value="on_hold"   {{ request('status')==='on_hold' ? 'selected' : '' }}>On Hold</option>
                    <option value="completed" {{ request('status')==='completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status')==='cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <x-input-label :value="__('Klien')" />
                <select name="client_id" class="input-unicorn rounded-xl px-4 py-2 mt-1">
                    <option value="">{{ __('Semua klien') }}</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-unicorn">{{ __('Filter') }}</button>
        </form>

        @if($projects->isEmpty())
            <div class="card-unicorn !p-12 text-center">
                <p class="text-5xl mb-3">📁</p>
                <p class="font-display font-bold text-lg text-purple-700">{{ __('Belum ada project') }}</p>
                <p class="text-sm text-purple-400 mt-1 mb-4">Buat project pertama untuk mulai grouping invoice & time entry.</p>
                <a href="{{ route('projects.create') }}" class="btn-unicorn">+ {{ __('Project Pertama') }}</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($projects as $p)
                    <a href="{{ route('projects.show', $p) }}" class="card-unicorn hover:shadow-unicorn-glow transition no-underline group">
                        <div class="h-2 -mx-6 -mt-6 mb-4 rounded-t-2xl"
                             style="background: {{ $p->color ?? 'linear-gradient(90deg, #FF7AC6, #A78BFA, #7DD3FC)' }};"></div>
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-display font-bold text-lg text-purple-900 group-hover:text-pink-500 truncate">{{ $p->name }}</h3>
                            <span class="badge-{{ match($p->status){'active'=>'mint','on_hold'=>'gold','completed'=>'blue','cancelled'=>'gray',default=>'gray'} }} text-[10px] shrink-0">{{ ucfirst(str_replace('_',' ',$p->status)) }}</span>
                        </div>
                        <p class="text-sm text-purple-500 mt-1">👤 {{ $p->client?->name }}</p>
                        @if($p->description)
                            <p class="text-xs text-purple-400 mt-2 line-clamp-2">{{ $p->description }}</p>
                        @endif
                        <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-purple-100">
                            <div>
                                <p class="text-[10px] uppercase tracking-wider text-purple-400">Budget</p>
                                <p class="text-sm font-bold text-purple-900">{{ $p->budget_amount ? number_format($p->budget_amount, 0) : '—' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase tracking-wider text-purple-400">Jam</p>
                                <p class="text-sm font-bold text-purple-900">{{ $p->budget_hours ? number_format($p->budget_hours, 0) : '—' }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div>{{ $projects->links() }}</div>
        @endif
    </div>
</x-app-layout>
