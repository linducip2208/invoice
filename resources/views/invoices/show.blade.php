<x-tabler.layout :title="__('Invoice') . ' ' . $invoice->invoice_no" :pretitle="__('Sales')">
    <x-slot name="actions">
        @if($invoice->status === 'draft')
            <a href="{{ route('invoices.edit', $invoice) }}" class="btn">
                <x-ti name="pencil" /> {{ __('Edit') }}
            </a>
        @endif
        @if(in_array($invoice->status, ['draft', 'sent', 'partial', 'overdue']))
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#send-invoice-modal">
                <x-ti name="send" /> {{ __('Send') }}
            </button>
        @endif
        <a href="{{ route('invoices.pdf', $invoice) }}" class="btn">
            <x-ti name="download" /> {{ __('PDF') }}
        </a>
        <form method="POST" action="{{ route('invoices.rotate-link', $invoice) }}" class="d-inline" onsubmit="return confirm({{ Js::from(__('Rotate the public link? The old link stops working.')) }});">
            @csrf
            <button class="btn" title="{{ __('Rotate public link') }}"><x-ti name="refresh" /></button>
        </form>
        <form method="POST" action="{{ route('invoices.duplicate', $invoice) }}" class="d-inline">
            @csrf
            <button class="btn"><x-ti name="copy" /> {{ __('Duplicate') }}</button>
        </form>
        @if(in_array($invoice->status, ['draft', 'cancelled']))
            <form method="POST" action="{{ route('invoices.update-status', $invoice) }}" class="d-inline">
                @csrf
                <input type="hidden" name="status" value="sent">
                <button class="btn btn-outline-success">{{ __('Mark Sent') }}</button>
            </form>
        @endif
        @if(!in_array($invoice->status, ['paid', 'cancelled']))
            <form method="POST" action="{{ route('invoices.update-status', $invoice) }}" class="d-inline" onsubmit="return confirm('{{ __('Cancel this invoice?') }}')">
                @csrf
                <input type="hidden" name="status" value="cancelled">
                <button class="btn btn-outline-secondary">{{ __('Cancel') }}</button>
            </form>
        @endif
        @if($invoice->status === 'draft')
            <x-tabler.delete-button :action="route('invoices.destroy', $invoice)"
                :confirm="__('Delete this invoice?')" :label="__('Delete')" :title="__('Delete')" />
        @endif
    </x-slot>

    @php $ws = workspace(); @endphp

    <div class="mb-3">
        <x-tabler.status-badge :status="$invoice->status" />
    </div>

    <!-- Invoice header card -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    @if($ws?->logo_path)
                        <img src="{{ Storage::disk('public')->url($ws->logo_path) }}" alt="Logo" class="mb-3" style="height: 3rem;">
                    @endif
                    <h3 class="card-title mb-1">{{ $ws?->name ?? config('app.name') }}</h3>
                    @if($ws?->address) <p class="text-muted mb-0">{{ $ws->address }}</p> @endif
                    @if($ws?->email)   <p class="text-muted mb-0">{{ $ws->email }}</p> @endif
                    @if($ws?->phone)   <p class="text-muted mb-0">{{ $ws->phone }}</p> @endif
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="subheader mb-1">{{ __('Invoice #') }}</p>
                    <p class="h2 mb-3">{{ $invoice->invoice_no }}</p>
                    <div class="text-muted">
                        <p class="mb-1"><span class="fw-semibold">{{ __('Issue:') }}</span> {{ $invoice->issue_date?->format('M d, Y') }}</p>
                        <p class="mb-1"><span class="fw-semibold">{{ __('Due:') }}</span> {{ $invoice->due_date?->format('M d, Y') }}</p>
                        @if($invoice->invoice_layout_id)
                            <p class="mb-1"><span class="fw-semibold">{{ __('Layout:') }}</span> {{ $invoice->layout?->name ?? 'Custom' }} <span class="badge bg-pink ms-1">CUSTOM</span></p>
                        @elseif($invoice->template)
                            <p class="mb-1"><span class="fw-semibold">{{ __('Template:') }}</span> {{ ucfirst($invoice->template) }}</p>
                        @endif
                        <p class="mb-0"><span class="fw-semibold">{{ __('Currency:') }}</span> {{ $invoice->currency }}</p>
                    </div>
                </div>
            </div>

            <hr>
            <div class="row g-4">
                <div class="col-md-6">
                    <h4 class="subheader mb-2">{{ __('Bill To') }}</h4>
                    <p class="fw-bold mb-0">{{ $invoice->client?->name ?? '—' }}</p>
                    @if($invoice->client?->company_name) <p class="text-muted mb-0">{{ $invoice->client->company_name }}</p> @endif
                    @if($invoice->client?->address)     <p class="text-muted mb-0">{{ $invoice->client->address }}</p> @endif
                    @if($invoice->client && ($invoice->client->city || $invoice->client->state || $invoice->client->postal_code))
                        <p class="text-muted mb-0">{{ collect([$invoice->client->city, $invoice->client->state, $invoice->client->postal_code])->filter()->join(', ') }}</p>
                    @endif
                    @if($invoice->client?->country) <p class="text-muted mb-0">{{ $invoice->client->country }}</p> @endif
                    @if($invoice->client?->email)   <p class="text-muted mb-0">{{ $invoice->client->email }}</p> @endif
                    @if($invoice->client?->tax_id)  <p class="text-muted mb-0">{{ __('Tax ID:') }} {{ $invoice->client->tax_id }}</p> @endif
                </div>
                <div class="col-md-6 text-md-end">
                    @if($invoice->primary_color || $invoice->accent_color)
                        <div class="d-flex gap-2 justify-content-md-end mb-2">
                            @if($invoice->primary_color)
                                <span class="d-inline-flex align-items-center gap-1 text-muted small">
                                    <span class="avatar avatar-xs rounded border" style="background-color: {{ $invoice->primary_color }}"></span>
                                    {{ __('Primary') }}
                                </span>
                            @endif
                            @if($invoice->accent_color)
                                <span class="d-inline-flex align-items-center gap-1 text-muted small">
                                    <span class="avatar avatar-xs rounded border" style="background-color: {{ $invoice->accent_color }}"></span>
                                    {{ __('Accent') }}
                                </span>
                            @endif
                        </div>
                    @endif
                    <a href="{{ $invoice->public_url }}" target="_blank" class="fw-semibold">
                        <x-ti name="link" /> {{ __('Open public link') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Items -->
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Items') }}</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th class="w-1">#</th>
                        <th>{{ __('Description') }}</th>
                        <th class="text-end">{{ __('Qty') }}</th>
                        <th class="text-end">{{ __('Unit') }}</th>
                        <th class="text-end">{{ __('Tax %') }}</th>
                        <th class="text-end">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->items as $index => $item)
                        <tr>
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td class="fw-medium">
                                {{ $item->description }}
                                @if($item->product) <span class="d-block small text-muted">{{ $item->product->name }}</span> @endif
                            </td>
                            <td class="text-end">{{ number_format((float) $item->quantity, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="text-end text-muted">{{ number_format((float) $item->tax_rate, 2) }}%</td>
                            <td class="text-end fw-bold">{{ number_format((float) $item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-body">
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">{{ __('Subtotal') }}</span><span class="fw-semibold">{{ number_format((float) $invoice->subtotal, 2) }}</span></div>
                    @if((float) $invoice->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">{{ __('Discount') }} @if($invoice->discount_type === 'percent')({{ number_format((float) $invoice->discount_value, 2) }}%)@endif</span>
                            <span class="text-danger fw-semibold">-{{ number_format((float) $invoice->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    @if((float) $invoice->tax_amount > 0)
                        <div class="d-flex justify-content-between mb-1"><span class="text-muted">{{ __('Tax') }} ({{ number_format((float) $invoice->tax_rate, 2) }}%)</span><span class="fw-semibold">{{ number_format((float) $invoice->tax_amount, 2) }}</span></div>
                    @endif
                    <div class="d-flex justify-content-between h3 border-top pt-2 mt-2">
                        <span>{{ __('Total') }}</span>
                        <span>{{ number_format((float) $invoice->total, 2) }} {{ $invoice->currency }}</span>
                    </div>
                    @if((float) $invoice->amount_paid > 0)
                        <div class="d-flex justify-content-between mb-1"><span class="text-success">{{ __('Paid') }}</span><span class="text-success fw-semibold">{{ number_format((float) $invoice->amount_paid, 2) }}</span></div>
                    @endif
                    <div class="d-flex justify-content-between fw-semibold border-top pt-2">
                        <span>{{ __('Balance Due') }}</span>
                        <span class="{{ $invoice->balance_due > 0 ? 'text-danger' : 'text-success' }}">{{ number_format((float) $invoice->balance_due, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes & Terms -->
    @if($invoice->notes || $invoice->terms)
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-4">
                    @if($invoice->notes)
                        <div class="col-md-6">
                            <h4 class="subheader mb-2">{{ __('Notes') }}</h4>
                            <p class="text-muted bg-light rounded p-3 border" style="white-space: pre-line;">{{ $invoice->notes }}</p>
                        </div>
                    @endif
                    @if($invoice->terms)
                        <div class="col-md-6">
                            <h4 class="subheader mb-2">{{ __('Terms') }}</h4>
                            <p class="text-muted bg-light rounded p-3 border" style="white-space: pre-line;">{{ $invoice->terms }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Payments -->
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Payments') }}</h3>
            <div class="card-actions">
                @if((float) $invoice->balance_due > 0 && !in_array($invoice->status, ['draft', 'cancelled']))
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add-payment-modal">
                        <x-ti name="plus" /> {{ __('Record Payment') }}
                    </button>
                @endif
            </div>
        </div>
        @include('payments._list', ['invoice' => $invoice])
    </div>

    <!-- View Tracking + Attachments -->
    @php
        $views = $invoice->publicViews ?? collect();
        $attachments = $invoice->attachments ?? collect();
    @endphp

    <div class="row g-3 mb-3">
        <!-- View tracking -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title mb-3">
                        <x-ti name="eye" /> {{ __('Client Views') }}
                    </h3>
                    @if($views->isNotEmpty())
                        <p class="h1">{{ $views->count() }}<span class="small fw-normal text-muted ms-2">{{ __('views') }}</span></p>
                        <p class="small text-muted mt-1">
                            {{ __('First viewed') }}: {{ $views->min('created_at')?->diffForHumans() }} ·
                            {{ __('Last') }}: {{ $views->max('created_at')?->diffForHumans() }}
                        </p>
                        <div class="mt-3 small text-muted">
                            @foreach($views->sortByDesc('created_at')->take(5) as $v)
                                <div class="d-flex justify-content-between"><span class="font-monospace">{{ $v->ip_address ?? '—' }}</span><span class="text-muted">{{ $v->created_at->diffForHumans() }}</span></div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">{{ __('Belum dilihat klien.') }}</p>
                        <p class="small text-muted mt-2">{{ __('Akan tercatat otomatis saat klien buka link publik.') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Attachments -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="card-title mb-3">
                        <x-ti name="file-text" /> {{ __('Attachments') }}
                    </h3>
                    <form method="POST" action="{{ route('invoices.attachments.store', $invoice) }}" enctype="multipart/form-data" class="mb-3">
                        @csrf
                        <div class="input-group">
                            <input type="file" name="file" required class="form-control form-control-sm">
                            <button class="btn btn-sm">+ {{ __('Upload') }}</button>
                        </div>
                    </form>
                    @if($attachments->isNotEmpty())
                        <div class="list-group list-group-flush">
                            @foreach($attachments as $att)
                                <div class="list-group-item px-0">
                                    <div class="row align-items-center g-2">
                                        <div class="col text-truncate">
                                            <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="text-truncate"><x-ti name="file-text" /> {{ $att->file_name }}</a>
                                        </div>
                                        <div class="col-auto text-muted small">{{ number_format($att->file_size / 1024, 0) }}KB</div>
                                        <div class="col-auto">
                                            <form method="POST" action="{{ route('invoices.attachments.destroy', $att) }}" class="d-inline" onsubmit="return confirm('Hapus?');">@csrf @method('DELETE')<button class="btn btn-sm btn-link text-danger p-0" title="{{ __('Delete') }}"><x-ti name="x" /></button></form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="small text-muted">{{ __('Belum ada lampiran.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Email Log -->
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Email Log') }}</h3>
        </div>
        @if($invoice->emailLogs->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('Sent At') }}</th>
                            <th>{{ __('To') }}</th>
                            <th>{{ __('Subject') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->emailLogs as $log)
                            <tr>
                                <td class="text-nowrap">{{ $log->sent_at?->format('M d, Y H:i') }}</td>
                                <td>{{ $log->to_email }}</td>
                                <td class="text-muted">{{ $log->subject }}</td>
                                <td class="text-center">
                                    <x-tabler.status-badge :status="$log->status" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="card-body">
                <p class="text-muted mb-0">{{ __('No emails sent yet.') }}</p>
            </div>
        @endif
    </div>

    <!-- Public Link -->
    <div class="card mb-3">
        <div class="card-body">
            <h3 class="card-title mb-3">{{ __('Public Link') }}</h3>
            <div class="input-group">
                <input type="text" readonly value="{{ $invoice->public_url }}" id="public-url-input" class="form-control form-control-sm bg-light">
                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('public-url-input').value).then(() => { const b = event.target; b.textContent = 'Copied!'; setTimeout(() => b.textContent = '{{ __('Copy') }}', 1500); })" class="btn btn-sm btn-primary">
                    {{ __('Copy') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Activity timeline -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><x-ti name="history" /> {{ __('Activity') }}</h3>
        </div>
        <div class="card-body">
            @if($activity->isNotEmpty())
                <ul class="steps steps-vertical">
                    @foreach($activity as $event)
                        <li class="step-item">
                            <div class="h4 m-0">{{ $event->description }}</div>
                            <div class="text-muted">{{ $event->created_at?->format('d M Y, H:i') }}@if($event->user)· {{ $event->user->name }}@endif</div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted mb-0">{{ __('No activity yet.') }}</p>
            @endif
        </div>
    </div>

    @include('invoices._send-modal', ['invoice' => $invoice])
    @include('payments._form', ['invoice' => $invoice])
</x-tabler.layout>
