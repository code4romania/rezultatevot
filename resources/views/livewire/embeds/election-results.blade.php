<div class="grid gap-8">
    <x-election.title
        :title="__('app.navigation.results')"
        :level="$level"
        :country="$country"
        :county="$county"
        :locality="$locality" />

    <x-last-updated-at :$election page="results" class="-mt-8" />

    @if (filled($this->aggregate))
        <x-stacked-bar
            :show-threshold="data_get($election, 'properties.show_threshold', false)"
            :items="$this->aggregate"
            :maxItems="4" />
    @endif

    <livewire:map
        :key="$this->componentKey('map', level: $level, county: $county)"
        :country="$country"
        :county="$county"
        :level="$level"
        :data="$this->data->toArray()"
        embed />

    <x-seats-chart :election="$election" :level="$level" :votables="$this->aggregate" />

    <x-results-table :election="$election" :level="$level" :votables="$this->aggregate" />

    <x-stats.records :stats="$this->recordStats" />
</div>
