@props(['align' => 'center'])

<th
    {{ $attributes->class([
        'px-3 py-3.5 text-sm font-semibold text-gray-900',
        match ($align) {
            'left' => 'text-left',
            'right' => 'text-right',
            default => 'text-center',
        },
    ]) }}>
    {{ $slot }}
</th>
