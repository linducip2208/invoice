<x-tabler.public :title="__('Quote :no', ['no' => $quote->quote_no])" :meta-description="__('Quote :no from :ws', ['no' => $quote->quote_no, 'ws' => $quote->workspace?->name])">
    @push('head')
        <meta name="robots" content="noindex, nofollow">
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js" defer></script>
    @endpush

    <x-tabler.alert />

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="subheader">{{ __('Quote from') }}</div>
                    <h2 class="page-title">{{ $quote->workspace?->name }}</h2>
                    @if($quote->workspace?->address)
                        <p class="text-muted mb-0">{{ $quote->workspace->address }}</p>
                    @endif
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="subheader">{{ __('No') }}</div>
                    <p class="h2 mb-1">{{ $quote->quote_no }}</p>
                    @php
                        $statusBadge = match($quote->status) {
                            'accepted','converted' => ['green', __('Accepted')],
                            'rejected' => ['red', __('Rejected')],
                            'expired' => ['yellow', __('Expired')],
                            'sent' => ['blue', __('Sent')],
                            default => ['yellow', __('Draft')],
                        };
                    @endphp
                    <span class="badge bg-{{ $statusBadge[0] }}-lt">{{ $statusBadge[1] }}</span>
                </div>
            </div>
            <hr>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="subheader">{{ __('For') }}</div>
                    <p class="fw-bold mb-0">{{ $quote->client?->company_name ?: $quote->client?->name }}</p>
                    @if($quote->client?->company_name)<p class="text-muted mb-0">{{ $quote->client->name }}</p>@endif
                    @if($quote->client?->email)<p class="text-muted mb-0">{{ $quote->client->email }}</p>@endif
                </div>
                <div class="col-md-6">
                    <div class="subheader">{{ __('Details') }}</div>
                    <p class="mb-1 text-muted"><x-ti name="calendar" /> {{ __('Issued') }}: <strong class="text-body">{{ $quote->issue_date->format('d M Y') }}</strong></p>
                    @if($quote->expiry_date)
                        <p class="mb-1 text-muted"><x-ti name="clock" /> {{ __('Expires') }}: <strong class="text-body">{{ $quote->expiry_date->format('d M Y') }}</strong></p>
                    @endif
                    <p class="mb-0 text-muted"><x-ti name="currency-dollar" /> {{ __('Currency') }}: <strong class="text-body">{{ $quote->currency }}</strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h3 class="card-title"><x-ti name="file-text" /> {{ __('Job Details') }}</h3></div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Description') }}</th>
                        <th class="text-end">{{ __('Qty') }}</th>
                        <th class="text-end">{{ __('Price') }}</th>
                        <th class="text-end">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quote->items as $item)
                        <tr>
                            <td>{{ $item['description'] }}</td>
                            <td class="text-end">{{ number_format($item['quantity'], 2) }}</td>
                            <td class="text-end">{{ number_format($item['unit_price'], 0) }}</td>
                            <td class="text-end fw-bold">{{ number_format($item['quantity'] * $item['unit_price'], 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <div class="row justify-content-end">
                <div class="col-md-5">
                    <div class="d-flex justify-content-between"><span class="text-muted">{{ __('Subtotal') }}</span><span class="fw-semibold">{{ number_format($quote->subtotal, 0) }}</span></div>
                    @if($quote->tax_amount > 0)
                        <div class="d-flex justify-content-between"><span class="text-muted">{{ __('Tax') }} ({{ number_format($quote->tax_rate, 0) }}%)</span><span>{{ number_format($quote->tax_amount, 0) }}</span></div>
                    @endif
                    <div class="d-flex justify-content-between border-top pt-2 mt-2">
                        <span class="fw-bold">{{ __('Total') }}</span>
                        <span class="fw-bold">{{ number_format($quote->total, 0) }} {{ $quote->currency }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($quote->notes || $quote->terms)
        <div class="row row-cards mb-3">
            @if($quote->notes)
                <div class="col-md-6">
                    <div class="card h-100"><div class="card-body"><h4 class="subheader">{{ __('Notes') }}</h4><p class="mb-0 whitespace-pre-line">{{ $quote->notes }}</p></div></div>
                </div>
            @endif
            @if($quote->terms)
                <div class="col-md-6">
                    <div class="card h-100"><div class="card-body"><h4 class="subheader">{{ __('Terms & Conditions') }}</h4><p class="mb-0 whitespace-pre-line">{{ $quote->terms }}</p></div></div>
                </div>
            @endif
        </div>
    @endif

    @if($isSigned)
        <div class="card mb-3">
            <div class="card-body text-center">
                <div class="mb-2 text-success"><x-ti name="check" class="icon icon-lg" /></div>
                <h3 class="card-title justify-content-center">{{ __('Quote already signed') }}</h3>
                @foreach($signatures as $sig)
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="row g-2 align-items-center">
                                <div class="col text-start">
                                    <p class="fw-bold mb-0">{{ $sig->signer_name }}</p>
                                    <p class="text-muted mb-0">{{ $sig->signer_email }} &middot; {{ $sig->signed_at->format('d M Y, H:i') }}</p>
                                </div>
                                <div class="col-auto">
                                    @if($sig->signature_image_path)
                                        <img src="{{ asset('storage/' . $sig->signature_image_path) }}" alt="{{ __('Signature') }}" class="img-responsive" style="max-height: 4rem;">
                                    @elseif($sig->signature_typed)
                                        <p class="fw-bold fst-italic h3 mb-0">{{ $sig->signature_typed }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif(in_array($quote->status, ['draft', 'sent']))
        <div class="card mb-3" x-data="signaturePad()">
            <div class="card-header"><h3 class="card-title"><x-ti name="pencil" /> {{ __('Approve Quote') }}</h3></div>
            <div class="card-body">
                <p class="text-muted">{{ __('Sign to accept this quote. Once signed, it will be converted to an invoice automatically.') }}</p>
                <form method="POST" action="{{ route('public.quote.sign', $quote->public_token) }}" @submit="onSubmit($event)">
                    @csrf
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <x-tabler.field :label="__('Name')" name="signer_name" :required="true">
                                <input type="text" id="signer_name" name="signer_name" required maxlength="120" value="{{ old('signer_name', $quote->client?->name) }}" class="form-control @error('signer_name') is-invalid @enderror">
                            </x-tabler.field>
                        </div>
                        <div class="col-md-6">
                            <x-tabler.field :label="__('Email')" name="signer_email" :required="true">
                                <input type="email" id="signer_email" name="signer_email" required value="{{ old('signer_email', $quote->client?->email) }}" class="form-control @error('signer_email') is-invalid @enderror">
                            </x-tabler.field>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="btn-group mb-2" role="group">
                            <button type="button" @click="mode = 'draw'" :class="mode === 'draw' ? 'btn btn-primary btn-sm' : 'btn btn-sm'" class="btn btn-sm"><x-ti name="pencil" /> {{ __('Draw') }}</button>
                            <button type="button" @click="mode = 'type'" :class="mode === 'type' ? 'btn btn-primary btn-sm' : 'btn btn-sm'" class="btn btn-sm"><x-ti name="keyboard" /> {{ __('Type Name') }}</button>
                        </div>
                        <div x-show="mode === 'draw'">
                            <div class="form-control p-0 overflow-hidden">
                                <canvas x-ref="canvas" class="d-block w-100" style="height: 200px; touch-action: none;"></canvas>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <p class="text-muted mb-0">{{ __('Draw your signature above') }}</p>
                                <button type="button" @click="clear()" class="btn btn-sm btn-link text-danger">{{ __('Clear') }}</button>
                            </div>
                            <input type="hidden" name="signature_data" :value="signatureData">
                        </div>
                        <div x-show="mode === 'type'" class="form-control p-3">
                            <input type="text" name="signature_typed" maxlength="120" placeholder="{{ __('Type your full name') }}" class="form-control-plaintext text-center fst-italic h3">
                        </div>
                        @error('signature_data')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="alert alert-warning">{{ __('By signing, you agree to the terms of this quote. IP and timestamp are recorded for verification.') }}</div>
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <button type="submit" class="btn btn-primary"><x-ti name="check" /> {{ __('Sign & Approve') }}</button>
                    </div>
                </form>
                <form method="POST" action="{{ route('public.quote.reject', $quote->public_token) }}" onsubmit="return confirm('{{ __('Are you sure you want to reject this quote?') }}')" class="mt-2 text-end">
                    @csrf
                    <button type="submit" class="btn btn-link text-danger btn-sm">{{ __('Reject Quote') }}</button>
                </form>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <x-tabler.empty icon="file-text" :title="__('Quote :status', ['status' => $quote->status])" />
            </div>
        </div>
    @endif

    <p class="text-center text-muted mt-3">{{ __('Powered by') }} <a href="/" class="fw-bold">Invocely</a></p>

    @push('scripts')
        <script>
            function signaturePad() {
                return {
                    mode: 'draw',
                    pad: null,
                    signatureData: '',
                    init() {
                        this.$nextTick(() => this.initPad());
                    },
                    initPad() {
                        if (typeof SignaturePad === 'undefined') {
                            setTimeout(() => this.initPad(), 100);
                            return;
                        }
                        const canvas = this.$refs.canvas;
                        const resize = () => {
                            const ratio = window.devicePixelRatio || 1;
                            canvas.width = canvas.offsetWidth * ratio;
                            canvas.height = canvas.offsetHeight * ratio;
                            canvas.getContext('2d').scale(ratio, ratio);
                            if (this.pad) this.pad.clear();
                        };
                        window.addEventListener('resize', resize);
                        resize();
                        this.pad = new SignaturePad(canvas, {
                            penColor: '#206bc4',
                            backgroundColor: 'rgba(0,0,0,0)',
                        });
                    },
                    clear() {
                        if (this.pad) this.pad.clear();
                        this.signatureData = '';
                    },
                    onSubmit(e) {
                        if (this.mode === 'draw') {
                            if (this.pad && !this.pad.isEmpty()) {
                                this.signatureData = this.pad.toDataURL('image/png');
                            } else {
                                e.preventDefault();
                                alert(@json(__('Please draw your signature, or switch to "Type Name" mode.')));
                                return false;
                            }
                        }
                    }
                };
            }
        </script>
    @endpush
</x-tabler.public>
