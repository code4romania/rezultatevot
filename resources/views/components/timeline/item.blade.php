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
        class="flex w-full gap-1">
        <span @class(['flex-1', 'font-semibold' => $isActive])>{{ $label }}</span>

        @if ($isLive)
            <x-timeline.live />
        @endif
    </a>

</li>
