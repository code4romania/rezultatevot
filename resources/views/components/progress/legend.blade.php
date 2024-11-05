@php

@endphp

<li {{ $attributes->merge([
    'class' => 'flex items-center gap-2',
    'style' => "--color-custom: {$color};",
]) }}>
    <span class="block w-4 h-4 bg-custom"></span>
    <span>{{ $text }}</span>
</li>
