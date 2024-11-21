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
            :level="$level" />

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
            :data="$this->data->toArray()" />

        <x-stats.records :stats="$this->recordStats" />

        @if (filled($this->aggregate))
            <x-candidates-table :items="$this->aggregate" />
        @endif

        <livewire:news-feed :election="$election" />

    </section>
</div>
