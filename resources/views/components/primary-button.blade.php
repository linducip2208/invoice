<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-unicorn']) }}>
    {{ $slot }}
</button>
