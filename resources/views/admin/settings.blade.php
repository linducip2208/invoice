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
</div>
</x-tabler.admin-layout>
