@extends('reports.pdf-shell', ['title' => 'Forecast Cashflow ' . $days . ' hari', 'subtitle' => 'Skenario: ' . ucfirst($scenario)])

@section('pdf-body')
<table>
<thead><tr><th>Minggu</th><th class="num">Inflow</th><th class="num">Outflow</th><th class="num">Net</th><th>Invoice</th></tr></thead>
<tbody>
@foreach($weeks as $w)
<tr>
<td>{{ $w->label }}</td>
<td class="num">{{ number_format($w->inflow, 0) }}</td>
<td class="num">{{ number_format($w->outflow, 0) }}</td>
<td class="num">{{ number_format($w->net, 0) }}</td>
<td>{{ $w->invoices->count() }} invoice</td>
</tr>
@endforeach
</tbody>
</table>
<p><strong>Total proyeksi: {{ number_format($totalProjected, 0) }} · Outflow: {{ number_format($totalOutflow, 0) }} · Net: {{ number_format($totalNet, 0) }}</strong></p>
@endsection
