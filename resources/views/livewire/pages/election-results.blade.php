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
                :s how-threshold="data_get($election, 'properties.show_threshold', false)"
                :items="$this->aggregate"
                :maxItems="4" />
        @endif

        <livewire:map
            :key="$this->mapKey()"
            :country="$country"
            :county="$county"
            :level="$level"
            :data="$this->data->toArray()" />

        @if (filled($this->aggregate))
            <x-candidates-table :items="$this->aggregate" />
        @endif
    </section>
</div>
