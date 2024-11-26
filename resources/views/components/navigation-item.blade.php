@props([
    'mobile' => false,
    'primary' => false,
])

<a
    {{ $attributes->merge([
            'href' => $item->url,
            'wire:navigate' => $item->target === '_self',
        ])->class([
            ' leading-tight',
            $primary ? 'font-medium text-primary-900 hover:bg-primary-50' : 'text-gray-600 hover:text-gray-900',
            // $isCurrent() ? 'bg-primary-50' : '',
            $primary ? ($mobile ? 'flex px-2 py-3' : 'px-3 py-2 rounded') : '',
        ]) }}>
    {{ $item->title }}
</a>
