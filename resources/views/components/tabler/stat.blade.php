@props(['label' => '', 'value' => '', 'icon' => 'chart-bar', 'delta' => null, 'deltaUp' => null])
<div class="col-sm-6 col-lg-3">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="subheader">{{ $label }}</div>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <div class="h1 mb-0 me-2">{{ $value }}</div>
                @if($delta !== null)
                    <span class="d-inline-flex align-items-center lh-1 {{ $deltaUp ? 'stat-delta-up' : 'stat-delta-down' }}">
                        {{ $delta }}
                    </span>
                @endif
                <span class="ms-auto text-muted"><x-ti :name="$icon" class="icon icon-lg" /></span>
            </div>
        </div>
    </div>
</div>
