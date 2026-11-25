<x-tabler.admin-layout :title="__('Settings')">
<x-slot name="actions"><a href="{{ route('admin.branding.edit') }}" class="btn btn-primary">{{ __('Branding') }}</a></x-slot>
<div class="row row-cards">
<div class="col-lg-6"><div class="card"><div class="card-header"><h3 class="card-title">{{ __('Environment') }}</h3></div>
<div class="card-body"><dl class="row g-2 mb-0">@foreach($checks as $k=>$v)<dt class="col-5 text-muted small">{{ $k }}</dt><dd class="col-7 small text-break">{{ $v }}</dd>@endforeach</dl></div></div></div>
<div class="col-lg-6"><div class="card"><div class="card-header"><h3 class="card-title">{{ __('Shortcuts') }}</h3></div>
<div class="card-body"><div class="btn-list flex-wrap">
<a href="{{ route('admin.branding.edit') }}" class="btn">{{ __('Branding') }}</a>
<a href="{{ route('admin.plans.index') }}" class="btn">{{ __('Plans') }}</a>
<a href="{{ route('admin.coupons.index') }}" class="btn">{{ __('Coupons') }}</a>
<a href="{{ route('admin.audit-logs') }}" class="btn">{{ __('Audit Logs') }}</a>
<a href="{{ route('admin.analytics') }}" class="btn">{{ __('Analytics') }}</a>
<a href="{{ route('admin.revenue') }}" class="btn">{{ __('Revenue Reports') }}</a>
</div><p class="text-muted small mt-2 mb-0">{{ __('Pengaturan aplikasi mengikuti .env + branding DB. Tidak ada setting tersembunyi.') }}</p></div></div></div>
<div class="col-lg-6"><div class="card"><div class="card-header"><h3 class="card-title">{{ __('Kurs USD → IDR') }}</h3>
<div class="card-actions"><form method="POST" action="{{ route('admin.settings.fx-refresh') }}">@csrf<button class="btn btn-sm">{{ __('Refresh kurs') }}</button></form></div></div>
<div class="card-body">
<p class="mb-2">
@if($fx['live_rate'])
<span class="h3 mb-0">Rp{{ number_format($fx['live_rate'], 0, ',', '.') }}</span>
<span class="text-muted small"> / 1 USD · {{ $fx['source'] === 'manual' ? __('manual (Setting)') : __('live') }}{{ $fx['live_date'] ? ' · ' . $fx['live_date'] : '' }}</span>
@else
<span class="text-danger">{{ __('Kurs belum tersedia — isi kurs manual di bawah.') }}</span>
@endif
</p>
<form method="POST" action="{{ route('admin.settings.fx') }}">
@csrf
<div class="row g-2 align-items-end">
<div class="col-md-5">
<x-tabler.field :label="__('Mode')" name="mode">
<select id="mode" name="mode" class="form-select">
<option value="auto" @selected($fx['mode'] === 'auto')>{{ __('Otomatis (API harian)') }}</option>
<option value="manual" @selected($fx['mode'] === 'manual')>{{ __('Manual') }}</option>
</select>
</x-tabler.field>
</div>
<div class="col-md-4">
<x-tabler.field :label="__('Kurs manual (Rp/USD)')" name="manual_usd_idr" :help="__('Dipakai saat mode manual / API mati.')">
<input type="number" id="manual_usd_idr" name="manual_usd_idr" min="1" step="0.01" value="{{ old('manual_usd_idr', $fx['manual_usd_idr']) }}" class="form-control" placeholder="18229">
</x-tabler.field>
</div>
<div class="col-md-3"><button class="btn btn-primary w-100">{{ __('Simpan') }}</button></div>
</div>
</form>
<p class="text-muted small mt-2 mb-0">{{ __('Dipakai untuk "setara Rp" di MRR & invoice USD. Angka asli USD tidak diubah.') }}</p>
</div></div></div>
</div>
</x-tabler.admin-layout>
