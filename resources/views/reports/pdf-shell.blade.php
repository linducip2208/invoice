<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>{{ $title ?? 'Laporan' }} — {{ $workspace->name ?? config('app.name') }}</title>
<style>
body { font-family: Helvetica, Arial, sans-serif; color: #111; font-size: 11px; }
h1 { font-size: 18px; margin: 0 0 2px; }
.sub { color: #555; font-size: 11px; margin-bottom: 12px; }
table { width: 100%; border-collapse: collapse; margin: 8px 0 14px; }
th, td { border: 1px solid #999; padding: 5px 7px; text-align: left; }
th { background: #eee; }
td.num, th.num { text-align: right; }
.total td { font-weight: bold; background: #f4f4f4; }
.small { color: #555; font-size: 10px; }
</style>
</head>
<body>
<h1>{{ $title ?? 'Laporan' }}</h1>
<div class="sub">{{ $workspace->name ?? '' }} · {{ $subtitle ?? '' }} · Dicetak {{ now()->format('d M Y H:i') }}</div>
@yield('pdf-body')
<p class="small">Angka dari data aplikasi per tanggal cetak. Cancelled dikecualikan dari revenue.</p>
</body>
</html>
