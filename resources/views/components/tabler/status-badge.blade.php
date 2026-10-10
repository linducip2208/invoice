@props(['status' => ''])
@php
$map = [
    'draft' => 'secondary', 'sent' => 'info', 'partial' => 'warning',
    'paid' => 'success', 'overdue' => 'danger', 'cancelled' => 'dark',
    'converted' => 'success', 'pending' => 'warning', 'active' => 'success',
    'past_due' => 'danger', 'expired' => 'secondary', 'trial' => 'info',
    'failed' => 'danger', 'approved' => 'success', 'rejected' => 'danger',
    'refunded' => 'secondary',
];
$color = $map[strtolower((string) $status)] ?? 'secondary';
@endphp
<span class="badge bg-{{ $color }} text-uppercase">{{ __(ucfirst((string) $status)) }}</span>
