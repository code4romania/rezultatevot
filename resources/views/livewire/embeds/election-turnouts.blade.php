@use('Filament\Support\Colors\Color')

<div class="grid gap-8">
    <x-election.title
        :title="__('app.navigation.turnout')"
        :level="$level"
        :country="$country"
        :county="$county"
        :locality="$locality" />

    <x-election.alert :$election class="-mt-4" />

    <x-last-updated-at :$election page="turnout" class="-mt-4" />

    @if (filled($this->aggregate))
        @if ($this->aggregate->max)
            <x-turnout-bar :value="$this->aggregate->value" :max="$this->aggregate->max" />
        @else
            <x-turnout-card :value="$this->aggregate->value" />
        @endif
    @endif

    <livewire:map
        :key="$this->componentKey('map', level: $level, county: $county)"
        :country="$country"
        :county="$county"
        :level="$level"
        :total-value="percent($this->aggregate?->value, $this->aggregate?->max)"
        :total-value-formatted="percent($this->aggregate?->value, $this->aggregate?->max, formatted: true)"
        :total-label="__('app.navigation.turnout')"
        :data="$this->data->toArray()"
        :legend="$this->getLegend()"
        embed />
</div>
