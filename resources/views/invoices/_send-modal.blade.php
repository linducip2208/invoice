@props(['invoice'])

<x-modal name="send-invoice-modal" maxWidth="lg">
    <form method="POST" action="{{ route('invoices.send', $invoice) }}" class="p-6">
        @csrf

        <div class="flex items-center gap-3 mb-1">
            <span class="text-3xl">📨</span>
            <div>
                <h2 class="font-display text-xl font-bold text-purple-900">{{ __('Send Invoice') }}</h2>
                <p class="text-sm text-purple-500">{{ __('Sprinkle this invoice onto your client') }}</p>
            </div>
        </div>

        <div class="mt-5 space-y-4">
            <div>
                <x-input-label for="to_email" :value="__('To Email')" />
                <x-text-input id="to_email" name="to_email" type="email" class="mt-1 block w-full" required value="{{ old('to_email', $invoice->client->email ?? '') }}" />
                <x-input-error :messages="$errors->get('to_email')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="subject" :value="__('Subject')" />
                <x-text-input id="subject" name="subject" type="text" class="mt-1 block w-full" required value="{{ old('subject', '✦ Invoice ' . $invoice->invoice_no . ' from ' . (workspace()?->name ?? config('app.name'))) }}" />
                <x-input-error :messages="$errors->get('subject')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="message" :value="__('Message')" />
                <textarea id="message" name="message" rows="4" class="mt-1 block w-full input-unicorn rounded-xl px-4 py-2" placeholder="{{ __('Add a personal sparkle…') }}">{{ old('message') }}</textarea>
                <x-input-error :messages="$errors->get('message')" class="mt-1" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <x-secondary-button x-on:click="$dispatch('close-modal', 'send-invoice-modal')">
                {{ __('Cancel') }}
            </x-secondary-button>
            <x-primary-button>
                ✦ {{ __('Send Invoice') }}
            </x-primary-button>
        </div>
    </form>
</x-modal>
