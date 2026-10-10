<x-tabler.layout :title="__('Ticket Baru')" :pretitle="__('Support Tickets')">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">{{ __('Ticket Baru') }}</h3></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('support.store') }}">
                        @csrf
                        <x-tabler.field :label="__('Subject *')" name="subject" :required="true">
                            <input type="text" id="subject" name="subject" required maxlength="255" class="form-control">
                        </x-tabler.field>
                        <x-tabler.field :label="__('Priority *')" name="priority" :required="true">
                            <select id="priority" name="priority" class="form-select">
                                <option>low</option><option selected>normal</option><option>high</option><option>urgent</option>
                            </select>
                        </x-tabler.field>
                        <x-tabler.field :label="__('Pesan *')" name="message" :required="true">
                            <textarea id="message" name="message" rows="6" required placeholder="Jelaskan issue / pertanyaan kamu..." class="form-control"></textarea>
                        </x-tabler.field>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('support.index') }}" class="btn">{{ __('Cancel') }}</a>
                            <button class="btn btn-primary"><x-ti name="send" /> {{ __('Submit Ticket') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-tabler.layout>
