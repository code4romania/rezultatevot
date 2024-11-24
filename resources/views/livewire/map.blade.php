<div class="relative">
    <div
        class="block h-[600px] outline-none z-[1]"
        data-url="{{ Vite::asset("resources/geojson/{$this->file}.geojson") }}"
        x-data="map"
        x-resize="resize"
        x-cloak></div>

    @if ($this->showOverlay)
        <div @class([
            'absolute inset-0 z-10 flex flex-col items-center justify-center leading-0',
            '@container',
            'text-purple-950' => filled($legend) && $this->totalValue < 50,
            'text-purple-50' => filled($legend) && $this->totalValue >= 50,
        ])>
            <div class="text-3xl font-bold @md:text-5xl @xl:text-6xl">
                {{ $this->totalValueFormatted ?? $this->totalValue }}
            </div>
            <div class="text-sm @sm:text-base @md:text-lg @lg:text-xl">
                {{ $this->totalLabel }}
            </div>
        </div>
    @endif
</div>
