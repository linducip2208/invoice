<x-tabler.layout :title="__('Email Report Subscriptions')" :pretitle="__('Reports')">
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title"><x-ti name="plus" /> {{ __('Subscribe ke Report') }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('report-subscriptions.store') }}">
                @csrf
                <div class="row g-2">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Report')" name="report_type">
                            <select id="report_type" name="report_type" required class="form-select">
                                <option value="weekly_summary">{{ __('Weekly Summary') }}</option>
                                <option value="monthly_pl">{{ __('Monthly P&L per Klien') }}</option>
                                <option value="quarterly_tax">{{ __('Quarterly Tax Summary') }}</option>
                                <option value="aging_report">{{ __('Aging Report') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Frekuensi')" name="frequency">
                            <select id="frequency" name="frequency" required class="form-select">
                                <option value="weekly">{{ __('Mingguan') }}</option>
                                <option value="monthly">{{ __('Bulanan') }}</option>
                                <option value="quarterly">{{ __('Per kuartal') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Hari (mingguan)')" name="day_of_week">
                            <select id="day_of_week" name="day_of_week" class="form-select">
                                @foreach(['1' => 'Senin', '2' => 'Selasa', '3' => 'Rabu', '4' => 'Kamis', '5' => 'Jumat', '6' => 'Sabtu', '7' => 'Minggu'] as $d => $name)
                                    <option value="{{ $d }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Tanggal (bulanan)')" name="day_of_month" type="number" :value="1" attrs='min="1" max="28"' />
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Jam')" name="hour" type="number" :value="8" attrs='min="0" max="23"' />
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><x-ti name="plus" /> {{ __('Subscribe') }}</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Active Subscriptions') }} ({{ $subscriptions->count() }})</h3>
        </div>
        @if($subscriptions->isEmpty())
            <div class="card-body">
                <x-tabler.empty icon="send" :title="__('Belum ada subscription.')" />
            </div>
        @else
            <div class="list-group list-group-flush">
                @foreach($subscriptions as $s)
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col">
                                <strong>{{ str_replace('_', ' ', ucfirst($s->report_type)) }}</strong>
                                <div class="text-muted small">{{ ucfirst($s->frequency) }} · {{ __('jam') }} {{ $s->hour }}:00 · {{ __('next') }}: {{ $s->next_send_at?->format('d M Y') }}</div>
                            </div>
                            <div class="col-auto">
                                <div class="btn-list">
                                    <form method="POST" action="{{ route('report-subscriptions.toggle', $s) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm {{ $s->is_active ? 'btn-outline-success' : '' }}">{{ $s->is_active ? __('Active') : __('Paused') }}</button>
                                    </form>
                                    <x-tabler.delete-button :action="route('report-subscriptions.destroy', $s)" :confirm="__('Hapus?')" :label="__('Hapus')" />
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-tabler.layout>
