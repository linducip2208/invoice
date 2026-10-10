<x-tabler.public :title="__('Confirm approval')">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">{{ __('You are approving quotation :no, revision :rev.', ['no' => $quote->quote_no, 'rev' => $quote->revision]) }}</h3>
                </div>
                <div class="card-body">
                    <div class="datagrid mb-3">
                        <div class="datagrid-item">
                            <div class="datagrid-title">{{ __('Total') }}</div>
                            <div class="datagrid-content h3 mb-0">{{ $quote->currency }} {{ number_format((float) $quote->total, 2) }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">{{ __('Verified as') }}</div>
                            <div class="datagrid-content">{{ $context['email'] }} ({{ $context['method'] === 'portal' ? __('portal approver') : __('email code') }})</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">{{ __('Document hash') }}</div>
                            <div class="datagrid-content"><code>{{ $hash }}</code></div>
                        </div>
                    </div>
                    @if($quote->terms)
                        <h4>{{ __('Payment terms') }}</h4>
                        <p>{{ $quote->terms }}</p>
                    @endif
                    <div class="alert alert-secondary" role="note">
                        <strong>{{ __('Consent') }}:</strong> {{ $consent }}
                    </div>
                    <x-tabler.alert />

                    @if($context['purpose'] === 'accept' || $context['purpose'] === null)
                        <form method="POST" action="{{ route('public.quote.confirm.post', $quote->public_token) }}" class="mb-3">
                            @csrf
                            <input type="hidden" name="purpose" value="accept">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <x-tabler.field :label="__('Your full name')" name="accept_name">
                                        <input type="text" id="accept_name" name="name" required maxlength="120" autocomplete="name" class="form-control">
                                    </x-tabler.field>
                                </div>
                                <div class="col-md-6">
                                    <x-tabler.field :label="__('Role / title (optional)')" name="accept_role">
                                        <input type="text" id="accept_role" name="role" maxlength="120" autocomplete="organization-title" class="form-control">
                                    </x-tabler.field>
                                </div>
                            </div>
                            <x-tabler.field :label="__('Typed signature (optional supporting record)')" name="accept_sig">
                                <input type="text" id="accept_sig" name="signature_typed" maxlength="120" placeholder="{{ __('Type your full name') }}" class="form-control">
                            </x-tabler.field>
                            <label class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="consent" value="1" required>
                                <span class="form-check-label required">{{ __('I have read the quotation above and I approve it with the authority I declared.') }}</span>
                            </label>
                            <button type="submit" class="btn btn-success w-100">
                                <x-ti name="check" /> {{ __('Approve quotation') }}
                            </button>
                        </form>
                    @endif

                    @if($context['purpose'] === 'reject' || $context['purpose'] === null)
                        <form method="POST" action="{{ route('public.quote.confirm.post', $quote->public_token) }}">
                            @csrf
                            <input type="hidden" name="purpose" value="reject">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <x-tabler.field :label="__('Your full name')" name="reject_name">
                                        <input type="text" id="reject_name" name="name" required maxlength="120" autocomplete="name" class="form-control">
                                    </x-tabler.field>
                                </div>
                                <div class="col-md-6">
                                    <x-tabler.field :label="__('Reason (optional)')" name="reject_reason">
                                        <input type="text" id="reject_reason" name="reason" maxlength="500" class="form-control">
                                    </x-tabler.field>
                                </div>
                            </div>
                            <label class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="consent" value="1" required>
                                <span class="form-check-label required">{{ __('I confirm I want to decline this quotation.') }}</span>
                            </label>
                            <button type="submit" class="btn btn-outline-danger w-100">{{ __('Decline quotation') }}</button>
                        </form>
                    @endif
                </div>
            </div>
            <p class="text-center"><a href="{{ route('public.quote.show', $quote->public_token) }}">{{ __('Back to review') }}</a></p>
        </div>
    </div>
</x-tabler.public>
