<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Portal Users') }} <span class="text-2xl">🪟</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ Akses portal untuk klien <strong>{{ $client->name }}</strong></p>
            </div>
            <a href="{{ route('clients.show', $client) }}" class="btn-unicorn-secondary text-sm">← {{ __('Kembali ke Klien') }}</a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl bg-rose-50 border-2 border-rose-200 p-4 text-rose-800">⚠️ {{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('clients.portal-users.invite', $client) }}" class="card-unicorn !p-6 flex items-end gap-3">
            @csrf
            <div class="flex-1">
                <x-input-label :value="__('Email akses portal')" />
                <x-text-input name="email" type="email" required class="mt-1 w-full" placeholder="staff@klien.com" />
            </div>
            <button type="submit" class="btn-unicorn">+ {{ __('Buat Akses') }}</button>
        </form>

        <div class="card-unicorn !p-0 overflow-hidden">
            <div class="p-5 border-b border-purple-100">
                <h3 class="font-display font-bold text-purple-900">Daftar Akses ({{ $portalUsers->count() }})</h3>
                <p class="text-xs text-purple-500 mt-1">URL portal: <code class="bg-purple-50 px-1.5 rounded">{{ route('portal.login') }}</code></p>
            </div>
            @if($portalUsers->isEmpty())
                <p class="p-8 text-center text-purple-400 text-sm">Belum ada akses portal untuk klien ini.</p>
            @else
                <div class="divide-y divide-purple-100">
                    @foreach($portalUsers as $u)
                        <div class="p-4 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-purple-900">{{ $u->email }}</p>
                                <p class="text-xs text-purple-500">
                                    @if($u->last_login_at)Login terakhir: {{ $u->last_login_at->diffForHumans() }}
                                    @else<span class="text-amber-600">Belum pernah login</span>@endif
                                </p>
                            </div>
                            <form method="POST" action="{{ route('portal-users.destroy', $u) }}" onsubmit="return confirm('Hapus akses {{ $u->email }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-500 text-sm font-semibold hover:underline">Hapus</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card-unicorn !p-5 bg-amber-50/60 border-amber-200">
            <p class="text-xs text-amber-800">
                💡 <strong>Cara kerja:</strong> Klien akan login di <code>/portal/login</code> dengan email & password sementara yang di-generate saat invite. Mereka bisa lihat semua invoice & quote miliknya. Tidak bisa edit, tidak bisa lihat klien lain.
            </p>
        </div>
    </div>
</x-app-layout>
