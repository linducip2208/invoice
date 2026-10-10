<x-tabler.public :title="__('Enter approval code')">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title text-center">{{ __('Enter approval code') }}</h2>
                    <p class="text-muted text-center">
                        {{ __('Quotation :no — we sent a 6-digit code to the address on file.', ['no' => $quote->quote_no]) }}
                    </p>
                    <x-tabler.alert />
                    <form method="POST" action="{{ route('public.quote.verify', $quote->public_token) }}" autocomplete="off">
                        @csrf
                        <input type="hidden" name="purpose" value="{{ request('purpose', 'accept') }}">
                        <x-tabler.field :label="__('6-digit code')" name="otp">
                            <input type="text" id="otp" name="otp" required autofocus inputmode="numeric"
                                pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code"
                                placeholder="••••••" class="form-control form-control-lg text-center @error('otp') is-invalid @enderror">
                        </x-tabler.field>
                        <button type="submit" class="btn btn-primary w-100">{{ __('Verify code') }}</button>
                    </form>
                    <p class="text-muted small mt-3 mb-0">
                        {{ __('Codes expire in 15 minutes and lock after :n wrong attempts.', ['n' => \App\Services\QuoteApprovalService::OTP_MAX_ATTEMPTS]) }}
                        <a href="{{ route('public.quote.show', $quote->public_token) }}">{{ __('Request a new code') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-tabler.public>
