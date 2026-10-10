<x-tabler.public :title="__('Quote :no', ['no' => $quote->quote_no])" :meta-description="__('Quote :no from :ws', ['no' => $quote->quote_no, 'ws' => $quote->workspace?->name])">
    @push('head')
        <meta name="robots" content="noindex, nofollow">
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
                    <div class="subheader">{{ __('No') }} · {{ __('Revision :rev', ['rev' => $quote->revision]) }}</div>
                    <p class="h2 mb-1">{{ $quote->quote_no }}</p>
                    <x-tabler.status-badge :status="$quote->status" />
                </div>
            </div>
            <hr>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="subheader">{{ __('For') }}</div>
                    <p class="fw-bold mb-0">{{ $quote->client?->company_name ?: $quote->client?->name }}</p>
                    @if($quote->client?->company_name)<p class="text-muted mb-0">{{ $quote->client->name }}</p>@endif
                </div>
                <div class="col-md-6">
                    <div class="subheader">{{ __('Details') }}</div>
                    <p class="mb-1 text-muted"><x-ti name="calendar" /> {{ __('Issued') }}: <strong class="text-body">{{ $quote->issue_date->format('d M Y') }}</strong></p>
                    @if($quote->expiry_date)
                        <p class="mb-1 text-muted"><x-ti name="clock" /> {{ __('Expires') }}: <strong class="text-body">{{ $quote->expiry_date->format('d M Y') }}</strong></p>
                    @endif
                    <p class="mb-0 text-muted">{{ __('Currency') }}: <strong class="text-body">{{ $quote->currency }}</strong></p>
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
                    <div class="card h-100"><div class="card-body"><h4 class="subheader">{{ __('Notes') }}</h4><p class="mb-0">{{ $quote->notes }}</p></div></div>
                </div>
            @endif
            @if($quote->terms)
                <div class="col-md-6">
                    <div class="card h-100"><div class="card-body"><h4 class="subheader">{{ __('Terms & Conditions') }}</h4><p class="mb-0">{{ $quote->terms }}</p></div></div>
                </div>
            @endif
        </div>
    @endif

    {{-- ============ State-driven actions (viewing is never acceptance) ============ --}}
    @if(in_array($quote->status, ['accepted', 'converted']))
        <div class="card mb-3">
            <div class="card-body text-center">
                <div class="mb-2 text-success"><x-ti name="check" class="icon icon-lg" /></div>
                <h3 class="card-title justify-content-center">{{ __('Quotation approved') }}</h3>
                <p class="text-muted">{{ __('Approved at :at (UTC), revision :rev.', ['at' => $quote->accepted_at?->format('d M Y, H:i'), 'rev' => $quote->revision]) }}</p>
                @php $lastApproval = $quote->approvalEvents()->where('outcome', 'accepted')->latest('decided_at')->first(); @endphp
                @if($lastApproval)
                    <p class="text-muted small">{{ __('Document hash: :h', ['h' => $lastApproval->document_sha256]) }}</p>
                @endif
            </div>
        </div>
    @elseif($quote->status === 'rejected')
        <div class="card mb-3">
            <div class="card-body text-center">
                <h3 class="card-title justify-content-center">{{ __('Quotation declined') }}</h3>
                <p class="text-muted">{{ __('This revision was declined. Contact the issuer if this looks wrong.') }}</p>
            </div>
        </div>
    @elseif($quote->status === 'expired')
        <div class="card mb-3">
            <div class="card-body text-center">
                <h3 class="card-title justify-content-center">{{ __('Quotation expired') }}</h3>
                <p class="text-muted">{{ __('Ask the issuer for a new revision.') }}</p>
            </div>
        </div>
    @elseif($quote->status === 'withdrawn')
        <div class="card mb-3">
            <div class="card-body text-center">
                <h3 class="card-title justify-content-center">{{ __('Quotation withdrawn') }}</h3>
                <p class="text-muted">{{ __('The issuer withdrew this quotation. Outstanding links and codes are invalid.') }}</p>
            </div>
        </div>
    @else
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title"><x-ti name="shield-lock" /> {{ __('Review & Approval') }}</h3></div>
            <div class="card-body">
                <div class="alert alert-info" role="note">
                    {{ __('Opening this link only lets you review. Approval needs a verified step below — nothing here approves the quote by itself.') }}
                </div>
                @if($portalCanApprove)
                    <p>{{ __('You are signed in to the customer portal as an authorized approver.') }}</p>
                    <div class="btn-list">
                        <a href="{{ route('public.quote.confirm', $quote->public_token) }}" class="btn btn-primary">
                            <x-ti name="check" /> {{ __('Continue to approval') }}
                        </a>
                    </div>
                @else
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h4>{{ __('Approve with email code') }}</h4>
                            <form method="POST" action="{{ route('public.quote.otp.request', $quote->public_token) }}">
                                @csrf
                                <input type="hidden" name="purpose" value="accept">
                                <x-tabler.field :label="__('Your email on file')" name="email" type="email"
                                    :value="old('email', $quote->client?->email)" attrs="required autocomplete=email" />
                                <button type="submit" class="btn btn-primary w-100">{{ __('Send approval code') }}</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <h4>{{ __('Decline with email code') }}</h4>
                            <form method="POST" action="{{ route('public.quote.otp.request', $quote->public_token) }}">
                                @csrf
                                <input type="hidden" name="purpose" value="reject">
                                <x-tabler.field :label="__('Your email on file')" name="reject_email">
                                    <input type="email" id="reject_email" name="email" required autocomplete="email" value="{{ old('email', $quote->client?->email) }}" class="form-control">
                                </x-tabler.field>
                                <button type="submit" class="btn btn-outline-danger w-100">{{ __('Send decline code') }}</button>
                            </form>
                        </div>
                    </div>
                    <p class="text-muted small mt-3 mb-0">{{ __('Codes go only to the contact address on file, expire in 15 minutes, and are bound to this exact revision.') }}</p>
                @endif
            </div>
        </div>
    @endif

    <p class="text-center text-muted mt-3">{{ __('Powered by') }} <a href="/" class="fw-bold">Invocely</a></p>
</x-tabler.public>
