@props(['name' => 'info-circle', 'class' => 'icon'])
@php
$path = resource_path('icons/tabler/' . preg_replace('/[^a-z0-9-]/', '', $name) . '.svg');
$svg = is_file($path) ? file_get_contents($path) : null;
if ($svg) {
    // Normalize size classes: keep Tabler stroke styling, allow sizing via class.
    $svg = preg_replace('/\s(width|height)="24"/', '', $svg);
    $svg = preg_replace('/class="[^"]*"/', 'class="' . e($class) . '"', $svg, 1);
}
@endphp
@if($svg)
{!! $svg !!}
@else
<svg class="{{ $class }}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M12 8v.01M12 11v5" /></svg>
@endif
