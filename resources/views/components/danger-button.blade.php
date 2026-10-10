<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-unicorn-danger']) }}>
    {{ $slot }}
</button>
