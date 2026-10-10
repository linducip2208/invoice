@props(['status' => ''])
@php
// -lt (light tint) variants: tinted background + matching dark text, designed
// to meet contrast. Solid bg-* with inherited text fails WCAG AA (axe-proven).
$map = [
    'draft' => 'secondary-lt', 'sent' => 'info-lt', 'partial' => 'warning-lt',
    'paid' => 'success-lt', 'overdue' => 'danger-lt', 'cancelled' => 'dark-lt',
    'converted' => 'success-lt', 'pending' => 'warning-lt', 'active' => 'success-lt',
    'past_due' => 'danger-lt', 'expired' => 'secondary-lt', 'trial' => 'info-lt',
    'failed' => 'danger-lt', 'approved' => 'success-lt', 'rejected' => 'danger-lt',
    'refunded' => 'secondary-lt',
];
$color = $map[strtolower((string) $status)] ?? 'secondary-lt';
@endphp
<span class="badge bg-{{ $color }} text-uppercase">{{ __(ucfirst((string) $status)) }}</span>
