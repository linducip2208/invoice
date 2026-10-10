@extends('layouts.seo', [
    'metaTitle' => $planA->name . ' vs ' . $planB->name . ' — ' . branding('app_name'),
    'metaDescription' => 'Bandingkan plan ' . $planA->name . ' dan ' . $planB->name . ' fitur per fitur. Mana yang cocok untuk bisnis kamu?',
])

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-12">
    <h1 class="font-display text-3xl sm:text-5xl font-bold text-purple-900 text-center">
        {{ $planA->name }} <span class="text-rainbow">vs</span> {{ $planB->name }}
    </h1>
    <p class="text-center text-purple-500 mt-3">{{ __('Bandingkan fitur per fitur untuk pilih plan yang tepat.') }}</p>

    <div class="mt-10 card-unicorn">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b-2 border-purple-200">
                    <th class="text-left py-3 px-2">Feature</th>
                    <th class="py-3 px-2 text-center">{{ $planA->name }}</th>
                    <th class="py-3 px-2 text-center">{{ $planB->name }}</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-purple-100">
                    <td class="py-3 px-2 font-bold">Harga / bulan (IDR)</td>
                    <td class="py-3 px-2 text-center">Rp{{ number_format($planA->price_idr_monthly, 0, ',', '.') }}</td>
                    <td class="py-3 px-2 text-center">Rp{{ number_format($planB->price_idr_monthly, 0, ',', '.') }}</td>
                </tr>
                <tr class="border-b border-purple-100">
                    <td class="py-3 px-2 font-bold">Harga / tahun (IDR)</td>
                    <td class="py-3 px-2 text-center">Rp{{ number_format($planA->price_idr_yearly, 0, ',', '.') }}</td>
                    <td class="py-3 px-2 text-center">Rp{{ number_format($planB->price_idr_yearly, 0, ',', '.') }}</td>
                </tr>
                @foreach($featureLabels as $key => $label)
                <tr class="border-b border-purple-100">
                    <td class="py-3 px-2 font-medium text-purple-700">{{ $label }}</td>
                    @foreach([$planA, $planB] as $p)
                        @php $val = $p->features[$key] ?? null; @endphp
                        <td class="py-3 px-2 text-center">
                            @if(is_bool($val) || in_array($val, [true, false], true))
                                <span class="text-xl">{{ $val ? '✅' : '—' }}</span>
                            @elseif($val === null)
                                <span class="text-emerald-600 font-bold">∞ Unlimited</span>
                            @else
                                <span class="font-bold">{{ $val }}</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-10 grid grid-cols-2 gap-4">
        <a href="{{ route('register') }}?plan={{ $planA->slug }}" class="card-unicorn text-center hover:shadow-unicorn-glow">
            <p class="text-xs text-purple-400 uppercase font-bold">Pilih</p>
            <p class="font-display font-bold text-2xl">{{ $planA->name }}</p>
            <button class="btn-unicorn-secondary mt-3">{{ __('Mulai dengan ') . $planA->name }}</button>
        </a>
        <a href="{{ route('register') }}?plan={{ $planB->slug }}" class="card-unicorn text-center hover:shadow-unicorn-glow">
            <p class="text-xs text-purple-400 uppercase font-bold">Pilih</p>
            <p class="font-display font-bold text-2xl">{{ $planB->name }}</p>
            <button class="btn-unicorn mt-3">{{ __('Mulai dengan ') . $planB->name }}</button>
        </a>
    </div>

    <div class="mt-8 text-center">
        <a href="{{ route('pricing') }}" class="text-purple-500 hover:text-pink-500 text-sm">← {{ __('Lihat semua plan') }}</a>
    </div>
</div>
@endsection
