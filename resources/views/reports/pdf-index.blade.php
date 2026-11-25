@extends('reports.pdf-shell', ['title' => 'Ringkasan Keuangan', 'subtitle' => $startDate->format('d M Y') . ' – ' . $endDate->format('d M Y')])

@section('pdf-body')
<table>
<thead><tr><th>Metrik</th><th class="num">Nilai</th></tr></thead>
<tbody>
<tr><td>Total ditagih</td><td class="num">{{ number_format($totals['invoiced'], 0) }}</td></tr>
<tr><td>Terkumpul</td><td class="num">{{ number_format($totals['collected'], 0) }}</td></tr>
<tr><td>Outstanding</td><td class="num">{{ number_format($totals['outstanding'], 0) }}</td></tr>
<tr><td>Overdue</td><td class="num">{{ number_format($totals['overdue'], 0) }}</td></tr>
<tr><td>Jumlah invoice</td><td class="num">{{ $totals['invoiceCount'] }}</td></tr>
<tr><td>Jumlah pembayaran</td><td class="num">{{ $totals['paymentCount'] }}</td></tr>
</tbody>
</table>
<h3>Top klien</h3>
<table>
<thead><tr><th>Klien</th><th class="num">Revenue</th><th class="num">Invoice</th></tr></thead>
<tbody>
@foreach($topClients as $c)
<tr><td>{{ $c->name }}</td><td class="num">{{ number_format($c->revenue ?? 0, 0) }}</td><td class="num">{{ $c->invoice_count }}</td></tr>
@endforeach
</tbody>
</table>
@endsection
