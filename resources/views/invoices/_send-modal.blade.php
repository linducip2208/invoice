@props(['invoice'])

<div class="modal modal-blur fade" id="send-invoice-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('invoices.send', $invoice) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><x-ti name="send" /> {{ __('Send Invoice') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">{{ __('Sprinkle this invoice onto your client') }}</p>
                    <x-tabler.field :label="__('To Email')" name="to_email">
                        <input type="email" id="to_email" name="to_email" required
                            value="{{ old('to_email', $invoice->client->email ?? '') }}"
                            class="form-control @error('to_email') is-invalid @enderror">
                    </x-tabler.field>
                    <x-tabler.field :label="__('Subject')" name="subject">
                        <input type="text" id="subject" name="subject" required
                            value="{{ old('subject', __('Invoice') . ' ' . $invoice->invoice_no . ' from ' . (workspace()?->name ?? config('app.name'))) }}"
                            class="form-control @error('subject') is-invalid @enderror">
                    </x-tabler.field>
                    <x-tabler.field :label="__('Message')" name="message">
                        <textarea id="message" name="message" rows="4" class="form-control @error('message') is-invalid @enderror" placeholder="{{ __('Add a personal sparkle…') }}">{{ old('message') }}</textarea>
                    </x-tabler.field>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary"><x-ti name="send" /> {{ __('Send Invoice') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
