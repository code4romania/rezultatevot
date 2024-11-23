@use('Filament\Support\Colors\Color')

<div class="grid gap-8">
    <x-election.title
        :title="__('app.navigation.turnout')"
        :level="$level"
        :country="$country"
        :county="$county"
        :locality="$locality" />

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
        :data="$this->data->toArray()"
        embed />
</div>
