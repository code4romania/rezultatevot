<li
    {{ $attributes->merge(['style' => "--color-custom: {$color};"])->class(['flex gap-2', 'items-center flex-wrap' => blank($image), 'items-start' => filled($image)]) }}>
    @if ($image)
        <img src="{{ $image }}" alt="{{ $label }}" class="w-12 h-12" />

        <div class="overflow-hidden">
            <div class="flex items-center gap-2">
                <span class="block w-4 h-4 bg-custom shrink-0"></span>
                <span class="truncate">{{ $label }}</span>
            </div>
            <span>{{ $description }}</span>
        </div>
    @else
        <span class="block w-4 h-4 bg-custom shrink-0"></span>

        <span class="inline">{{ $label }}</span>
        <span class="inline">{{ $description }}</span>
    @endif
</li>
