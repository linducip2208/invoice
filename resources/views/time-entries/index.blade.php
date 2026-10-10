<x-tabler.layout :title="__('Time Tracking')" :pretitle="__('Workspace')">
    <div class="row row-decks row-cards mb-3">
        <x-tabler.stat :label="__('Total Hours')" :value="number_format($totalHours, 2) . 'h'" icon="clock" />
        <x-tabler.stat :label="__('Billable Amount')" :value="number_format($totalAmount, 2)" icon="coins" />
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title"><x-ti name="plus" /> {{ __('Tambah Time Entry') }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('time-entries.store') }}">
                @csrf
                <div class="row g-2">
                    <div class="col-md-2">
                        <x-tabler.field name="entry_date" type="date" :value="now()->toDateString()" attrs="required" />
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field name="client_id">
                            <select id="client_id" name="client_id" class="form-select">
                                <option value="">— {{ __('Klien (opsional)') }} —</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <x-tabler.field name="description" type="text" attrs='required placeholder="Apa yang dikerjakan"' />
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field name="hours" type="number" attrs='step="0.25" min="0.01" required placeholder="Jam (mis: 2.5)"' />
                    </div>
                    <div class="col-md-3">
                        <x-tabler.field name="hourly_rate" type="number" attrs='step="0.01" min="0" required placeholder="Rate / jam"' />
                    </div>
                </div>
                <div class="row g-2 align-items-center">
                    <div class="col-md-8">
                        <label class="form-check">
                            <input type="checkbox" name="billable" value="1" checked class="form-check-input">
                            <span class="form-check-label">{{ __('Billable (bisa di-invoice)') }}</span>
                        </label>
                    </div>
                    <div class="col-md-4">
                        <div class="btn-list justify-content-end">
                            <button class="btn btn-primary">{{ __('Catat') }}</button>
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
                        <th>{{ __('Tanggal') }}</th>
                        <th>{{ __('Klien') }}</th>
                        <th>{{ __('Deskripsi') }}</th>
                        <th class="text-end">{{ __('Jam') }}</th>
                        <th class="text-end">{{ __('Rate') }}</th>
                        <th class="text-end">{{ __('Total') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $e)
                        <tr>
                            <td class="text-muted">{{ $e->entry_date?->format('d M Y') }}</td>
                            <td class="text-muted">{{ $e->client?->name ?? '—' }}</td>
                            <td>{{ $e->description }}</td>
                            <td class="text-end">{{ number_format($e->hours, 2) }}</td>
                            <td class="text-end">{{ number_format($e->hourly_rate, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($e->amount, 2) }}</td>
                            <td class="text-end">
                                <x-tabler.delete-button :action="route('time-entries.destroy', $e)" :confirm="__('Hapus?')" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-tabler.empty icon="clock" :title="__('Belum ada entry.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($entries->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $entries->links() }}</div>
        @endif
    </div>
</x-tabler.layout>
