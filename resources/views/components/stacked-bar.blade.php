@php
    $cursor = 0;
@endphp

<div class="grid gap-2">
    <svg preserveAspectRatio="none" viewBox="0 0 100 80" class="w-full h-20">
        @foreach ($items as $item)
            <rect
                x="{{ $cursor }}"
                width="{{ $item['percent'] }}"
                y="8"
                height="64"
                fill="rgb({{ $item['color'] }})" />

            @php
                $cursor += $item['percent'];
            @endphp
        @endforeach

        @if ($showThreshold)
            <line x1="50" x2="50" y1="0" y2="80" vector-effect="non-scaling-stroke"
                strokeWidth="1.5"
                stroke="#443F46" />
        @endif
    </svg>

    <ul class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($items as $item)
            <x-legend
                :image="data_get($item, 'image')"
                :label="$item['name']"
                :description="sprintf('%s (%s)', Number::percentage($item['percent'], 2), Number::format($item['votes']))"
                :color="$item['color']" />
        @endforeach
    </ul>
</div>
