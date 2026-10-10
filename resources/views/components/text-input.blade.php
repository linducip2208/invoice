@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'input-unicorn rounded-xl px-4 py-2 w-full text-sm text-purple-900 placeholder-purple-300']) }}>
