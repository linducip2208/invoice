<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white text-xl font-bold shadow-unicorn-pink" style="background: linear-gradient(135deg, #FF7AC6, #A78BFA);">
                    {{ strtoupper(substr($client->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="heading-unicorn">{{ $client->name }}</h2>
                    <p class="text-sm text-purple-500 mt-1">
                        @if($client->company_name) ✦ {{ $client->company_name }} @endif
                        @if($client->is_active) <span class="badge-green ml-2">{{ __('Active') }}</span> @else <span class="badge-red ml-2">{{ __('Inactive') }}</span> @endif
                    </p>
                </div>
            </div>
            <div class="flex gap-2 items-center">
                <a href="{{ route('clients.index') }}" class="text-sm font-semibold text-purple-500 hover:text-pink-500">← {{ __('Back') }}</a>
                <a href="{{ route('clients.edit', $client) }}" class="btn-unicorn-secondary">✏️ {{ __('Edit') }}</a>
                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Delete this client?') }}')">
                    @csrf @method('DELETE')
                    <x-danger-button>🗑️ {{ __('Delete') }}</x-danger-button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-4 flex items-center gap-2">
                <span class="text-pink-400">✦</span> {{ __('Client Details') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                @php $field = fn($l, $v) => "<div><p class=\"text-xs font-semibold text-purple-400 uppercase tracking-wider\">$l</p><p class=\"mt-1 text-sm text-purple-900 font-medium\">$v</p></div>"; @endphp
                {!! $field(__('Name'), e($client->name)) !!}
                {!! $field(__('Company'), e($client->company_name ?? '—')) !!}
                {!! $field(__('Email'), e($client->email ?? '—')) !!}
                {!! $field(__('Phone'), e($client->phone ?? '—')) !!}
                <div class="md:col-span-2">
                    <p class="text-xs font-semibold text-purple-400 uppercase tracking-wider">{{ __('Address') }}</p>
                    <p class="mt-1 text-sm text-purple-900 font-medium">
                        {{ $client->address ?? '—' }}
                        @if($client->city || $client->state || $client->postal_code)
                            <br>{{ collect([$client->city, $client->state, $client->postal_code])->filter()->join(', ') }}
                        @endif
                        @if($client->country) <br>{{ $client->country }} @endif
                    </p>
                </div>
                {!! $field(__('Tax ID'), e($client->tax_id ?? '—')) !!}
                {!! $field(__('Total Invoices'), $client->invoices_count) !!}
                @if($client->notes)
                    <div class="md:col-span-2">
                        <p class="text-xs font-semibold text-purple-400 uppercase tracking-wider">{{ __('Notes') }}</p>
                        <p class="mt-1 text-sm text-purple-900 leading-relaxed bg-purple-50/40 rounded-xl p-3 border border-purple-100">{{ $client->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card-unicorn p-0 overflow-hidden">
            <div class="rainbow-bar"></div>
            <div class="px-6 py-5 flex items-center justify-between">
                <h3 class="font-display font-bold text-lg text-purple-900 flex items-center gap-2">
                    <span class="text-pink-400">✦</span> {{ __('Invoices') }}
                </h3>
                <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="btn-unicorn">✦ {{ __('New Invoice') }}</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table-unicorn">
                    <thead>
                        <tr>
                            <th>{{ __('Invoice No') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Issue') }}</th>
                            <th>{{ __('Due') }}</th>
                            <th class="text-right">{{ __('Total') }}</th>
                            <th class="text-right">{{ __('Balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td><a href="{{ route('invoices.show', $invoice) }}" class="font-bold text-purple-700 hover:text-pink-500">{{ $invoice->invoice_no }}</a></td>
                                <td><span class="badge-{{ $invoice->status_color }}">{{ ucfirst($invoice->status) }}</span></td>
                                <td class="text-purple-500">{{ $invoice->issue_date?->format('M d, Y') ?? '—' }}</td>
                                <td class="text-purple-500">{{ $invoice->due_date?->format('M d, Y') ?? '—' }}</td>
                                <td class="text-right font-bold text-purple-900">{{ number_format($invoice->total, 2) }}</td>
                                <td class="text-right font-bold text-{{ $invoice->balance_due > 0 ? 'pink-600' : 'green-600' }}">{{ number_format($invoice->balance_due, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center sparkles-bg">
                                    <span class="text-5xl block animate-unicorn-float">📜</span>
                                    <p class="mt-3 text-purple-500 font-semibold">{{ __('No invoices yet for this sparkle.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4">{{ $invoices->links() }}</div>
        </div>
    </div>
</x-app-layout>
