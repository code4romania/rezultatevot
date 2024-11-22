<div class="grid gap-8">
    <x-election.title
        :title="__('app.navigation.results')"
        :level="$level"
        :country="$country"
        :county="$county"
        :locality="$locality" />

    @if (filled($this->aggregate))
        <x-stacked-bar
            :show-threshold="data_get($election, 'properties.show_threshold', false)"
            :items="$this->aggregate"
            :maxItems="4" />
    @endif

    <livewire:map
        :key="$this->mapKey()"
        :country="$country"
        :county="$county"
        :level="$level"
        :data="$this->data->toArray()"
        embed />

    @if (filled($this->aggregate))
        <x-candidates.results-table :items="$this->aggregate" />
    @endif

    <x-stats.records :stats="$this->recordStats" />
</div>
