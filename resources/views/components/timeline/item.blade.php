@props([
    'url' => null,
    'label' => null,
    'isActive' => false,
    'isLive' => false,
])

<li>
    <a
        href="{{ $url }}"
        class="flex gap-1"
        wire:navigate>
        <span @class(['font-semibold' => $isActive])>{{ $label }}</span>

        @if ($isLive)
            <span class="inline-flex items-center gap-x-1.5 rounded-md bg-red-600 px-1.5 py-0.5">
                <x-icon-dot class="h-1.5 w-1.5 fill-white animate-pulse" />

                <span class="text-xs font-bold text-red-50">LIVE</span>
            </span>
        @endif
    </a>

</li>
