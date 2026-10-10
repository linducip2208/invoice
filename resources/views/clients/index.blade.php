<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Clients') }} <span class="text-2xl">🦄</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Your sparkly customer family') }}</p>
            </div>
            <a href="{{ route('clients.create') }}" class="btn-unicorn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                {{ __('Add Client') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="card-unicorn mb-5">
            <form method="GET" action="{{ route('clients.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <div class="sm:col-span-2">
                    <x-input-label for="search" :value="__('Search')" />
                    <x-text-input id="search" name="search" type="text" :value="request('search')" placeholder="{{ __('Name, company, email, phone…') }}" />
                </div>
                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="input-unicorn rounded-xl px-4 py-2 w-full">
                        <option value="">{{ __('All') }}</option>
                        <option value="active"   @selected(request('status')==='active')>{{ __('Active') }}</option>
                        <option value="inactive" @selected(request('status')==='inactive')>{{ __('Inactive') }}</option>
                    </select>
                </div>
                <div class="flex gap-2 justify-end">
                    @if(request()->anyFilled(['search','status']))
                        <a href="{{ route('clients.index') }}" class="btn-unicorn-secondary">{{ __('Reset') }}</a>
                    @endif
                    <button type="submit" class="btn-unicorn">✦ {{ __('Filter') }}</button>
                </div>
            </form>
        </div>

        <div class="card-unicorn p-0 overflow-hidden">
            <div class="rainbow-bar"></div>
            <div class="overflow-x-auto">
                <table class="table-unicorn">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Company') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th class="text-center">{{ __('Invoices') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clients as $client)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-unicorn-pink shrink-0" style="background: linear-gradient(135deg, #FF7AC6, #A78BFA);">
                                            {{ strtoupper(substr($client->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-purple-900">{{ $client->name }}</div>
                                            @if(!$client->is_active)
                                                <span class="badge-red text-[10px]">{{ __('Inactive') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-purple-700">{{ $client->company_name ?? '—' }}</td>
                                <td class="text-purple-500">{{ $client->email ?? '—' }}</td>
                                <td class="text-purple-500">{{ $client->phone ?? '—' }}</td>
                                <td class="text-center"><span class="badge-purple">{{ $client->invoices_count }}</span></td>
                                <td class="text-right space-x-3 whitespace-nowrap">
                                    <a href="{{ route('clients.show', $client) }}" class="text-purple-600 hover:text-pink-500 font-semibold text-sm">{{ __('View') }}</a>
                                    <a href="{{ route('clients.edit', $client) }}" class="text-amber-600 hover:text-amber-700 font-semibold text-sm">{{ __('Edit') }}</a>
                                    <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Delete this client?') }}')">
                                        @csrf @method('DELETE')
                                        <button class="text-rose-500 hover:text-rose-700 font-semibold text-sm">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center sparkles-bg">
                                    <span class="text-6xl block animate-unicorn-float">🦄</span>
                                    <p class="mt-3 text-purple-500 font-semibold">{{ __('No clients yet!') }}</p>
                                    <p class="text-sm text-purple-400">{{ __('Invite your first client to your magical realm.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $clients->links() }}</div>
    </div>
</x-app-layout>
