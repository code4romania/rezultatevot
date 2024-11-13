@props(['hiddenByDefault' => false])

<tr
    {{ $attributes->merge([
        'class' => 'even:bg-gray-50',

        'x-show="expanded"' => $hiddenByDefault,
        'x-cloak' => $hiddenByDefault,
    ]) }}>
    {{ $slot }}
</tr>
