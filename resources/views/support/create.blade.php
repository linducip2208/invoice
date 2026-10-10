<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">💬 {{ __('Ticket Baru') }}</h2></x-slot>
    <div class="max-w-2xl mx-auto card-unicorn">
        <form method="POST" action="{{ route('support.store') }}" class="space-y-4">
            @csrf
            <div><label class="text-sm font-bold">Subject *</label><input type="text" name="subject" required maxlength="255" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></div>
            <div><label class="text-sm font-bold">Priority *</label>
                <select name="priority" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full">
                    <option>low</option><option selected>normal</option><option>high</option><option>urgent</option>
                </select>
            </div>
            <div><label class="text-sm font-bold">Pesan *</label><textarea name="message" rows="6" required class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full" placeholder="Jelaskan issue / pertanyaan kamu..."></textarea></div>
            <div class="flex justify-end gap-3"><a href="{{ route('support.index') }}" class="btn-unicorn-secondary">Cancel</a><button class="btn-unicorn">Submit Ticket</button></div>
        </form>
    </div>
</x-app-layout>
