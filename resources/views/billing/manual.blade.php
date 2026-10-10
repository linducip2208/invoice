<x-tabler.layout :title="__('Pembayaran Manual')" :pretitle="__('Billing')" :subtitle="__('Transfer manual & upload bukti — admin verifikasi 1×24 jam')">
    @php
        $plan = $subscription->plan;
        $amount = $plan->priceFor($subscription->currency, $subscription->billing_period);
        $amountFormatted = $subscription->currency === 'IDR'
            ? 'Rp ' . number_format($amount, 0, ',', '.')
            : '$ ' . number_format($amount, 2);
    @endphp

    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="subheader">{{ __('Order Summary') }}</div>
                <div class="h2 mb-0">{{ $plan->name }}</div>
                <div class="text-muted">{{ ucfirst($subscription->billing_period) }} · {{ $subscription->currency }}</div>
            </div>
            <div class="h1 mb-0">{{ $amountFormatted }}</div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h3 class="card-title">{{ __('Transfer ke salah satu metode di bawah') }}</h3></div>
        <div class="card-body">
            @if($methods->isEmpty())
                <x-tabler.empty icon="wallet" :title="__('Belum ada metode pembayaran manual yang dikonfigurasi superadmin. Hubungi support.')" />
            @else
                <div class="row g-2">
                    @foreach($methods as $m)
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body d-flex gap-3">
                                    <span class="avatar"><x-ti name="wallet" /></span>
                                    <div class="flex-fill">
                                        <div class="fw-semibold">{{ $m->label }}</div>
                                        @if($m->bank_or_provider)
                                            <div class="text-muted small">{{ $m->bank_or_provider }}</div>
                                        @endif
                                        <div class="row g-2 mt-1">
                                            @if($m->account_number)
                                                <div class="col-md-6">
                                                    <div class="subheader">{{ __('No. Rekening / Akun') }}</div>
                                                    <code class="fw-semibold user-select-all">{{ $m->account_number }}</code>
                                                </div>
                                            @endif
                                            @if($m->account_name)
                                                <div class="col-md-6">
                                                    <div class="subheader">{{ __('A.N.') }}</div>
                                                    <div class="fw-semibold">{{ $m->account_name }}</div>
                                                </div>
                                            @endif
                                        </div>
                                        @if($m->instructions)
                                            <p class="small text-muted mt-2 mb-0" style="white-space: pre-line;">{{ $m->instructions }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="alert alert-warning mt-3 mb-0">
                <x-ti name="alert-triangle" /> {{ __('Transfer tepat sebesar') }} <strong>{{ $amountFormatted }}</strong> {{ __('dengan note "SUB-' . $subscription->id . '" supaya verifikasi cepat.') }}
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h3 class="card-title">{{ __('Upload Bukti Transfer') }}</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('billing.manual.proof', $subscription) }}" enctype="multipart/form-data">
                @csrf
                <x-tabler.field :label="__('Screenshot / foto bukti transfer *')" name="proof" :required="true">
                    <input type="file" id="proof" name="proof" accept="image/*" required class="form-control">
                </x-tabler.field>
                <x-tabler.field :label="__('Catatan (opsional)')" name="note">
                    <textarea id="note" name="note" rows="2" placeholder="{{ __('Misal: transfer dari BCA an. Budi') }}" class="form-control"></textarea>
                </x-tabler.field>
                <button class="btn btn-primary"><x-ti name="upload" /> {{ __('Upload & Submit') }}</button>
            </form>

            @if($subscription->manual_proof_path)
                <div class="alert alert-success mt-3 mb-0">
                    <x-ti name="check" /> {{ __('Bukti sudah di-upload') }} ({{ __('upload ulang akan menggantikan yang lama') }}).
                </div>
            @endif
        </div>
    </div>

    <a href="{{ route('billing.show') }}" class="btn">
        <x-ti name="arrow-left" /> {{ __('Kembali ke Billing') }}
    </a>
</x-tabler.layout>
