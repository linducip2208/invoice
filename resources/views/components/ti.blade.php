@props(['name' => 'info-circle', 'class' => 'icon'])
@php
$path = resource_path('icons/tabler/' . preg_replace('/[^a-z0-9-]/', '', $name) . '.svg');
$svg = is_file($path) ? file_get_contents($path) : null;
if ($svg) {
    // Selalu paksa class `icon` agar ukuran terkunci via CSS; width/height
    // 24 dipertahankan sebagai fallback bila CSS belum termuat.
    $cls = trim(preg_replace('/\s+/', ' ', 'icon ' . preg_replace('/\bicon\b/', '', $class)));
    if (! preg_match('/<svg[^>]*\swidth=/', $svg)) {
        $svg = preg_replace('/<svg\b/', '<svg width="24" height="24"', $svg, 1);
    }
    $svg = preg_replace('/class="[^"]*"/', 'class="' . e($cls) . '"', $svg, 1);
    if (! str_contains($svg, 'aria-hidden')) {
        $svg = preg_replace('/<svg\b/', '<svg aria-hidden="true"', $svg, 1);
    }
}
@endphp
@if($svg)
{!! $svg !!}
@else
<svg class="icon {{ $class }}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M12 8v.01M12 11v5" /></svg>
@endif
