<x-tabler.layout :title="__('Reminders')" :pretitle="__('Pengingat otomatis untuk') . ' ' . $invoice->invoice_no">
    <x-slot name="actions">
        <a href="{{ route('invoices.show', $invoice) }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Kembali ke Invoice') }}
        </a>
    </x-slot>

    {{-- Invoice summary --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row align-items-center g-3">
                <div class="col">
                    <p class="subheader mb-1">{{ __('Invoice') }}</p>
                    <p class="h2 mb-1">{{ $invoice->invoice_no }}</p>
                    <p class="text-muted small mb-0">{{ $invoice->client?->name }} · Jatuh tempo {{ $invoice->due_date->format('d M Y') }}</p>
                </div>
                <div class="col-auto text-end">
                    <p class="subheader mb-1">{{ __('Sisa Tagihan') }}</p>
                    <p class="h2 mb-1">{{ $invoice->currency }} {{ number_format($invoice->balance_due ?? $invoice->total, 0) }}</p>
                    <x-tabler.status-badge :status="$invoice->status" />
                </div>
            </div>
        </div>
    </div>

    {{-- Auto-schedule action --}}
    @if(! in_array($invoice->status, ['paid', 'cancelled']))
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center g-3">
                    <div class="col">
                        <h3 class="card-title mb-1">{{ __('Auto-Schedule Reminder') }}</h3>
                        <p class="text-muted small mb-0">
                            {{ __('Schedule otomatis 4 reminder: H-3, hari-H, H+3, dan H+7 setelah jatuh tempo. Reminder yang ada akan diganti.') }}
                        </p>
                    </div>
                    <div class="col-auto">
                        <form method="POST" action="{{ route('invoices.reminders.schedule', $invoice) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary"><x-ti name="sparkles" /> {{ __('Jadwalkan Otomatis') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Manual add --}}
    @if(! in_array($invoice->status, ['paid', 'cancelled']))
        <div class="card mb-3">
            <div class="card-body">
                <h3 class="card-title mb-3">{{ __('Tambah Reminder Manual') }}</h3>
                <form method="POST" action="{{ route('invoices.reminders.store', $invoice) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-tabler.field :label="__('Kapan dikirim')" name="scheduled_at" type="datetime-local" attrs="required" />
                        </div>
                        <div class="col-md-6">
                            <x-tabler.field :label="__('Subjek email')" name="template_subject">
                                <input type="text" id="template_subject" name="template_subject" required
                                    value="{{ old('template_subject') }}" placeholder="Pengingat: Invoice @{{invoice_no}}"
                                    class="form-control @error('template_subject') is-invalid @enderror">
                            </x-tabler.field>
                        </div>
                    </div>
                    <x-tabler.field :label="__('Isi pesan')" name="template_body" :help="__('Variabel:') . ' {{client_name}}, {{invoice_no}}, {{amount_due}}, {{currency}}, {{due_date}}, {{pay_link}}, {{workspace_name}}'">
                        <textarea id="template_body" name="template_body" rows="6" required
                                  class="form-control font-monospace @error('template_body') is-invalid @enderror">Halo @{{client_name}},

Invoice @{{invoice_no}} sebesar @{{currency}} @{{amount_due}} masih belum lunas. Mohon segera diselesaikan.

Link: @{{pay_link}}</textarea>
                    </x-tabler.field>
                    <button type="submit" class="btn btn-primary"><x-ti name="plus" /> {{ __('Tambah Reminder') }}</button>
                </form>
            </div>
        </div>
    @endif

    {{-- Timeline reminders --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Riwayat Reminder') }}
                <span class="text-muted fw-normal small">({{ $reminders->count() }})</span>
            </h3>
        </div>
        @if($reminders->isEmpty())
            <div class="card-body">
                <x-tabler.empty icon="mail" :title="__('Belum ada reminder')"
                    :body="__('Klik Jadwalkan Otomatis untuk setup 4 reminder default.')" />
            </div>
        @else
            <div class="list-group list-group-flush">
                @foreach($reminders as $r)
                    @php
                        $statusBadge = match($r->status) {
                            'sent'      => ['success', __('Terkirim')],
                            'pending'   => ['warning', __('Menunggu')],
                            'failed'    => ['danger', __('Gagal')],
                            'cancelled' => ['secondary', __('Dibatalkan')],
                        };
                        $offsetLabel = $r->schedule_offset < 0
                            ? 'H' . $r->schedule_offset
                            : ($r->schedule_offset === 0 ? 'Hari-H' : 'H+' . $r->schedule_offset);
                    @endphp
                    <div class="list-group-item">
                        <div class="row align-items-start g-3">
                            <div class="col-auto">
                                <span class="avatar">{{ $offsetLabel }}</span>
                            </div>
                            <div class="col">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <p class="fw-bold mb-0 text-truncate">{{ $r->template_subject }}</p>
                                    <span class="badge bg-{{ $statusBadge[0] }}">{{ $statusBadge[1] }}</span>
                                </div>
                                <p class="small text-muted mt-1 mb-0">
                                    {{ __('Dijadwalkan') }}: {{ $r->scheduled_at->format('d M Y, H:i') }}
                                    @if($r->sent_at) · {{ __('Dikirim') }}: {{ $r->sent_at->format('d M Y, H:i') }} @endif
                                </p>
                                @if($r->fail_reason)
                                    <p class="small text-danger mt-1 mb-0">{{ $r->fail_reason }}</p>
                                @endif
                            </div>
                            @if($r->status === 'pending')
                                <div class="col-auto">
                                    <form method="POST" action="{{ route('invoices.reminders.cancel', $r) }}" class="d-inline" onsubmit="return confirm('Batalkan reminder ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Batalkan') }}</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-tabler.layout>
