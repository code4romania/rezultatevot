<div class="grid gap-8">
    <x-election.header :election="$election" page="results" />

    {{ $this->form }}

    <x-election.tabs
        :parameters="$this->getQueryParameters()"
        :election="$election"
        page="results" />

    <section class="contents">
        <x-election.title
            :title="__('app.navigation.results')"
            :embed-url="$this->getEmbedUrl()"
            :level="$level"
            :country="$country"
            :county="$county"
            :locality="$locality" />

        @if ($this->recordStats->isEmpty())
            <x-alert class="bg-yellow-50">
                Aici vor fi afișate rezultatele alegerilor pe măsură ce ele sunt publicate.
            </x-alert>
        @endif

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
            :data="$this->data->toArray()" />

        @if ($this->recordStats->isNotEmpty())
            <x-stats.records :stats="$this->recordStats" />
        @endif

        <x-seats-chart :election="$election" :level="$level" :votables="$this->aggregate" />

        <x-results-table :election="$election" :level="$level" :votables="$this->aggregate" />

        <livewire:news-feed :election="$election" />

    </section>
</div>
