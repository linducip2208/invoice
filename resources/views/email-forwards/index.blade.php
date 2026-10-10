<x-tabler.layout :title="__('Email Inbox Forward')" :pretitle="__('Automation')">
    <div class="card mb-3">
        <div class="card-header"><h3 class="card-title">{{ __('Forward Address Kamu') }}</h3></div>
        <div class="card-body">
            <p class="text-muted">{{ __('Forward email vendor / supplier ke alamat ini → AI auto-extract → buat expense draft.') }}</p>
            <div class="input-group">
                <code class="form-control font-monospace user-select-all">{{ $inboundAddress }}</code>
                <button type="button" onclick="navigator.clipboard.writeText('{{ $inboundAddress }}'); this.textContent='{{ __('Copied') }}'" class="btn btn-primary">
                    <x-ti name="copy" /> {{ __('Copy') }}
                </button>
            </div>
            <p class="form-hint mt-2">{{ __('Setup inbound email di Postmark/Mailgun/SES dengan webhook URL:') }} <code>POST /webhooks/email-inbox</code></p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">{{ __('Riwayat Forward') }} ({{ $forwards->count() }})</h3></div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('From') }}</th>
                        <th>{{ __('Subject') }}</th>
                        <th>{{ __('Tgl') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Expense') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($forwards as $f)
                        <tr>
                            <td class="text-muted">{{ $f->from_email }}</td>
                            <td class="text-truncate" style="max-width: 16rem;">{{ $f->subject }}</td>
                            <td class="text-muted small">{{ $f->forwarded_at->diffForHumans() }}</td>
                            <td class="text-center">
                                @php $color = match($f->status){'converted'=>'green','manual_review'=>'yellow','failed'=>'red','duplicate'=>'secondary',default=>'blue'}; @endphp
                                <span class="badge bg-{{ $color }}-lt">{{ ucfirst(str_replace('_',' ', $f->status)) }}</span>
                            </td>
                            <td class="text-end">
                                @if($f->expense_id)
                                    <a href="{{ route('expenses.index') }}" class="btn btn-sm">{{ __('Lihat') }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-tabler.empty icon="mail" :title="__('Belum ada email yang diforward.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($forwards->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $forwards->links() }}</div>
        @endif
    </div>
</x-tabler.layout>
