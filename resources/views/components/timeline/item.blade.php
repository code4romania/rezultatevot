@props([
    'url' => null,
    'label' => null,
    'isActive' => false,
    'isLive' => false,
    'election' => null,
])

@php
    $url = $election->is_visible ? $url : "https://istoric.rezultatevot.ro/elections/{$election->old_id}/results";
@endphp

<li>
    <a
        href="{{ $url }}"
        @if ($election->is_visible) wire:navigate
        @else target="_blank" rel="noopener noreferrer" @endif
        class="flex gap-1">
        <span @class(['font-semibold' => $isActive])>{{ $label }}</span>

        @if ($isLive)
            <span class="inline-flex items-center gap-x-1.5 rounded-md bg-red-600 px-1.5 py-0.5">
                <x-icon-dot class="h-1.5 w-1.5 fill-white animate-pulse" />

                <span class="text-xs font-bold text-red-50">LIVE</span>
            </span>
        @endif
    </a>

</li>
