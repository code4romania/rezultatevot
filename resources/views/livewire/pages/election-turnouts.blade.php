@use('App\Livewire\Charts')
@use('Filament\Support\Colors\Color')

<div class="grid gap-8">
    <x-election.header :election="$election" page="turnout" />

    {{ $this->form }}

    <x-election.tabs
        :parameters="$this->getQueryParameters()"
        :election="$election"
        page="turnout" />

    <section class="contents">
        <x-election.title
            :title="__('app.navigation.turnout')"
            :embed-url="$this->getEmbedUrl()"
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
            :key="$this->mapKey() . '-map'"
            :country="$country"
            :county="$county"
            :level="$level"
            :data="$this->data->toArray()"
            :legend="$this->getLegend()" />

        <div class="grid items-stretch gap-8 xl:grid-cols-3">
            <livewire:charts.turnout-area-chart
                :key="$this->mapKey() . '-area'"
                :parameters="$this->getQueryParameters()"
                :election="$election"
                :areas="$this->areas" />

            <livewire:charts.turnout-population-pyramid-chart
                :key="$this->mapKey() . '-population'"
                :parameters="$this->getQueryParameters()"
                :election="$election"
                :demographics="$this->demographics" />
        </div>

        <livewire:vote-monitor-stats
            :election="$election"
            show-embed />

        <x-candidates.turnouts-table :items="$this->candidates" />

        <livewire:news-feed :election="$election" />
    </section>
</div>
