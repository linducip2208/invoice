@props(['label' => '', 'value' => '', 'icon' => 'chart-bar', 'delta' => null, 'deltaUp' => null, 'tone' => 'primary'])
<div class="col-sm-6 col-lg-3">
    <div class="kpi-card">
        <div class="kpi-top">
            <p class="kpi-label">{{ $label }}</p>
            <span class="icon-box tone-{{ $tone }}"><x-ti :name="$icon" /></span>
        </div>
        <p class="kpi-value">{{ $value }}</p>
        @if($delta !== null)
            <div class="kpi-foot"><span class="kpi-delta {{ $deltaUp ? 'is-up' : 'is-down' }}">{{ $delta }}</span></div>
        @endif
    </div>
</div>
