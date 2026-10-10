<x-tabler.layout :title="__('Quick Invoice')" :pretitle="__('Mobile')">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Quick Invoice') }}</h3>
                    <div class="card-subtitle">{{ __('3 field. 1 klik kirim. Bahkan dari HP.') }}</div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('mobile.quick-invoice.store') }}">
                        @csrf
                        <x-tabler.field :label="__('Klien *')" name="client_id" :required="true">
                            <select id="client_id" name="client_id" required class="form-select form-select-lg">
                                <option value="">— {{ __('Pilih klien') }} —</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>

                        <x-tabler.field :label="__('Apa yang ditagih *')" name="description" :required="true">
                            <textarea id="description" name="description" rows="2" required maxlength="500"
                                placeholder="Mis. Jasa design landing page" class="form-control form-control-lg"></textarea>
                        </x-tabler.field>

                        <div class="row g-2">
                            <div class="col-6">
                                <x-tabler.field :label="__('Total *')" name="amount" :required="true">
                                    <input type="number" id="amount" name="amount" step="0.01" min="0.01" required placeholder="500000" class="form-control form-control-lg fw-bold">
                                </x-tabler.field>
                            </div>
                            <div class="col-6">
                                <x-tabler.field :label="__('Jatuh tempo')" name="due_in_days">
                                    <select id="due_in_days" name="due_in_days" class="form-select form-select-lg">
                                        <option value="7">+7 {{ __('hari') }}</option>
                                        <option value="14" selected>+14 {{ __('hari') }}</option>
                                        <option value="30">+30 {{ __('hari') }}</option>
                                        <option value="60">+60 {{ __('hari') }}</option>
                                    </select>
                                </x-tabler.field>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg"><x-ti name="bolt" /> {{ __('Buat Invoice') }}</button>
                        <p class="form-hint text-center mt-2 mb-0">{{ __('Detail lainnya bisa di-edit setelah ini di halaman invoice.') }}</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-tabler.layout>
