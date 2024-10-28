@props(['mobile' => false])

<a
    {{ $attributes->merge([
            'href' => route($route),
            'wire:navigate' => true,
        ])->class([
            'font-medium leading-tight',
            'text-primary-900 hover:bg-primary-50',
            $isCurrent() ? 'bg-primary-50' : '',
            $mobile ? 'flex px-2 py-3' : 'px-3 py-2 rounded',
        ]) }}>
    {{ $label }}
</a>
