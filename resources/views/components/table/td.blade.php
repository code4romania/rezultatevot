@props(['align' => 'center'])

<td
    {{ $attributes->class([
        'px-3 py-4 text-sm font-semibold text-gray-500 whitespace-nowrap',
        match ($align) {
            'left' => 'text-left',
            'right' => 'text-right',
            default => 'text-center',
        },
    ]) }}>
    {{ $slot }}
</td>
