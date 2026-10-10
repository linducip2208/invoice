<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="heading-unicorn flex items-center gap-2">{{ __('Workspace Settings') }} <span class="text-2xl">⚙️</span></h2>
            <p class="text-sm text-purple-500 mt-1">✦ {{ __('Decorate your magical realm') }}</p>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        @if (session('status') === 'workspace-updated')
            <div class="mb-6 px-5 py-3 rounded-2xl text-sm font-semibold text-green-800 flex items-center gap-2" style="background: linear-gradient(135deg, #DCFCE7, #BBF7D0); border: 1px solid rgba(134,239,172,0.5);">
                <span class="text-xl">🦄</span> {{ __('Workspace settings sparkled successfully.') }}
            </div>
        @endif

        <form method="POST" action="{{ route('workspace.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PATCH')

            <div class="card-unicorn">
                <div class="rainbow-bar -mx-6 -mt-6 mb-6 rounded-t-2xl"></div>
                <h3 class="font-display font-bold text-lg text-purple-900">✦ {{ __('Company Information') }}</h3>
                <p class="text-sm text-purple-500 mt-1 mb-5">{{ __('These details appear on every invoice.') }}</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <x-input-label :value="__('Company Name *')" />
                        <x-text-input class="block mt-1 w-full" type="text" name="name" :value="old('name', $workspace->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label :value="__('Tax ID / NPWP')" />
                        <x-text-input class="block mt-1 w-full" type="text" name="tax_id" :value="old('tax_id', $workspace->tax_id)" placeholder="12.345.678.9-123.000" />
                    </div>
                    <div>
                        <x-input-label :value="__('Phone')" />
                        <x-text-input class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $workspace->phone)" />
                    </div>
                    <div>
                        <x-input-label :value="__('Company Email')" />
                        <x-text-input class="block mt-1 w-full" type="email" name="email" :value="old('email', $workspace->email)" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label :value="__('Address')" />
                        <textarea name="address" rows="2" class="block mt-1 w-full input-unicorn rounded-xl px-4 py-2">{{ old('address', $workspace->address) }}</textarea>
                    </div>
                    <div>
                        <x-input-label :value="__('City')" />
                        <x-text-input class="block mt-1 w-full" type="text" name="city" :value="old('city', $workspace->city)" />
                    </div>
                    <div>
                        <x-input-label :value="__('State / Province')" />
                        <x-text-input class="block mt-1 w-full" type="text" name="state" :value="old('state', $workspace->state)" />
                    </div>
                    <div>
                        <x-input-label :value="__('Country')" />
                        <x-text-input class="block mt-1 w-full" type="text" name="country" :value="old('country', $workspace->country)" />
                    </div>
                    <div>
                        <x-input-label :value="__('Postal Code')" />
                        <x-text-input class="block mt-1 w-full" type="text" name="postal_code" :value="old('postal_code', $workspace->postal_code)" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label :value="__('Company Logo')" />
                        @if($workspace->logo_path)
                            <div class="mt-1 mb-3 flex items-center gap-3">
                                <img src="{{ asset('storage/' . $workspace->logo_path) }}" alt="Logo" class="h-16 w-auto object-contain border-2 border-purple-200 rounded-xl p-2 bg-white">
                                <span class="text-xs text-purple-500">{{ __('Current logo — upload a new one to replace') }}</span>
                            </div>
                        @endif
                        <input type="file" name="logo" accept="image/*" class="block mt-1 w-full text-sm text-purple-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-gradient-to-r file:from-pink-100 file:to-purple-100 file:text-purple-700 hover:file:from-pink-200 hover:file:to-purple-200 cursor-pointer" />
                    </div>
                </div>
            </div>

            <div class="card-unicorn">
                <h3 class="font-display font-bold text-lg text-purple-900">✦ {{ __('Invoice Settings') }}</h3>
                <p class="text-sm text-purple-500 mt-1 mb-5">{{ __('Numbering, currency, template, and branding.') }}</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <x-input-label :value="__('Invoice Prefix *')" />
                        <x-text-input class="block mt-1 w-full" type="text" name="invoice_prefix" :value="old('invoice_prefix', $workspace->invoice_prefix)" required />
                        <p class="mt-1 text-xs text-purple-400">✦ {{ __('Next:') }} <span class="font-semibold text-pink-500">{{ $workspace->invoice_prefix }}{{ now()->format('Ymd') }}-{{ str_pad($workspace->next_invoice_no, 4, '0', STR_PAD_LEFT) }}</span></p>
                    </div>
                    <div>
                        <x-input-label :value="__('Currency *')" />
                        <select name="currency" class="block mt-1 w-full input-unicorn rounded-xl px-4 py-2">
                            @foreach ($currencies as $code => $label)
                                <option value="{{ $code }}" @selected(old('currency', $workspace->currency) === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label :value="__('Default Template')" />
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-2">
                            @php $tplGradients = ['classic'=>'from-purple-100 to-blue-100','modern'=>'from-pink-100 to-purple-100','minimal'=>'from-gray-100 to-purple-50','bold'=>'from-pink-200 to-yellow-100','elegant'=>'from-purple-200 to-pink-100']; @endphp
                            @foreach ($templates as $value => $label)
                                <label class="relative rounded-xl p-3 cursor-pointer border-2 border-purple-100 hover:border-pink-300 has-[:checked]:border-pink-400 has-[:checked]:shadow-unicorn-pink transition">
                                    <input type="radio" name="default_template" value="{{ $value }}" {{ old('default_template', $workspace->default_template) === $value ? 'checked' : '' }} class="sr-only">
                                    <div class="text-center">
                                        <div class="w-full h-14 rounded-lg bg-gradient-to-br {{ $tplGradients[$value] ?? 'from-purple-100 to-pink-100' }} mb-2"></div>
                                        <span class="text-sm font-semibold text-purple-700">{{ $label }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <x-input-label :value="__('Primary Color *')" />
                        <div class="flex items-center gap-3 mt-1">
                            <input type="color" id="primary_color_picker" value="{{ old('primary_color', $workspace->primary_color) }}" class="h-11 w-16 rounded-xl border-2 border-purple-200 cursor-pointer">
                            <x-text-input class="block w-full" type="text" name="primary_color" id="primary_color" :value="old('primary_color', $workspace->primary_color)" required pattern="^#[0-9A-Fa-f]{6}$" />
                        </div>
                    </div>
                    <div>
                        <x-input-label :value="__('Accent Color *')" />
                        <div class="flex items-center gap-3 mt-1">
                            <input type="color" id="accent_color_picker" value="{{ old('accent_color', $workspace->accent_color) }}" class="h-11 w-16 rounded-xl border-2 border-purple-200 cursor-pointer">
                            <x-text-input class="block w-full" type="text" name="accent_color" id="accent_color" :value="old('accent_color', $workspace->accent_color)" required pattern="^#[0-9A-Fa-f]{6}$" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end pb-8">
                <x-primary-button class="px-8 py-3">✦ {{ __('Save Settings') }} ✦</x-primary-button>
            </div>
        </form>
    </div>

    <script>
        ['primary','accent'].forEach(function(prefix){
            const picker = document.getElementById(prefix + '_color_picker');
            const text = document.getElementById(prefix + '_color');
            picker.addEventListener('input', () => text.value = picker.value);
            text.addEventListener('input', () => picker.value = text.value);
        });
    </script>
</x-app-layout>
