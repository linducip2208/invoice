@extends('reports.pdf-shell', ['title' => 'Laba Rugi per Klien', 'subtitle' => 'Sejak ' . $startDate->format('d M Y')])

@section('pdf-body')
<table>
<thead><tr><th>Klien</th><th class="num">Revenue</th><th class="num">Biaya</th><th class="num">Waktu</th><th class="num">Profit</th><th class="num">Margin</th><th class="num">Inv</th></tr></thead>
<tbody>
@foreach($rows as $r)
<tr>
<td>{{ $r->client->name }}</td>
<td class="num">{{ number_format($r->revenue, 0) }}</td>
<td class="num">{{ number_format($r->expense, 0) }}</td>
<td class="num">{{ number_format($r->time_cost, 0) }}</td>
<td class="num">{{ number_format($r->profit, 0) }}</td>
<td class="num">{{ $r->margin_pct !== null ? $r->margin_pct . '%' : '—' }}</td>
<td class="num">{{ $r->invoice_count }}</td>
</tr>
@endforeach
</tbody>
</table>
<p><strong>Total revenue: {{ number_format($totals->revenue, 0) }} · Profit: {{ number_format($totals->profit, 0) }}{{ $totals->margin_pct !== null ? ' (' . $totals->margin_pct . '%)' : '' }}</strong></p>
@endsection
