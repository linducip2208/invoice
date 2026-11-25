@extends('reports.pdf-shell', ['title' => 'Laporan Pajak ' . $start->format('M Y'), 'subtitle' => $start->format('d M Y') . ' – ' . $end->format('d M Y')])

@section('pdf-body')
<p><strong>Total PPN: {{ number_format($totalPpn, 0) }} · Total PPh23: {{ number_format($totalPph23, 0) }}</strong></p>
<table>
<thead><tr><th>Invoice</th><th>Klien</th><th class="num">PPN</th><th class="num">PPh23</th><th>E-Faktur</th></tr></thead>
<tbody>
@foreach($taxDetails as $t)
<tr>
<td>{{ $t->invoice?->invoice_no ?? '—' }}</td>
<td>{{ $t->invoice?->client?->name ?? '—' }}</td>
<td class="num">{{ number_format($t->ppn_amount, 0) }}</td>
<td class="num">{{ number_format($t->pph23_amount, 0) }}</td>
<td>{{ $t->efaktur_status ?? '—' }}</td>
</tr>
@endforeach
</tbody>
</table>
@endsection
