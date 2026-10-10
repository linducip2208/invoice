<x-tabler.public :title="$planA->name . ' vs ' . $planB->name" :meta-description="__('Compare the :a and :b plans feature by feature.', ['a' => $planA->name, 'b' => $planB->name])">
    <div class="page-header">
        <div class="row g-2 align-items-center">
            <div class="col text-center">
                <h1 class="page-title">{{ $planA->name }} {{ __('vs') }} {{ $planB->name }}</h1>
                <div class="text-muted mt-1">{{ __('Compare features side by side to pick the right plan.') }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Feature') }}</th>
                        <th class="text-center">{{ $planA->name }}</th>
                        <th class="text-center">{{ $planB->name }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold">{{ __('Price / month (IDR)') }}</td>
                        <td class="text-center">Rp{{ number_format($planA->price_idr_monthly, 0, ',', '.') }}</td>
                        <td class="text-center">Rp{{ number_format($planB->price_idr_monthly, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">{{ __('Price / year (IDR)') }}</td>
                        <td class="text-center">Rp{{ number_format($planA->price_idr_yearly, 0, ',', '.') }}</td>
                        <td class="text-center">Rp{{ number_format($planB->price_idr_yearly, 0, ',', '.') }}</td>
                    </tr>
                    @foreach($featureLabels as $key => $label)
                        <tr>
                            <td class="text-muted">{{ $label }}</td>
                            @foreach([$planA, $planB] as $p)
                                @php $val = $p->features[$key] ?? null; @endphp
                                <td class="text-center">
                                    @if(is_bool($val))
                                        @if($val)
                                            <span class="badge bg-green-lt"><x-ti name="check" /> {{ __('Yes') }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    @elseif($val === null)
                                        <span class="badge bg-blue-lt">{{ __('Unlimited') }}</span>
                                    @else
                                        <span class="fw-bold">{{ $val }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row row-cards mt-4">
        <div class="col-sm-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="subheader">{{ __('Choose') }}</div>
                    <h3 class="card-title justify-content-center">{{ $planA->name }}</h3>
                    <a href="{{ route('register') }}?plan={{ $planA->slug }}" class="btn mt-2">{{ __('Start with ') . $planA->name }}</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="subheader">{{ __('Choose') }}</div>
                    <h3 class="card-title justify-content-center">{{ $planB->name }}</h3>
                    <a href="{{ route('register') }}?plan={{ $planB->slug }}" class="btn btn-primary mt-2">{{ __('Start with ') . $planB->name }}</a>
                </div>
            </div>
        </div>
    </div>

    <p class="text-center mt-4">
        <a href="{{ route('pricing') }}" class="text-muted">&larr; {{ __('See all plans') }}</a>
    </p>
</x-tabler.public>
