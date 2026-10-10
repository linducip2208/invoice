@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center px-3 pt-1 border-b-2 text-sm font-semibold leading-5 text-purple-700 border-pink-400 transition'
    : 'inline-flex items-center px-3 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-purple-500 hover:text-purple-700 hover:border-purple-200 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
