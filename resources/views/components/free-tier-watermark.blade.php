@props(['invoice'])
@php
    $shouldShow = ! $invoice->workspace?->planFeature('remove_watermark', false);
@endphp

@if($shouldShow)
<div style="margin-top: 24px; padding: 12px; text-align: center; border-top: 1px dashed #d8b4fe; font-size: 11px; color: #a78bfa;">
    ✦ {{ __('Made with') }} <a href="{{ url('/') }}" style="color: #a855f7; text-decoration: none; font-weight: 600;">Invocely</a> · {{ __('Sparkly invoicing for dreamy businesses') }} 🦄
</div>
@endif
