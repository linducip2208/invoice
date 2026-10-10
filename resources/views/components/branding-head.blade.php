@php
    $appName = branding('app_name');
    $logo = branding('logo_path');
    $favicon = branding('favicon_path');
    $primary = branding('primary_color');
    $accent = branding('accent_color');
    $sky = branding('sky_color');
    $fontDisplay = branding('font_display');
    $fontBody = branding('font_body');
    $fontDisplayKey = strtolower(str_replace(' ', '-', $fontDisplay));
    $fontBodyKey = strtolower(str_replace(' ', '-', $fontBody));
@endphp

@if($favicon)
    <link rel="icon" type="image/png" href="{{ asset('storage/' . $favicon) }}">
@endif

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family={{ urlencode(strtolower(str_replace(' ', '+', $fontDisplay))) }}:400,600,700|{{ urlencode(strtolower(str_replace(' ', '+', $fontBody))) }}:400,500,600,700&display=swap" rel="stylesheet" />

<style>
    :root {
        --brand-primary: {{ $primary }};
        --brand-accent: {{ $accent }};
        --brand-sky: {{ $sky }};
        --brand-font-display: '{{ $fontDisplay }}', system-ui, sans-serif;
        --brand-font-body: '{{ $fontBody }}', system-ui, sans-serif;
    }
    body { font-family: var(--brand-font-body); }
    .font-display, .heading-unicorn, h1, h2, h3.font-display { font-family: var(--brand-font-display); }
</style>
