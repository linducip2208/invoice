@php
    $items = [
        ['icon' => '🏠', 'label' => 'Home', 'route' => 'dashboard', 'active' => request()->routeIs('dashboard')],
        ['icon' => '📄', 'label' => 'Invoice', 'route' => 'invoices.index', 'active' => request()->routeIs('invoices.*')],
        ['icon' => '+', 'label' => 'Buat', 'route' => 'invoices.create', 'fab' => true],
        ['icon' => '👥', 'label' => 'Klien', 'route' => 'clients.index', 'active' => request()->routeIs('clients.*')],
        ['icon' => '👤', 'label' => 'Saya', 'route' => 'profile.edit', 'active' => request()->routeIs('profile.*')],
    ];
@endphp

<nav class="lg:hidden fixed bottom-0 inset-x-0 bg-white/90 backdrop-blur-md border-t border-purple-100 z-30"
     style="padding-bottom: env(safe-area-inset-bottom);">
    <div class="flex items-stretch justify-around h-16">
        @foreach($items as $item)
            <a href="{{ route($item['route']) }}"
               @class([
                   'flex flex-col items-center justify-center flex-1 transition',
                   '!flex-grow-0 -mt-5 mx-1' => isset($item['fab']),
                   'text-pink-500' => $item['active'] ?? false,
                   'text-purple-400' => ! ($item['active'] ?? false) && ! isset($item['fab']),
               ])>
                @if(isset($item['fab']))
                    <span class="w-14 h-14 rounded-full flex items-center justify-center text-2xl text-white font-bold shadow-unicorn-pink"
                          style="background: linear-gradient(135deg, #FF7AC6, #A78BFA);">
                        {{ $item['icon'] }}
                    </span>
                @else
                    <span class="text-xl mb-0.5">{{ $item['icon'] }}</span>
                    <span class="text-[10px] font-bold">{{ $item['label'] }}</span>
                @endif
            </a>
        @endforeach
    </div>
</nav>

{{-- Spacer to prevent content overlap --}}
<div class="lg:hidden h-16" style="height: calc(4rem + env(safe-area-inset-bottom));"></div>
