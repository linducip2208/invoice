@extends('reports.pdf-shell', ['title' => 'Laporan Aging Piutang', 'subtitle' => 'Per ' . $asOf->format('d M Y')])

@section('pdf-body')
<table>
<thead><tr><th>Klien</th><th>Email</th><th class="num">Belum JT</th><th class="num">1–30</th><th class="num">31–60</th><th class="num">61–90</th><th class="num">90+</th><th class="num">Total</th></tr></thead>
<tbody>
@foreach($byClient as $row)
<tr>
<td>{{ $row->client->name ?? '—' }}</td>
<td>{{ $row->client->email ?? '' }}</td>
<td class="num">{{ number_format($row->buckets['current'], 0) }}</td>
<td class="num">{{ number_format($row->buckets['1_30'], 0) }}</td>
<td class="num">{{ number_format($row->buckets['31_60'], 0) }}</td>
<td class="num">{{ number_format($row->buckets['61_90'], 0) }}</td>
<td class="num">{{ number_format($row->buckets['over_90'], 0) }}</td>
<td class="num">{{ number_format($row->total, 0) }}</td>
</tr>
@endforeach
</tbody>
</table>
<p><strong>Grand total: {{ number_format($grandTotal, 0) }}</strong></p>
@endsection
